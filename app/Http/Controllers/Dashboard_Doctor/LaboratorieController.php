<?php

namespace App\Http\Controllers\Dashboard_Doctor;

use App\Http\Controllers\Controller;
use App\Interfaces\doctor_dashboard\LaboratoriesRepositoryInterface;
use App\Models\Invoice;
use Illuminate\Http\Request;

class LaboratorieController extends Controller
{
    private $laboratorie;

    public function __construct(LaboratoriesRepositoryInterface $laboratorie)
    {
        $this->laboratorie = $laboratorie;
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'description' => ['required', 'string', 'max:5000'],
        ]);
        $invoice = Invoice::where('doctor_id', auth('doctor')->id())->findOrFail($request->invoice_id);
        $request->merge(['patient_id' => $invoice->patient_id, 'doctor_id' => $invoice->doctor_id]);
        return $this->laboratorie->store($request);
    }


    public function update(Request $request, $id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:laboratories,id']])->validate();
        $request->validate(['description' => ['required', 'string', 'max:5000']]);
        return $this->laboratorie->update($request, $id);
    }


    public function destroy($id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:laboratories,id']])->validate();
        return $this->laboratorie->destroy($id);
    }
}
