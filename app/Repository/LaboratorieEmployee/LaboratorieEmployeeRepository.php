<?php

namespace App\Repository\LaboratorieEmployee;

use App\Interfaces\LaboratorieEmployee\LaboratorieEmployeeRepositoryInterface;
use App\Models\LaboratorieEmployee;
use Illuminate\Support\Facades\Hash;

class LaboratorieEmployeeRepository implements LaboratorieEmployeeRepositoryInterface
{

    public function index()
    {
        $laboratorie_employees = LaboratorieEmployee::all();
        return view('Dashboard.laboratorie_employee.index', compact('laboratorie_employees'));
    }

    public function store($request)
    {
        try {

            $laboratorie_employee = new LaboratorieEmployee();
            $laboratorie_employee->name = $request->name;
            $laboratorie_employee->email = $request->email;
            $laboratorie_employee->password = Hash::make($request->password);
            $laboratorie_employee->save();
            session()->flash('add');
            return back();

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function update($request, $id)
    {
        $laboratorie_employee = LaboratorieEmployee::findOrFail($id);
        $laboratorie_employee->name = $request->name;
        $laboratorie_employee->email = $request->email;
        if ($request->filled('password')) {
            $laboratorie_employee->password = Hash::make($request->password);
        }
        $laboratorie_employee->save();

        session()->flash('edit');
        return redirect()->back();
    }

    public function destroy($id)
    {
        try {
            LaboratorieEmployee::destroy($id);
            session()->flash('delete');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
