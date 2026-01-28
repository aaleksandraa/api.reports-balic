<?php

namespace App\Services;

use App\Models\DailyReport;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function generateReportPdf(DailyReport $report)
    {
        $data = $this->prepareReportData($report);

        $pdf = Pdf::loadView('pdf.report', $data);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    private function prepareReportData(DailyReport $report)
    {
        // Calculate totals
        $totalFiscal = $report->fiscalItems->sum('price');
        $totalNonFiscal = $report->nonFiscalItems->sum('price');
        $totalCardPayments = $report->cardPayments->sum('price');
        $totalWireTransfers = $report->wireTransfers->sum('price');
        $totalAssociates = $report->associates->sum('price');
        $grandTotal = $totalFiscal + $totalNonFiscal + $totalCardPayments + $totalWireTransfers;

        return [
            'report' => $report,
            'location' => $report->location,
            'fiscalItems' => $report->fiscalItems,
            'nonFiscalItems' => $report->nonFiscalItems,
            'cardPayments' => $report->cardPayments,
            'wireTransfers' => $report->wireTransfers,
            'associates' => $report->associates,
            'patients' => $report->patients,
            'workSchedule' => $report->workSchedule,
            'unpaidExams' => $report->unpaidExams,
            'todayPatientsQuick' => $report->todayPatientsQuick,
            'todayPatientsDetailed' => $report->todayPatientsDetailed,
            'todayPatientsTotal' => $this->calculateTodayPatientsTotal($report),
            'plannedProcedures' => $report->plannedProcedures,
            'totals' => [
                'totalFiscal' => $totalFiscal,
                'totalNonFiscal' => $totalNonFiscal,
                'totalCardPayments' => $totalCardPayments,
                'totalWireTransfers' => $totalWireTransfers,
                'totalAssociates' => $totalAssociates,
                'grandTotal' => $grandTotal,
            ],
        ];
    }

    private function calculateTodayPatientsTotal(DailyReport $report)
    {
        $quickTotal = $report->todayPatientsQuick->sum('count');
        $detailedTotal = $report->todayPatientsDetailed->count();
        return $quickTotal + $detailedTotal;
    }
}
