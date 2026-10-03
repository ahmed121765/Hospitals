<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Interfaces\Finance\PaymentRepositoryInterface;
use Illuminate\Http\Request;

class PaymentAccountController extends Controller
{
    private $Payment;

    public function __construct(PaymentRepositoryInterface $Payment)
    {
        $this->Payment = $Payment;
    }
    public function index()
    {
        return $this->Payment->index();
    }


    public function create()
    {
        return $this->Payment->create();
    }


    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'credit' => ['required', 'numeric', 'between:0.01,999999.99'],
            'description' => ['required', 'string', 'max:255'],
        ]);
        return $this->Payment->store($request);
    }


    public function show($id)
    {
        return $this->Payment->show($id);
    }


    public function edit($id)
    {
        return $this->Payment->edit($id);
    }


    public function update(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'exists:payment_accounts,id'],
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'credit' => ['required', 'numeric', 'between:0.01,999999.99'],
            'description' => ['required', 'string', 'max:255'],
        ]);
        return $this->Payment->update($request);
    }


    public function destroy(Request $request)
    {
        $request->validate(['id' => ['required', 'integer', 'exists:payment_accounts,id']]);
        return $this->Payment->destroy($request);
    }
}
