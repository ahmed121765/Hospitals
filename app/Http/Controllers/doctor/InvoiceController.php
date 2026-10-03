<?php

namespace App\Http\Controllers\doctor;

use App\Http\Controllers\Controller;
use App\Interfaces\doctor_dashboard\InvoicesRepositoryInterface;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    private $invoices;

    public function __construct(InvoicesRepositoryInterface $invoices)
    {
        $this->invoices = $invoices;
    }

    public function index()
    {
        return $this->invoices->index();
    }

    public function reviewInvoices()
    {
        return $this->invoices->reviewInvoices();
    }

    public function completedInvoices()
    {
        return $this->invoices->completedInvoices();
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:invoices,id']])->validate();
        return $this->invoices->show($id);
    }

    public function showLaboratorie($id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:laboratories,id']])->validate();
        return $this->invoices->showLaboratorie($id);
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
