<?php

namespace App\Http\Controllers\Dashboard_Patient;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Laboratorie;
use App\Models\Ray;
use App\Models\ReceiptAccount;

class PatientController extends Controller
{
    public function invoices()
    {

        $invoices = Invoice::where('patient_id', auth('patient')->id())->get();
        return view('Dashboard.dashboard_patient.invoices', compact('invoices'));
    }

    public function laboratories()
    {

        $laboratories = Laboratorie::where('patient_id', auth('patient')->id())->get();
        return view('Dashboard.dashboard_patient.laboratories', compact('laboratories'));
    }

    public function viewLaboratories($id)
    {

        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:laboratories,id']])->validate();
        $laboratorie = Laboratorie::where('patient_id', auth('patient')->id())->findOrFail($id);
        return view('Dashboard.dashboard_LaboratorieEmployee.invoices.patient_details', compact('laboratorie'));
    }

    public function rays()
    {

        $rays = Ray::where('patient_id', auth('patient')->id())->get();
        return view('Dashboard.dashboard_patient.rays', compact('rays'));
    }

    public function viewRays($id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:rays,id']])->validate();
        $rays = Ray::where('patient_id', auth('patient')->id())->findOrFail($id);
        return view('Dashboard.dashboard_RayEmployee.invoices.patient_details', compact('rays'));
    }

    public function payments()
    {

        $payments = ReceiptAccount::where('patient_id', auth('patient')->id())->get();
        return view('Dashboard.dashboard_patient.payments', compact('payments'));
    }
}
