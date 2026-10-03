<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class singleInvoices extends Seeder
{
    public function run()
    {
        DB::table('invoices')->delete();
        DB::table('invoices')->insert([
            'invoice_type' => 1,
            'invoice_date' => '2026-06-24',
            'patient_id' =>1,
            'doctor_id' => 1,
            'section_id' =>1,
            'Service_id' =>1,
            'price' => 500.00,
            'discount_value' => 0.00,
            'tax_rate' => '17%',
            'tax_value' => '85',
            'total_with_tax' => 585.00,
            'type' => 1,
            'invoice_status' => 1,
        ]);

        
    }
}
