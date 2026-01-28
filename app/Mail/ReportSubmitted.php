<?php

namespace App\Mail;

use App\Models\DailyReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $report;

    public function __construct(DailyReport $report)
    {
        $this->report = $report;
    }

    public function build()
    {
        return $this->subject('Izvještaj predan - ' . $this->report->location->name . ' - ' . $this->report->date)
                    ->view('emails.report-submitted');
    }
}
