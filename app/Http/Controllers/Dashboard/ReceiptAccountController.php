<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Interfaces\Finance\ReceiptRepositoryInterface;
use Illuminate\Http\Request;

class ReceiptAccountController extends Controller
{
    private $Receipt;

    public function __construct(ReceiptRepositoryInterface $Receipt)
    {
        $this->Receipt = $Receipt;
    }


    public function index()
    {
        return $this->Receipt->index();
    }

    public function create()
    {
        return $this->Receipt->create();
    }


    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'Debit' => ['required', 'numeric', 'between:0.01,999999.99'],
            'description' => ['required', 'string', 'max:255'],
        ]);
        return $this->Receipt->store($request);
    }


    public function show($id)
    {
        return $this->Receipt->show($id);
    }


    public function edit($id)
    {
        return $this->Receipt->edit($id);
    }


    public function update(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'exists:receipt_accounts,id'],
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'Debit' => ['required', 'numeric', 'between:0.01,999999.99'],
            'description' => ['required', 'string', 'max:255'],
        ]);
        return $this->Receipt->update($request);
    }


    public function destroy(Request $request)
    {
        $request->validate(['id' => ['required', 'integer', 'exists:receipt_accounts,id']]);
        return $this->Receipt->destroy($request);
    }
}
