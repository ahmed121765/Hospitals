<?php

namespace App\Http\Controllers\Dashboard_Doctor;

use App\Http\Controllers\Controller;
use App\Models\Diagnostic;
use App\Models\Laboratorie;
use App\Models\Ray;
use Illuminate\Http\Request;

class PatientDetailsController extends Controller
{
    public function index($id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:patients,id']])->validate();
        $doctorId = auth('doctor')->id();
        $patient_records = Diagnostic::where('doctor_id', $doctorId)->where('patient_id', $id)->get();
        $patient_rays = Ray::where('doctor_id', $doctorId)->where('patient_id', $id)->get();
        $patient_Laboratories = Laboratorie::where('doctor_id', $doctorId)->where('patient_id', $id)->get();
        return view('Dashboard.doctor.invoices.patient_details', compact('patient_records', 'patient_rays', 'patient_Laboratories'));
    }
}
