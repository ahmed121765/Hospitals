<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInsuranceRequest;
use App\Http\Requests\UpdateInsuranceRequest;
use App\Interfaces\insurances\insuranceRepositoryInterface;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{

    private $insurance;

    public function __construct(insuranceRepositoryInterface $insurance)
    {
        $this->insurance = $insurance;
    }

    public function index()
    {
        return $this->insurance->index();
    }

    public function create()
    {
        return $this->insurance->create();
    }


    public function store(StoreInsuranceRequest $request)
    {
        return $this->insurance->store($request);
    }

    public function edit($id)
    {
        return $this->insurance->edit($id);
    }


    public function update(UpdateInsuranceRequest $request)
    {
        return $this->insurance->update($request);
    }



    public function destroy(Request $request)
    {
        $request->validate(['id' => ['required', 'integer', 'exists:insurances,id']]);
        return $this->insurance->destroy($request);
    }
}
