<?php

namespace App;

use App\Mail\SendFc007Email;
use App\Models\Fc007Log;
use App\Models\Fc007ReportEmailRecipient;
use Carbon\Carbon;
use App\Models\Vessel;
use App\Traits\FpdiTrait;
use App\Traits\QueryTrait;
use App\Models\Vessel_type;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

trait FC007Trait
{
    use FpdiTrait;
    use QueryTrait;
    use UtilitiesTrait;
    use EmailManagementTrait;

    public function generateFC007($sessionPrincipalId, $sessionMonth, $sessionVesselTypeId, $output = true, $referenceNumber = null)
    {
        // data
        $principalId = $sessionPrincipalId;
        $month = $sessionMonth;
        $vesselTypeId = $sessionVesselTypeId;
        $vesselTypeData = Vessel_type::find($vesselTypeId);
        $vesselData = Vessel::where('principal_id', $principalId)
            ->where('vessel_type_id', $vesselTypeId)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
        $formattedMonth = Carbon::createFromFormat("Y-m", $month)->format('F Y');
        $subtractMonth = Carbon::createFromFormat("Y-m", $month)->subMonth()->format('F Y');
        $currentDate = Carbon::now()->format('Y F d');
        // data end

        // initiate fpdi
        $pdf = $this->initiateFpdi('NETI-SGA', 'NETI-SGA', $vesselTypeData->name . " - " . $formattedMonth . " TRAINING FEE.pdf");
        $templatePath = storage_path('app/public/SGA/Training Fee Template.pdf');
        $template = $pdf->setSourceFile($templatePath);
        $importedPage = $pdf->importPage($template);
        $pageWidth = 210;
        $pageHeight = 297;

        foreach ($vesselData as $data) {
            $this->trainingFee($pdf, $importedPage, $data, $currentDate, $formattedMonth, $subtractMonth, $pageWidth, $pageHeight);
        }

        //signature 
        $this->getSignature($pdf, Auth::user()->signature_path, 42, 269, 12, 12);

        if ($output) {
            $pdf->Output();
        } else {
            // save to folder
            $fileName = $referenceNumber . '.pdf';
            $filePath = storage_path('app/public/F-FC-007/' . $fileName);
            $pdfContents = $pdf->Output('', 'S');
            $storeFile = file_put_contents($filePath, $pdfContents);

            if (!$storeFile) {
                session()->flash('error', 'Saving file failed!');
            } else {
                // save to database
                $query = Fc007Log::create([
                    'reference_number' => $referenceNumber,
                    'file_path' => $fileName,
                    'status_id' => 2,
                    'generated_by' => Auth::user()->full_name,
                    'principal_id' => $principalId,
                ]);
                $errorMsg = "Saving summary report failed!";
                $successMsg = "Summary report saved successfully!";
                $this->storeTrait($query, $errorMsg, $successMsg);

                // update serial number
                foreach ($vesselData as $data) {
                    $this->updateSerialNumber($data->id, $data->incremented_serial_number, $errorMsg, $successMsg);
                }

                // send email notification
                $emailData = $this->sendEmailNotification(2,NULL,1);
                $subject = $this->fcEmailSubject(2);
                foreach ($emailData as $name => $email) {
                    Mail::to($email)
                        ->cc('sherwin.roxas@neti.com.ph')
                        ->send(new SendFc007Email($referenceNumber, $subject, $name));
                }

                session()->flash('success', "F-FC-007 report successfully sent for verification!");
                return $this->redirectRoute('dashboard.fc007');
            }
        }
    }

    public function updateSerialNumber($id, $newSerialNumber, $errorMsg, $successMsg)
    {
        $vessel = Vessel::find($id);
        $update = $vessel->update([
            'serial_number' => $newSerialNumber,
        ]);

        $this->updateTraitNoRoute($vessel, $update, $errorMsg, $successMsg);
    }

    public function trainingFee($pdf, $importedPage, $data, $currentDate, $formattedMonth, $subtractMonth, $pageWidth, $pageHeight)
    {
        $pdf->AddPage('P', [$pageWidth, $pageHeight]);
        $pdf->useTemplate($importedPage);
        // Set font
        $pdf->SetFont('Helvetica', 'B', 9);

        // serial number
        $pdf->setXY(174, 19);
        $pdf->Cell(20.5, 5, $data->training_fee_serial_number, 0, 0, "C");
        // name of vessel
        $pdf->setXY(92, 54.8);
        $pdf->Cell(25, 5, $data->formatted_name_with_code, 0, 0, "C");
        // current date
        $pdf->setXY(165, 54.8);
        $pdf->Cell(25, 5, $currentDate, 0, 0, "C");
        // for the period
        $pdf->setXY(92, 62);
        $pdf->Cell(20.5, 5, $formattedMonth, 0, 0, "C");

        // particulars
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->setXY(40.5, 74);
        $pdf->Cell(20.5, 5, "Training Fee - for the month of " . $formattedMonth, 0, 0, "L");
        $pdf->setXY(40.5, 86);
        $pdf->Cell(20.5, 5, "Add: " . $subtractMonth . " per SGA " . $data->subtracted_serialNumber, 0, 0, "L");
        $pdf->setXY(40.5, 99);
        $pdf->Cell(20.5, 5, "Less: Remittance received for " . $subtractMonth . " SGA Fee", 0, 0, "L");

        // price
        $trainingFee = $data->training_fee;
        $addedFee = $trainingFee * 2;
        $lessedFee = $addedFee - $trainingFee;
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->setXY(180, 74);
        $pdf->Cell(20.5, 5, number_format($trainingFee, 2), 0, 0, "L");

        $pdf->setXY(180, 79);
        $pdf->Cell(20.5, 5, number_format($trainingFee, 2), 0, 0, "L");

        $pdf->SetFont('Helvetica', '', 9);
        $pdf->setXY(180, 86);
        $pdf->Cell(20.5, 5, number_format($trainingFee, 2), 0, 0, "L");

        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->setXY(180, 92.3);
        $pdf->Cell(20.5, 5, number_format($addedFee, 2), 0, 0, "L");

        $pdf->SetFont('Helvetica', '', 9);
        $pdf->setXY(180, 99);
        $pdf->Cell(20.5, 5, number_format($trainingFee, 2), 0, 0, "L");

        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->setXY(180, 105);
        $pdf->Cell(20.5, 5, number_format($lessedFee, 2), 0, 0, "L");

        $this->getSignature($pdf, Auth::user()->signature_path, 42, 270, 12, 12);
    }
}
