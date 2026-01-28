<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::where('role', 'radnik')
            ->with('locations')
            ->get();

        return response()->json($employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'email' => $employee->email,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'job_title' => $employee->job_title,
                'active' => $employee->active,
                'daily_work_hours' => $employee->daily_work_hours,
                'weekly_work_hours' => $employee->weekly_work_hours,
                'monthly_work_hours' => $employee->monthly_work_hours,
                'location_ids' => $employee->locations->pluck('id')->toArray(),
                'created_at' => $employee->created_at,
            ];
        }));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'daily_work_hours' => 'sometimes|numeric|min:0',
            'weekly_work_hours' => 'sometimes|numeric|min:0',
            'monthly_work_hours' => 'sometimes|numeric|min:0',
        ]);

        $employee = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'role' => 'radnik',
            'job_title' => $request->job_title,
            'active' => true,
            'daily_work_hours' => $request->input('daily_work_hours', 8),
            'weekly_work_hours' => $request->input('weekly_work_hours', 40),
            'monthly_work_hours' => $request->input('monthly_work_hours', 160),
        ]);

        return response()->json([
            'message' => 'Employee created successfully',
            'id' => $employee->id,
            'email' => $employee->email,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'job_title' => $employee->job_title,
            'active' => $employee->active,
            'daily_work_hours' => $employee->daily_work_hours,
            'weekly_work_hours' => $employee->weekly_work_hours,
            'monthly_work_hours' => $employee->monthly_work_hours,
            'location_ids' => [],
            'created_at' => $employee->created_at,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $employee = User::where('role', 'radnik')->findOrFail($id);

        $request->validate([
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'job_title' => 'sometimes|string|max:255',
            'active' => 'sometimes|boolean',
            'daily_work_hours' => 'sometimes|numeric|min:0',
            'weekly_work_hours' => 'sometimes|numeric|min:0',
            'monthly_work_hours' => 'sometimes|numeric|min:0',
        ]);

        $employee->update($request->only([
            'email',
            'first_name',
            'last_name',
            'job_title',
            'active',
            'daily_work_hours',
            'weekly_work_hours',
            'monthly_work_hours',
        ]));

        $employee->load('locations');

        return response()->json([
            'message' => 'Employee updated successfully',
            'id' => $employee->id,
            'email' => $employee->email,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'job_title' => $employee->job_title,
            'active' => $employee->active,
            'daily_work_hours' => $employee->daily_work_hours,
            'weekly_work_hours' => $employee->weekly_work_hours,
            'monthly_work_hours' => $employee->monthly_work_hours,
            'location_ids' => $employee->locations->pluck('id')->toArray(),
            'created_at' => $employee->created_at,
        ]);
    }

    public function destroy($id)
    {
        $employee = User::where('role', 'radnik')->findOrFail($id);
        $employee->delete();

        return response()->json([
            'message' => 'Employee deleted successfully',
        ]);
    }

    public function assignLocation(Request $request, $id)
    {
        $request->validate([
            'location_id' => 'required|uuid|exists:locations,id',
        ]);

        $employee = User::where('role', 'radnik')->findOrFail($id);

        if (!$employee->locations()->where('location_id', $request->location_id)->exists()) {
            $employee->locations()->attach($request->location_id);
        }

        return response()->json([
            'message' => 'Location assigned successfully',
        ]);
    }

    public function removeLocation(Request $request, $id)
    {
        $request->validate([
            'location_id' => 'required|uuid|exists:locations,id',
        ]);

        $employee = User::where('role', 'radnik')->findOrFail($id);
        $employee->locations()->detach($request->location_id);

        return response()->json([
            'message' => 'Location removed successfully',
        ]);
    }

    // Work hours statistics
    public function workHoursStats(Request $request, $id)
    {
        $employee = User::where('role', 'radnik')->findOrFail($id);

        $period = $request->input('period', 'week'); // day, week, month
        $date = $request->input('date', now()->toDateString());

        $carbonDate = Carbon::parse($date);

        switch ($period) {
            case 'day':
                $startDate = $carbonDate->startOfDay();
                $endDate = $carbonDate->copy()->endOfDay();
                break;
            case 'week':
                $startDate = $carbonDate->startOfWeek();
                $endDate = $carbonDate->copy()->endOfWeek();
                break;
            case 'month':
                $startDate = $carbonDate->startOfMonth();
                $endDate = $carbonDate->copy()->endOfMonth();
                break;
            default:
                $startDate = $carbonDate->startOfWeek();
                $endDate = $carbonDate->copy()->endOfWeek();
        }

        $workSchedules = WorkSchedule::where('employee_id', $id)
            ->whereHas('report', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->with('report:id,date,location_id')
            ->get();

        $totalHours = $workSchedules->where('status', 'present')->sum('hours_worked');
        $vacationDays = $workSchedules->where('status', 'vacation')->count();
        $sickLeaveDays = $workSchedules->where('status', 'sick_leave')->count();
        $absentDays = $workSchedules->where('status', 'absent')->count();

        $dailyBreakdown = $workSchedules->groupBy(function ($item) {
            return $item->report->date;
        })->map(function ($daySchedules) use ($employee) {
            $dayHours = $daySchedules->sum('hours_worked');
            $status = $daySchedules->first()->status ?? 'present';
            $expectedHours = $employee->daily_work_hours ?? 8;

            return [
                'date' => $daySchedules->first()->report->date,
                'hours' => $dayHours,
                'entries' => $daySchedules->count(),
                'status' => $status,
                'expected_hours' => $expectedHours,
                'difference' => $status === 'present' ? round($dayHours - $expectedHours, 2) : 0,
            ];
        })->values();

        // Calculate expected hours based on period
        $expectedHours = 0;
        if ($period === 'day') {
            $expectedHours = $employee->daily_work_hours ?? 8;
        } elseif ($period === 'week') {
            $expectedHours = $employee->weekly_work_hours ?? 40;
        } elseif ($period === 'month') {
            $expectedHours = $employee->monthly_work_hours ?? 160;
        }

        return response()->json([
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->first_name . ' ' . $employee->last_name,
                'job_title' => $employee->job_title,
                'daily_work_hours' => $employee->daily_work_hours,
                'weekly_work_hours' => $employee->weekly_work_hours,
                'monthly_work_hours' => $employee->monthly_work_hours,
            ],
            'period' => $period,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'total_hours' => round($totalHours, 2),
            'expected_hours' => $expectedHours,
            'difference' => round($totalHours - $expectedHours, 2),
            'total_days' => $workSchedules->pluck('report.date')->unique()->count(),
            'vacation_days' => $vacationDays,
            'sick_leave_days' => $sickLeaveDays,
            'absent_days' => $absentDays,
            'daily_breakdown' => $dailyBreakdown,
        ]);
    }
}
