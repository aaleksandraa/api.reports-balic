<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Services\PdfService;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    protected $pdfService;
    protected $emailService;

    public function __construct(PdfService $pdfService, EmailService $emailService)
    {
        $this->pdfService = $pdfService;
        $this->emailService = $emailService;
    }

    public function index(Request $request)
    {
        $query = DailyReport::with(['location', 'submittedBy']);

        if ($request->has('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        $reports = $query->orderBy('date', 'desc')->get();

        return response()->json($reports);
    }

    public function byLocation($locationId, Request $request)
    {
        $query = DailyReport::where('location_id', $locationId)
            ->with([
                'location',
                'submittedBy',
                'fiscalItems',
                'nonFiscalItems',
                'cardPayments',
                'wireTransfers',
                'associates.doctor',
                'patients.doctor',
                'workSchedule',
                'unpaidExams.doctor',
                'todayPatientsQuick.service',
                'todayPatientsDetailed.service',
                'plannedProcedures'
            ]);

        if ($request->has('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->orderBy('date', 'desc')->get();

        return response()->json($reports);
    }

    public function show($id)
    {
        $report = DailyReport::with([
            'location',
            'submittedBy',
            'fiscalItems',
            'nonFiscalItems',
            'cardPayments',
            'wireTransfers',
            'associates.doctor',
            'patients.doctor',
            'workSchedule',
            'unpaidExams.doctor',
            'todayPatientsQuick.service',
            'todayPatientsDetailed.service',
            'plannedProcedures'
        ])->findOrFail($id);

        return response()->json($report);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_id' => 'required|exists:locations,id',
            'date' => 'required|date',
            'day_of_week' => 'required|string',
            'notes' => 'nullable|string',
            'fiscal_items' => 'array',
            'non_fiscal_items' => 'array',
            'card_payments' => 'array',
            'wire_transfers' => 'array',
            'associates' => 'array',
            'patients' => 'array',
            'work_schedule' => 'array',
            'unpaid_exams' => 'array',
            'today_patients_quick' => 'array',
            'today_patients_detailed' => 'array',
            'planned_procedures' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Check if report exists
            $report = DailyReport::where('location_id', $request->location_id)
                ->where('date', $request->date)
                ->first();

            if ($report) {
                // Update existing
                $report->update([
                    'day_of_week' => $request->day_of_week,
                    'notes' => $request->notes,
                ]);

                // Delete existing items
                $report->fiscalItems()->delete();
                $report->nonFiscalItems()->delete();
                $report->cardPayments()->delete();
                $report->wireTransfers()->delete();
                $report->associates()->delete();
                $report->patients()->delete();
                $report->workSchedule()->delete();
                $report->unpaidExams()->delete();
                $report->todayPatientsQuick()->delete();
                $report->todayPatientsDetailed()->delete();
                $report->plannedProcedures()->delete();
            } else {
                // Create new
                $report = DailyReport::create([
                    'location_id' => $request->location_id,
                    'date' => $request->date,
                    'day_of_week' => $request->day_of_week,
                    'notes' => $request->notes,
                    'submitted_by' => auth()->id(),
                ]);
            }

            // Create items
            if ($request->has('fiscal_items')) {
                foreach ($request->fiscal_items as $item) {
                    $report->fiscalItems()->create($item);
                }
            }

            if ($request->has('non_fiscal_items')) {
                foreach ($request->non_fiscal_items as $item) {
                    $report->nonFiscalItems()->create($item);
                }
            }

            if ($request->has('card_payments')) {
                foreach ($request->card_payments as $item) {
                    $report->cardPayments()->create($item);
                }
            }

            if ($request->has('wire_transfers')) {
                foreach ($request->wire_transfers as $item) {
                    $report->wireTransfers()->create($item);
                }
            }

            if ($request->has('associates')) {
                foreach ($request->associates as $item) {
                    $report->associates()->create($item);
                }
            }

            if ($request->has('patients')) {
                foreach ($request->patients as $item) {
                    $report->patients()->create($item);
                }
            }

            if ($request->has('work_schedule')) {
                foreach ($request->work_schedule as $item) {
                    $report->workSchedule()->create($item);
                }
            }

            if ($request->has('unpaid_exams')) {
                foreach ($request->unpaid_exams as $item) {
                    $report->unpaidExams()->create($item);
                }
            }

            if ($request->has('today_patients_quick')) {
                foreach ($request->today_patients_quick as $item) {
                    $report->todayPatientsQuick()->create($item);
                }
            }

            if ($request->has('today_patients_detailed')) {
                foreach ($request->today_patients_detailed as $item) {
                    $report->todayPatientsDetailed()->create($item);
                }
            }

            if ($request->has('planned_procedures')) {
                foreach ($request->planned_procedures as $item) {
                    $report->plannedProcedures()->create($item);
                }
            }

            DB::commit();

            return response()->json([
                'id' => $report->id,
                'message' => 'Report saved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to save report: ' . $e->getMessage()], 500);
        }
    }

    public function submit($id)
    {
        $report = DailyReport::with(['location', 'submittedBy'])->findOrFail($id);

        $report->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'submitted_by' => auth()->id(),
        ]);

        // Send email notification
        try {
            $this->emailService->sendReportSubmitted($report);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send email: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Report submitted successfully']);
    }

    public function downloadPdf($id)
    {
        $report = DailyReport::with([
            'location',
            'fiscalItems',
            'nonFiscalItems',
            'cardPayments',
            'wireTransfers',
            'associates.doctor',
            'patients.doctor',
            'workSchedule',
            'unpaidExams.doctor',
            'todayPatientsQuick.service',
            'todayPatientsDetailed.service',
            'plannedProcedures'
        ])->findOrFail($id);

        $pdf = $this->pdfService->generateReportPdf($report);

        return $pdf->download('izvjestaj-' . $report->date . '.pdf');
    }

    public function export($id)
    {
        $report = DailyReport::with([
            'location',
            'submittedBy',
            'fiscalItems',
            'nonFiscalItems',
            'cardPayments',
            'wireTransfers',
            'associates.doctor',
            'patients.doctor',
            'workSchedule'
        ])->findOrFail($id);

        $pdf = $this->pdfService->generateReportPdf($report);

        return $pdf->download('izvjestaj-' . $report->date . '.pdf');
    }

    public function sendEmail(Request $request, $id)
    {
        $report = DailyReport::with([
            'location',
            'submittedBy',
            'fiscalItems',
            'nonFiscalItems',
            'cardPayments',
            'wireTransfers',
            'associates.doctor',
            'patients.doctor',
            'workSchedule'
        ])->findOrFail($id);

        $email = $request->input('email') ?? $report->location->email ?? auth()->user()->email;

        if (!$email) {
            return response()->json(['error' => 'Email adresa nije pronađena'], 400);
        }

        try {
            $this->emailService->sendReport($report, $email);

            // Update report status
            $report->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            return response()->json([
                'message' => 'Izvještaj je uspješno poslan na email: ' . $email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Greška pri slanju emaila: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Same as store but with existing report
        return $this->store($request);
    }

    public function destroy($id)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $report = DailyReport::findOrFail($id);
        $report->delete();

        return response()->json(['message' => 'Report deleted successfully']);
    }
}
