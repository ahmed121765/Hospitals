<?php

namespace App\Http\Livewire;

use App\Models\Doctor;
use App\Models\FundAccount;
use App\Models\Group;
use App\Models\group_invoice;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PatientAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;

class GroupInvoices extends Component
{
    public $InvoiceSaved = false;
    public $InvoiceUpdated = false;
    public $show_table = true;
    public $updateMode = false;
    public $group_invoice_id;
    public $Group_id;
    public $catchError;
    public $price = 0;
    public $patient_id, $doctor_id, $section_id, $type;
    public $discount_value = 0;
    public $tax_rate = 0;



    public function render()
    {
        return view('livewire.group_invoices.group-invoices', [
            'group_invoices' => Invoice::where('invoice_type', 2)->get(),
            'Patients' => Patient::all(),
            'Doctors' => Doctor::all(),
            'Groups' => Group::all(),
            'subtotal' => $Total_after_discount = ((is_numeric($this->price) ? $this->price : 0)) - ((is_numeric($this->discount_value) ? $this->discount_value : 0)),
            'tax_value' => $Total_after_discount * ((is_numeric($this->tax_rate) ? $this->tax_rate : 0) / 100)
        ])->extends('Dashboard.layouts');
    }


    public function show_form_add()
    {
        $this->show_table = false;
    }


    public function get_section()
    {
        $doctor = Doctor::with('section')->find($this->doctor_id);
        if (!$doctor || !$doctor->section) {
            $this->addError('doctor_id', trans('validation.exists'));
            return;
        }
        $this->resetErrorBag('doctor_id');
        $this->section_id = $doctor->section->name;
    }

    public function get_price()
    {
        $group = Group::find($this->Group_id);
        if (!$group) {
            $this->addError('Group_id', trans('validation.exists'));
            return;
        }
        $this->resetErrorBag('Group_id');
        $this->price = $group->Total_before_discount;
        $this->discount_value = $group->discount_value;
        $this->tax_rate = $group->tax_rate;
    }


