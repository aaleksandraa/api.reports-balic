<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\DailyReport;
use App\Models\User;
use App\Models\Location;
use App\Models\Doctor;
use Carbon\Carbon;

class MonthlyReportsSeeder extends Seeder
{
    /**
     * Seed monthly reports for January 2026
     */
    public function run(): void
    {
        echo "🚀 Kreiranje mjesečnih izvještaja za Januar 2026...\n\n";

        // Get data
        $locations = Location::where('active', true)->get();
        $user = User::where('role', 'admin')->first();
        $doctors = Doctor::where('role', 'doctor')->where('active', true)->get();
        $employees = User::where('role', 'employee')->where('active', true)->get();

        if ($locations->isEmpty() || !$user || $doctors->isEmpty()) {
            echo "❌ Nema potrebnih podataka. Prvo pokrenite DatabaseSeeder.\n";
            return;
        }

        // January 2026 - 1st to 16th
        $startDate = Carbon::create(2026, 1, 1);
        $endDate = Carbon::create(2026, 1, 16);

        $currentDate = $startDate->copy();
        $reportCount = 0;

        while ($currentDate <= $endDate) {
            // Skip Sundays (Nedjelja)
            if ($currentDate->dayOfWeek === Carbon::SUNDAY) {
                $currentDate->addDay();
                continue;
            }

            // Create reports for BOTH locations
            foreach ($locations as $location) {
                echo "📅 Kreiranje izvještaja za: " . $currentDate->format('d.m.Y') . " (" . $currentDate->locale('bs')->dayName . ") - {$location->name}\n";

                // Create daily report
                $report = DailyReport::create([
                    'id' => Str::uuid(),
                    'location_id' => $location->id,
                    'date' => $currentDate->format('Y-m-d'),
                    'day_of_week' => $currentDate->locale('bs')->dayName,
                    'notes' => 'Automatski generisan izvještaj za testiranje',
                    'submitted_by' => $user->id,
                    'status' => 'submitted',
                    'submitted_at' => $currentDate->copy()->setTime(17, 0, 0),
                ]);

            // Fiscal items (3-5 items per day)
            $fiscalCount = rand(3, 5);
            for ($i = 0; $i < $fiscalCount; $i++) {
                $services = ['Ginekološki pregled', 'Ultrazvuk', '4D Ultrazvuk', 'Kontrola', 'Konsultacije'];
                $prices = [50, 80, 120, 40, 60];
                $serviceIndex = array_rand($services);

                $doctorCounts = [];
                foreach ($doctors as $doctor) {
                    $doctorCounts[$doctor->id] = rand(0, 5);
                }

                \DB::table('fiscal_items')->insert([
                    'id' => Str::uuid(),
                    'report_id' => $report->id,
                    'service_name' => $services[$serviceIndex],
                    'doctor_counts' => json_encode($doctorCounts),
                    'price' => $prices[$serviceIndex] * array_sum($doctorCounts),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Non-fiscal items (1-3 items per day)
            $nonFiscalCount = rand(1, 3);
            for ($i = 0; $i < $nonFiscalCount; $i++) {
                $services = ['Laboratorijska analiza', 'Konsultacije', 'Pregled dokumentacije'];
                $prices = [30, 40, 25];
                $serviceIndex = array_rand($services);

                $doctorCounts = [];
                foreach ($doctors as $doctor) {
                    $doctorCounts[$doctor->id] = rand(0, 3);
                }

                \DB::table('non_fiscal_items')->insert([
                    'id' => Str::uuid(),
                    'report_id' => $report->id,
                    'service_name' => $services[$serviceIndex],
                    'doctor_counts' => json_encode($doctorCounts),
                    'price' => $prices[$serviceIndex] * array_sum($doctorCounts),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Card payments (1-2 items per day)
            $cardCount = rand(1, 2);
            for ($i = 0; $i < $cardCount; $i++) {
                $services = ['Ginekološki pregled', 'Ultrazvuk', '4D Ultrazvuk'];
                $prices = [50, 80, 120];
                $serviceIndex = array_rand($services);

                $doctorCounts = [];
                foreach ($doctors as $doctor) {
                    $doctorCounts[$doctor->id] = rand(0, 2);
                }

                \DB::table('card_payments')->insert([
                    'id' => Str::uuid(),
                    'report_id' => $report->id,
                    'service_name' => $services[$serviceIndex],
                    'doctor_counts' => json_encode($doctorCounts),
                    'price' => $prices[$serviceIndex] * array_sum($doctorCounts),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Wire transfers (0-1 items per day)
            if (rand(0, 1)) {
                $doctorCounts = [];
                foreach ($doctors as $doctor) {
                    $doctorCounts[$doctor->id] = rand(0, 1);
                }

                \DB::table('wire_transfers')->insert([
                    'id' => Str::uuid(),
                    'report_id' => $report->id,
                    'patient_name' => 'Pacijent ' . rand(1, 100),
                    'doctor_counts' => json_encode($doctorCounts),
                    'price' => rand(100, 500),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Patients (5-15 per day)
            $patientCount = rand(5, 15);
            $reasons = ['Ginekološki', 'Trudnički', 'Kontrola', '4D', 'Sterilitet'];
            $cities = ['Sarajevo', 'Tuzla', 'Zenica', 'Mostar', 'Banja Luka'];

            for ($i = 0; $i < $patientCount; $i++) {
                \DB::table('patients')->insert([
                    'id' => Str::uuid(),
                    'report_id' => $report->id,
                    'full_name' => 'Pacijent ' . rand(1, 1000),
                    'city' => $cities[array_rand($cities)],
                    'reason' => $reasons[array_rand($reasons)],
                    'doctor_id' => $doctors->random()->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

                // Work schedule - employees (only on weekdays)
                if ($currentDate->isWeekday()) {
                    foreach ($employees as $employee) {
                        // Not all employees work every day
                        if (rand(0, 10) > 2) { // 80% chance to work
                            $arrivalHour = rand(7, 9);
                            $departureHour = rand(15, 17);
                            $hoursWorked = $departureHour - $arrivalHour + (rand(0, 1) * 0.5);

                            \DB::table('work_schedule')->insert([
                                'id' => Str::uuid(),
                                'report_id' => $report->id,
                                'employee_id' => $employee->id,
                                'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                                'arrival_time' => sprintf('%02d:00', $arrivalHour),
                                'departure_time' => sprintf('%02d:00', $departureHour),
                                'hours_worked' => $hoursWorked,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }

                $reportCount++;
            }

            $currentDate->addDay();
        }

        echo "\n";
        echo "========================================\n";
        echo "  ✅ SEEDING ZAVRŠEN!\n";
        echo "========================================\n";
        echo "\n";
        echo "📊 Kreirano izvještaja: $reportCount\n";
        echo "📅 Period: 01.01.2026 - 16.01.2026\n";
        echo "🏢 Lokacije: " . $locations->pluck('name')->join(', ') . "\n";
        echo "👤 Kreirao: {$user->first_name} {$user->last_name}\n";
        echo "\n";
        echo "Izvještaji uključuju:\n";
        echo "  ✅ Fiskalne stavke (3-5 po danu)\n";
        echo "  ✅ Nefiskalne stavke (1-3 po danu)\n";
        echo "  ✅ Kartična plaćanja (1-2 po danu)\n";
        echo "  ✅ Žiralne uplate (0-1 po danu)\n";
        echo "  ✅ Pacijenti (5-15 po danu)\n";
        echo "  ✅ Radni raspored (radni dani)\n";
        echo "\n";
        echo "💡 Nedjelje su preskočene (neradni dan)\n";
        echo "💡 Izvještaji kreirani za obje lokacije\n";
        echo "\n";
    }
}
