<?php

namespace App;

use App\Models\User;
use App\Models\Fc007Log;
use App\Models\Fc007ReportEmailRecipient;
use App\Models\SummaryReportEmailRecipient;

trait EmailManagementTrait
{
    public function pageTitle($value)
    {
        switch ($value) {
            case '1':
                $title = "Generate Board";
                break;
            case '2':
                $title = "Verification Board";
                break;
            case '3':
                $title = "Approval Board";
                break;
            case '4':
                $title = "Principal Board";
                break;
            case '5':
                $title = "O.R. Board";
                break;
            default:
                $title = "";
                break;
        }

        return $title;
    }

    public function fcEmailSubject($newStatusId)
    {
        switch ($newStatusId) {
            case '1':
                $subject = "Generate Process";
                break;
            case '2':
                $subject = "Verification Process";
                break;
            case '3':
                $subject = "Approval Process";
                break;
            case '4':
                $subject = "Principal Process";
                break;
            case '5':
                $subject = "Upload O.R. Process";
                break;
            case '6':
                $subject = "O.R ready for viewing";
                break;
            default:
                $subject = "";
                break;
        }

        return $subject;
    }

    public function SummaryEmailSubject($currentStatusId)
    {
        switch ($currentStatusId) {
            case '1':
                $subject = "Letter and Summary Report for Verification";
                break;
            case '2':
                $subject = "Letter and Summary Report for Approval";
                break;
            case '3':
                $subject = "Letter and Summary Report sent to Principal";
                break;
            default:
                $subject = "Letter and Summary Report received by Principal";
                break;
        }

        return $subject;
    }

    public function sendEmailNotification($newProcessId, $principalId = null, $currentProcessId)
    {
        if ($currentProcessId == 1 || $currentProcessId == 2 || $currentProcessId == 4) {
            $emailData = Fc007ReportEmailRecipient::where('process_id', $newProcessId)
                ->where('is_active', 1)
                ->with('user:id,email,f_name')
                ->get()
                ->pluck('user.email', 'user.f_name');
        } else {
            $emailData = User::whereHas('company', function ($query) use ($principalId) {
                $query->whereHas('fclog', function ($query) use ($principalId) {
                    $query->where('principal_id', $principalId);
                });
            })
                ->get()
                ->pluck('email');
        }
        return $emailData;
    }

    public function getSummaryReportRecipient($currentProcessId, $principalId = null)
    {
        $newStatusId = $currentProcessId + 1;
        if ($currentProcessId == 1 || $currentProcessId == 2 || $currentProcessId == 4) {
            $emailData = SummaryReportEmailRecipient::where('process_id', $newStatusId)
                ->where('is_active', 1)
                ->with('user:id,email,f_name')
                ->get()
                ->pluck('user.email', 'user.f_name');
        } else {
            $emailData = User::whereHas('company', function ($query) use ($principalId) {
                $query->whereHas('summary_log', function ($query) use ($principalId) {
                    $query->where('principal_id', $principalId);
                });
            })
                ->get()
                ->pluck('email');
        }

        return $emailData;
    }
}
