<?php

namespace App\Services;

use App\Models\DailyReport;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReportSubmitted;

class EmailService
{
    public function sendReportSubmitted(DailyReport $report)
    {
        $report->load(['location', 'submittedBy']);

        if ($report->submittedBy && $report->submittedBy->email) {
            Mail::to($report->submittedBy->email)
                ->send(new ReportSubmitted($report));
        }
    }

    public function sendReport(DailyReport $report, string $email)
    {
        $report->load([
            'location',
            'submittedBy',
            'fiscalItems',
            'nonFiscalItems',
            'cardPayments',
            'wireTransfers',
            'associates.doctor',
            'patients.doctor',
            'workSchedule'
        ]);

        Mail::to($email)->send(new ReportSubmitted($report));
    }

    public function sendReportReminder($email, $locationName, $date)
    {
        // TODO: Implement reminder email
    }

    public function sendWelcomeEmail($email, $firstName, $tempPassword)
    {
        // TODO: Implement welcome email
    }
}
