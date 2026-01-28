<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Location;
use App\Models\Doctor;
use App\Models\Service;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'id' => Str::uuid(),
            'email' => 'admin@izvjestaji.com',
            'password' => Hash::make('admin123'),
            'first_name' => 'Admin',
            'last_name' => 'User',
            'role' => 'admin',
            'active' => true,
        ]);

        echo "✅ Admin kreiran: admin@izvjestaji.com / admin123\n";

        // Create Staff User
        $staff = User::create([
            'id' => Str::uuid(),
            'email' => 'staff@izvjestaji.com',
            'password' => Hash::make('staff123'),
            'first_name' => 'Staff',
            'last_name' => 'User',
            'role' => 'staff',
            'active' => true,
        ]);

        echo "✅ Staff kreiran: staff@izvjestaji.com / staff123\n";

        // Create Locations
        $location1 = Location::create([
            'id' => Str::uuid(),
            'name' => 'Ordinacija Centar',
            'address' => 'Trg Oslobođenja 1, 71000 Sarajevo',
            'city' => 'Sarajevo',
            'phone' => '+387 33 123 456',
            'email' => 'centar@izvjestaji.com',
            'active' => true,
        ]);

        $location2 = Location::create([
            'id' => Str::uuid(),
            'name' => 'Ordinacija Istok',
            'address' => 'Zmaja od Bosne 10, 71000 Sarajevo',
            'city' => 'Sarajevo',
            'phone' => '+387 33 234 567',
            'email' => 'istok@izvjestaji.com',
            'active' => true,
        ]);

        echo "✅ Lokacije kreirane: Ordinacija Centar, Ordinacija Istok\n";

        // Create Employees
        $medSestra = User::create([
            'id' => Str::uuid(),
            'email' => 'sestra@izvjestaji.com',
            'password' => Hash::make('sestra123'),
            'first_name' => 'Marija',
            'last_name' => 'Marković',
            'role' => 'employee',
            'job_title' => 'Medicinska sestra',
            'active' => true,
        ]);

        $recepcionar = User::create([
            'id' => Str::uuid(),
            'email' => 'recepcija@izvjestaji.com',
            'password' => Hash::make('recepcija123'),
            'first_name' => 'Ana',
            'last_name' => 'Anić',
            'role' => 'employee',
            'job_title' => 'Recepcionar',
            'active' => true,
        ]);

        echo "✅ Radnici kreirani: Marija Marković (Med. sestra), Ana Anić (Recepcionar)\n";

        // Assign employees to locations
        \DB::table('staff_locations')->insert([
            [
                'user_id' => $medSestra->id,
                'location_id' => $location1->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $medSestra->id,
                'location_id' => $location2->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $recepcionar->id,
                'location_id' => $location1->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Assign staff to locations
        \DB::table('staff_locations')->insert([
            [
                'user_id' => $staff->id,
                'location_id' => $location1->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $staff->id,
                'location_id' => $location2->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create Doctors
        $doctor1 = Doctor::create([
            'id' => Str::uuid(),
            'first_name' => 'Adem',
            'last_name' => 'Balić',
            'initials' => 'AB',
            'email' => 'adem.balic@izvjestaji.com',
            'role' => 'doctor',
            'active' => true,
        ]);

        $doctor2 = Doctor::create([
            'id' => Str::uuid(),
            'first_name' => 'Devleta',
            'last_name' => 'Balić',
            'initials' => 'DB',
            'email' => 'devleta.balic@izvjestaji.com',
            'role' => 'doctor',
            'active' => true,
        ]);

        $doctor3 = Doctor::create([
            'id' => Str::uuid(),
            'first_name' => 'Enida',
            'last_name' => 'Hodžić',
            'initials' => 'EH',
            'email' => 'enida.hodzic@izvjestaji.com',
            'role' => 'associate',
            'active' => true,
        ]);

        echo "✅ Doktori kreirani: Adem Balić, Devleta Balić, Enida Hodžić\n";

        // Assign doctors to locations
        \DB::table('doctor_locations')->insert([
            [
                'doctor_id' => $doctor1->id,
                'location_id' => $location1->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'doctor_id' => $doctor1->id,
                'location_id' => $location2->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'doctor_id' => $doctor2->id,
                'location_id' => $location1->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'doctor_id' => $doctor3->id,
                'location_id' => $location2->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create Services
        Service::create([
            'id' => Str::uuid(),
            'name' => 'Ginekološki pregled',
            'category' => 'fiscal',
            'price' => 50.00,
            'active' => true,
        ]);

        Service::create([
            'id' => Str::uuid(),
            'name' => 'Ultrazvuk',
            'category' => 'fiscal',
            'price' => 80.00,
            'active' => true,
        ]);

        Service::create([
            'id' => Str::uuid(),
            'name' => '4D Ultrazvuk',
            'category' => 'fiscal',
            'price' => 120.00,
            'active' => true,
        ]);

        Service::create([
            'id' => Str::uuid(),
            'name' => 'Laboratorijska analiza',
            'category' => 'non-fiscal',
            'price' => 30.00,
            'active' => true,
        ]);

        Service::create([
            'id' => Str::uuid(),
            'name' => 'Konsultacije',
            'category' => 'non-fiscal',
            'price' => 40.00,
            'active' => true,
        ]);

        echo "✅ Usluge kreirane: 5 usluga (3 fiskalne, 2 nefiskalne)\n";

        echo "\n";
        echo "========================================\n";
        echo "  SEEDING ZAVRŠEN!\n";
        echo "========================================\n";
        echo "\n";
        echo "📧 Admin pristup:\n";
        echo "   Email: admin@izvjestaji.com\n";
        echo "   Password: admin123\n";
        echo "\n";
        echo "📧 Staff pristup:\n";
        echo "   Email: staff@izvjestaji.com\n";
        echo "   Password: staff123\n";
        echo "\n";
        echo "👷 Radnici:\n";
        echo "   Email: sestra@izvjestaji.com / sestra123\n";
        echo "   Email: recepcija@izvjestaji.com / recepcija123\n";
        echo "\n";
        echo "✅ 2 Lokacije\n";
        echo "✅ 3 Doktora\n";
        echo "✅ 2 Radnika\n";
        echo "✅ 5 Usluga\n";
        echo "\n";
    }
}
