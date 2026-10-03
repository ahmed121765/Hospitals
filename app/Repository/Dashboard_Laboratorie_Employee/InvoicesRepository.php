<?php

namespace App\Repository\Dashboard_Laboratorie_Employee;

use App\Interfaces\Dashboard_Laboratorie_Employee\InvoicesRepositoryInterface;
use App\Models\Laboratorie;
use App\Traits\UploadTrait;

class InvoicesRepository implements InvoicesRepositoryInterface
{

    use UploadTrait;

    public function index()
    {
        $invoices = Laboratorie::where('case', 0)->get();
        return view('Dashboard.dashboard_LaboratorieEmployee.invoices.index', compact('invoices'));
    }

    public function completed_invoices()
    {
        $invoices = Laboratorie::where('case', 1)->where('employee_id', auth('laboratorie_employee')->id())->get();
        return view('Dashboard.dashboard_LaboratorieEmployee.invoices.completed_invoices', compact('invoices'));
    }

    public function edit($id)
    {
        $invoice = Laboratorie::where('case', 0)->findOrFail($id);
        return view('Dashboard.dashboard_LaboratorieEmployee.invoices.add_diagnosis', compact('invoice'));
    }

    public function update($request, $id)
    {
        $invoice = Laboratorie::where('case', 0)->findOrFail($id);

        $invoice->update([
            'employee_id' => auth('laboratorie_employee')->id(),
            'description_employee' => $request->description_employee,
            'case' => 1,
        ]);


        if ($request->hasFile('photos')) {
            foreach ($request->photos as $photo) {
                //Upload img
                $this->verifyAndStoreImageForeach($photo, 'laboratories', 'upload_image', $invoice->id, 'App\Models\Laboratorie');
            }
        }
        session()->flash('edit');
        return redirect()->route('invoices_laboratorie_employee.index');

    }

    public function view_laboratories($id)
    {
        $laboratorie = Laboratorie::where('employee_id', auth('laboratorie_employee')->id())->findOrFail($id);
        return view('Dashboard.dashboard_LaboratorieEmployee.invoices.patient_details', compact('laboratorie'));
    }
}
