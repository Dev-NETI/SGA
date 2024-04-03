<?php

namespace App\Livewire\Reports\SGA;

use Carbon\Carbon;
use App\Models\Vessel;
use Livewire\Component;
use App\Traits\FpdiTrait;
use App\Models\Vessel_type;
use Illuminate\Support\Facades\Session;

class TrainingFeeComponent extends Component
{
    use FpdiTrait;

    public function generate()
    {
        // data
        $principalId = Session::get('principalId');
        $month = Session::get('month');
        $vesselTypeId = Session::get('vesselTypeId');
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

        foreach ($vesselData as $data) {
            $this->trainingFee($pdf, $importedPage, $data, $currentDate, $formattedMonth, $subtractMonth);
        }

        $pdf->Output();
    }


    
    public function trainingFee($pdf, $importedPage, $data, $currentDate, $formattedMonth, $subtractMonth)
    {
        $pdf->AddPage('P');
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
    }

}