<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$report = DB::table('daily_reports')->whereDate('date', '2026-01-16')->first();

if ($report) {
    echo "Report ID: {$report->id}\n";
    echo "Location ID: {$report->location_id}\n";
    echo "Date: {$report->date}\n";

    $fiscalCount = DB::table('fiscal_items')->where('report_id', $report->id)->count();
    $nonFiscalCount = DB::table('non_fiscal_items')->where('report_id', $report->id)->count();
    $cardCount = DB::table('card_payments')->where('report_id', $report->id)->count();
    $wireCount = DB::table('wire_transfers')->where('report_id', $report->id)->count();
    $patientCount = DB::table('patients')->where('report_id', $report->id)->count();

    echo "Fiscal items: {$fiscalCount}\n";
    echo "Non-fiscal items: {$nonFiscalCount}\n";
    echo "Card payments: {$cardCount}\n";
    echo "Wire transfers: {$wireCount}\n";
    echo "Patients: {$patientCount}\n";
} else {
    echo "No report found\n";
}
