<?php

namespace App\Http\Controllers\Dashboard_Doctor;

use App\Http\Controllers\Controller;
use App\Interfaces\doctor_dashboard\RaysRepositoryInterface;
use App\Models\Invoice;
use Illuminate\Http\Request;

class RayController extends Controller
{

    private $ray;

    public function __construct(RaysRepositoryInterface $ray)
    {
        $this->ray = $ray;
    }


    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'description' => ['required', 'string', 'max:5000'],
        ]);
        $invoice = Invoice::where('doctor_id', auth('doctor')->id())->findOrFail($request->invoice_id);
        $request->merge(['patient_id' => $invoice->patient_id, 'doctor_id' => $invoice->doctor_id]);
        return $this->ray->store($request);
    }


    public function update(Request $request, $id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:rays,id']])->validate();
        $request->validate(['description' => ['required', 'string', 'max:5000']]);
        return $this->ray->update($request, $id);
    }


    public function destroy($id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:rays,id']])->validate();
        return $this->ray->destroy($id);
    }
}
