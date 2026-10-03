<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorsRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Interfaces\Doctors\DoctorRepositoryInterface;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{

    private $Doctors;

    public function __construct(DoctorRepositoryInterface $Doctors)
    {
        $this->Doctors = $Doctors;
    }


    public function index()
    {
        return $this->Doctors->index();
    }


    public function create()
    {
        return $this->Doctors->create();
    }


    public function store(StoreDoctorsRequest $request)
    {
        return $this->Doctors->store($request);
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        return $this->Doctors->edit($id);
    }


    public function update(UpdateDoctorRequest $request, $id)
    {
        $request->merge(['id' => $id]);
        return $this->Doctors->update($request);
    }


    public function destroy(Request $request)
    {
        $request->validate([
            'page_id' => ['sometimes', 'integer', 'in:1'],
        ]);
        if ($request->filled('page_id')) {
            $request->validate(['id' => ['required', 'integer', 'exists:doctors,id']]);
        } else {
            $request->validate(['delete_select_id' => ['required', 'string', 'regex:/^\d+(,\d+)*$/']]);
            validator(
                ['ids' => explode(',', $request->delete_select_id)],
                ['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'distinct', 'exists:doctors,id']]
            )->validate();
        }
        return $this->Doctors->destroy($request);
    }

    public function update_password(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer|exists:doctors,id',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6'
        ]);

        return $this->Doctors->update_password($request);
    }

    public function update_status(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer|exists:doctors,id',
            'status' => 'required|in:0,1',
        ]);
        return $this->Doctors->update_status($request);
    }
}
