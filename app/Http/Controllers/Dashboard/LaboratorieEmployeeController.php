<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Interfaces\LaboratorieEmployee\LaboratorieEmployeeRepositoryInterface;
use Illuminate\Http\Request;

class LaboratorieEmployeeController extends Controller
{

    private $laboratorie_employee;

    public function __construct(LaboratorieEmployeeRepositoryInterface $laboratorie_employee)
    {
        $this->laboratorie_employee = $laboratorie_employee;
    }

    public function index()
    {
        return $this->laboratorie_employee->index();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:laboratorie_employees,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);
        return $this->laboratorie_employee->store($request);
    }


    public function update(Request $request, $id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:laboratorie_employees,id']])->validate();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:laboratorie_employees,email,' . $id],
            'password' => ['nullable', 'string', 'min:8'],
        ]);
        return $this->laboratorie_employee->update($request, $id);
    }


    public function destroy($id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:laboratorie_employees,id']])->validate();
        return $this->laboratorie_employee->destroy($id);
    }
}
