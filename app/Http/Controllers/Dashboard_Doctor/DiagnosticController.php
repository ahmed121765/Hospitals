<?php

namespace App\Http\Controllers\Dashboard_Doctor;

use App\Http\Controllers\Controller;
use App\Interfaces\doctor_dashboard\DiagnosisRepositoryInterface;
use App\Models\Invoice;
use Illuminate\Http\Request;

class DiagnosticController extends Controller
{

    private $Diagnosis;

    public function __construct(DiagnosisRepositoryInterface $Diagnosis)
    {
        $this->Diagnosis = $Diagnosis;
    }

    public function index()
    {
        
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'diagnosis' => ['required', 'string', 'max:5000'],
            'medicine' => ['nullable', 'string', 'max:5000'],
        ]);
        $invoice = Invoice::where('doctor_id', auth('doctor')->id())->findOrFail($request->invoice_id);
        $request->merge(['patient_id' => $invoice->patient_id, 'doctor_id' => $invoice->doctor_id]);
        return $this->Diagnosis->store($request);
    }

    public function addReview(Request $request)
    {
        $request->validate([
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'diagnosis' => ['required', 'string', 'max:5000'],
            'medicine' => ['nullable', 'string', 'max:5000'],
        ]);
        $invoice = Invoice::where('doctor_id', auth('doctor')->id())->findOrFail($request->invoice_id);
        $request->merge(['patient_id' => $invoice->patient_id, 'doctor_id' => $invoice->doctor_id]);
        return $this->Diagnosis->addReview($request);
    }


    public function show($id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:patients,id']])->validate();
        return $this->Diagnosis->show($id);
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
