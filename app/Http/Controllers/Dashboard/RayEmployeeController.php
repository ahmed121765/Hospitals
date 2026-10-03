<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Interfaces\RayEmployee\RayEmployeeRepositoryInterface;
use Illuminate\Http\Request;

class RayEmployeeController extends Controller
{
    private $employee;

    public function __construct(RayEmployeeRepositoryInterface $employee)
    {
        $this->employee = $employee;
    }


    public function index()
    {
        return $this->employee->index();
    }



    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:ray_employees,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);
        return $this->employee->store($request);
    }



    public function update(Request $request, $id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:ray_employees,id']])->validate();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:ray_employees,email,' . $id],
            'password' => ['nullable', 'string', 'min:8'],
        ]);
        return $this->employee->update($request, $id);
    }


    public function destroy($id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:ray_employees,id']])->validate();
        return $this->employee->destroy($id);
    }
}
