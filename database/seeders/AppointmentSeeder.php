<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentSeeder extends Seeder
{
    public function run()
    {
        DB::table('appointments')->delete();

        $doctors = Doctor::all();
        $sections = Section::all();

        // Return if no doctors or sections exist
        if ($doctors->isEmpty() || $sections->isEmpty()) {
            return;
        }

        $Appointments = [
            ['name' => 'السبت', 'email' => 'saturday@hospital.com', 'phone' => '0123456789'],
            ['name' => 'الاحد', 'email' => 'sunday@hospital.com', 'phone' => '0123456789'],
            ['name' => 'الاثنين', 'email' => 'monday@hospital.com', 'phone' => '0123456789'],
            ['name' => 'الثلاثاء', 'email' => 'tuesday@hospital.com', 'phone' => '0123456789'],
            ['name' => 'الاربعاء', 'email' => 'wednesday@hospital.com', 'phone' => '0123456789'],
            ['name' => 'الخميس', 'email' => 'thursday@hospital.com', 'phone' => '0123456789'],
            ['name' => 'الجمعة', 'email' => 'friday@hospital.com', 'phone' => '0123456789'],
        ];

        foreach ($Appointments as $Appointment) {
            $Appointment['doctor_id'] = $doctors->random()->id;
            $Appointment['section_id'] = $sections->random()->id;
            Appointment::create($Appointment);
        }
    }
}