    public function store()
    {

        $this->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
            'Group_id' => ['required', 'integer', 'exists:groups,id'],
            'type' => ['required', 'integer', 'in:1,2'],
        ]);

        $doctor = Doctor::with('section')->findOrFail($this->doctor_id);
        if (!$doctor->section) {
            $this->addError('doctor_id', trans('validation.exists'));
            return;
        }
        $group = Group::findOrFail($this->Group_id);
        $this->section_id = $doctor->section->name;
        $this->price = $group->Total_before_discount;
        $this->discount_value = $group->discount_value;
        $this->tax_rate = $group->tax_rate;
        $this->validate([
            'discount_value' => ['required', 'numeric', 'min:0', 'lte:price'],
            'tax_rate' => ['required', 'numeric', 'between:0,100'],
        ]);
        $totalWithTax = ($this->price - $this->discount_value) * (1 + $this->tax_rate / 100);
        if ($totalWithTax > 999999.99) {
            $this->addError('tax_rate', 'The invoice total exceeds the maximum supported amount.');
            return;
        }

        if ($this->updateMode) {
            $this->validate(['group_invoice_id' => ['required', 'integer', 'exists:invoices,id']]);
            Invoice::where('invoice_type', 2)->findOrFail($this->group_invoice_id);
        }

        // في حالة كانت الفاتورة نقدي
        if ($this->type == 1) {

            try {
                // في حالة التعديل
                if ($this->updateMode) {

                    $group_invoices = Invoice::findorfail($this->group_invoice_id);
                    $group_invoices->invoice_type = 2;
                    $group_invoices->invoice_date = date('Y-m-d');
                    $group_invoices->patient_id = $this->patient_id;
                    $group_invoices->doctor_id = $this->doctor_id;
                    $group_invoices->section_id = $doctor->section_id;
                    $group_invoices->Group_id = $this->Group_id;
                    $group_invoices->price = $this->price;
                    $group_invoices->discount_value = $this->discount_value;
                    $group_invoices->tax_rate = $this->tax_rate;
                    // قيمة الضريبة = السعر - الخصم * نسبة الضريبة /100
                    $group_invoices->tax_value = ($this->price - $this->discount_value) * ((is_numeric($this->tax_rate) ? $this->tax_rate : 0) / 100);
                    // الاجمالي شامل الضريبة  = السعر - الخصم + قيمة الضريبة
                    $group_invoices->total_with_tax = $group_invoices->price - $group_invoices->discount_value + $group_invoices->tax_value;
                    $group_invoices->type = $this->type;
                    $group_invoices->save();

                    $fund_accounts = FundAccount::where('invoice_id', $this->group_invoice_id)->first();
                    $fund_accounts->date = date('Y-m-d');
                    $fund_accounts->invoice_id = $group_invoices->id;
                    $fund_accounts->Debit = $group_invoices->total_with_tax;
                    $fund_accounts->credit = 0.00;
                    $fund_accounts->save();
                    $this->InvoiceUpdated = true;
                    $this->show_table = true;

                }

                // في حالة الاضافة
                else {

                    $group_invoices = new Invoice();
                    $group_invoices->invoice_type = 2;
                    $group_invoices->invoice_date = date('Y-m-d');
                    $group_invoices->patient_id = $this->patient_id;
                    $group_invoices->doctor_id = $this->doctor_id;
                    $group_invoices->section_id = $doctor->section_id;
                    $group_invoices->Group_id = $this->Group_id;
                    $group_invoices->price = $this->price;
                    $group_invoices->discount_value = $this->discount_value;
                    $group_invoices->tax_rate = $this->tax_rate;
                    // قيمة الضريبة = السعر - الخصم * نسبة الضريبة /100
                    $group_invoices->tax_value = ($this->price - $this->discount_value) * ((is_numeric($this->tax_rate) ? $this->tax_rate : 0) / 100);
                    // الاجمالي شامل الضريبة  = السعر - الخصم + قيمة الضريبة
                    $group_invoices->total_with_tax = $group_invoices->price - $group_invoices->discount_value + $group_invoices->tax_value;
                    $group_invoices->type = $this->type;
                    $group_invoices->save();

                    $fund_accounts = new FundAccount();
                    $fund_accounts->date = date('Y-m-d');
                    $fund_accounts->invoice_id = $group_invoices->id;
                    $fund_accounts->Debit = $group_invoices->total_with_tax;
                    $fund_accounts->credit = 0.00;
                    $fund_accounts->save();
                    $this->InvoiceSaved = true;
                    $this->show_table = true;
                    $this->rest();
                }

            } catch (\Exception $e) {
                $this->catchError = $e->getMessage();
            }

        }

        //----------------------------------------------------------------------------------------------------

        // في حالة الفاتورة اجل
        else {

            try {
                // في حالة التعديل
                if ($this->updateMode) {

                    $group_invoices = Invoice::findorfail($this->group_invoice_id);
                    $group_invoices->invoice_type = 2;
                    $group_invoices->invoice_date = date('Y-m-d');
                    $group_invoices->patient_id = $this->patient_id;
                    $group_invoices->doctor_id = $this->doctor_id;
                    $group_invoices->section_id = $doctor->section_id;
                    $group_invoices->Group_id = $this->Group_id;
                    $group_invoices->price = $this->price;
                    $group_invoices->discount_value = $this->discount_value;
                    $group_invoices->tax_rate = $this->tax_rate;
                    // قيمة الضريبة = السعر - الخصم * نسبة الضريبة /100
                    $group_invoices->tax_value = ($this->price - $this->discount_value) * ((is_numeric($this->tax_rate) ? $this->tax_rate : 0) / 100);
                    // الاجمالي شامل الضريبة  = السعر - الخصم + قيمة الضريبة
                    $group_invoices->total_with_tax = $group_invoices->price - $group_invoices->discount_value + $group_invoices->tax_value;
                    $group_invoices->type = $this->type;
                    $group_invoices->save();

                    $patient_accounts = PatientAccount::where('invoice_id', $this->group_invoice_id)->first();
                    $patient_accounts->date = date('Y-m-d');
                    $patient_accounts->invoice_id = $group_invoices->id;
                    $patient_accounts->patient_id = $group_invoices->patient_id;
                    $patient_accounts->Debit = $group_invoices->total_with_tax;
                    $patient_accounts->credit = 0.00;
                    $patient_accounts->save();
                    $this->InvoiceUpdated = true;
                    $this->show_table = true;
                    $this->rest();

                }

                // في حالة الاضافة
                else {


                    $group_invoices = new Invoice();
                    $group_invoices->invoice_type = 2;
                    $group_invoices->invoice_date = date('Y-m-d');
                    $group_invoices->patient_id = $this->patient_id;
                    $group_invoices->doctor_id = $this->doctor_id;
                    $group_invoices->section_id = $doctor->section_id;
                    $group_invoices->Group_id = $this->Group_id;
                    $group_invoices->price = $this->price;
                    $group_invoices->discount_value = $this->discount_value;
                    $group_invoices->tax_rate = $this->tax_rate;
                    // قيمة الضريبة = السعر - الخصم * نسبة الضريبة /100
                    $group_invoices->tax_value = ($this->price - $this->discount_value) * ((is_numeric($this->tax_rate) ? $this->tax_rate : 0) / 100);
                    // الاجمالي شامل الضريبة  = السعر - الخصم + قيمة الضريبة
                    $group_invoices->total_with_tax = $group_invoices->price - $group_invoices->discount_value + $group_invoices->tax_value;
                    $group_invoices->type = $this->type;
                    $group_invoices->save();

                    $patient_accounts = new PatientAccount();
                    $patient_accounts->date = date('Y-m-d');
                    $patient_accounts->invoice_id = $group_invoices->id;
                    $patient_accounts->patient_id = $group_invoices->patient_id;
                    $patient_accounts->Debit = $group_invoices->total_with_tax;
                    $patient_accounts->credit = 0.00;
                    $patient_accounts->save();
                    $this->InvoiceSaved = true;
                    $this->show_table = true;
                    $this->rest();
                }

            } catch (\Exception $e) {
                $this->catchError = $e->getMessage();
            }
        }
    }


    public function edit($id)
    {

        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:invoices,id']])->validate();
        Invoice::where('invoice_type', 2)->findOrFail($id);

        $this->show_table = false;
        $this->updateMode = true;
        $group_invoices = Invoice::findorfail($id);
        $this->group_invoice_id = $group_invoices->id;
        $this->patient_id = $group_invoices->patient_id;
        $this->doctor_id = $group_invoices->doctor_id;
        $this->section_id = $group_invoices->Section->name;
        $this->Group_id = $group_invoices->Group_id;
        $this->price = $group_invoices->price;
        $this->discount_value = $group_invoices->discount_value;
        $this->tax_rate = $group_invoices->tax_rate;
        $this->tax_value = $group_invoices->tax_value;
        $this->type = $group_invoices->type;

    }

    public function delete($id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:invoices,id']])->validate();
        Invoice::where('invoice_type', 2)->findOrFail($id);
        $this->group_invoice_id = (int) $id;
    }

    public function destroy()
    {
        validator(['id' => $this->group_invoice_id], ['id' => ['required', 'integer', 'exists:invoices,id']])->validate();
        Invoice::where('invoice_type', 2)->findOrFail($this->group_invoice_id);
        Invoice::destroy($this->group_invoice_id);
        return redirect()->to('/group_invoices');
    }

    public function print($id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:invoices,id']])->validate();
        $single_invoice = Invoice::findorfail($id);
        if ((int) $single_invoice->invoice_type !== 2) {
            abort(404);
        }
        return Redirect::route('group_Print_single_invoices', [
            'invoice_date' => $single_invoice->invoice_date,
            'doctor_id' => $single_invoice->Doctor->name,
            'section_id' => $single_invoice->Section->name,
            'Group_id' => $single_invoice->Group->name,
            'type' => $single_invoice->type,
            'price' => $single_invoice->price,
            'discount_value' => $single_invoice->discount_value,
            'tax_rate' => $single_invoice->tax_rate,
            'total_with_tax' => $single_invoice->total_with_tax,
        ]);

    }
}
