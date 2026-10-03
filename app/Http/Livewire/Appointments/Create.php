<?php

namespace App\Http\Livewire\Appointments;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Section;
use Livewire\Component;

class Create extends Component
{
    public $doctors;
    public $sections;
    public $doctor;
    public $section;
    public $name;
    public $email;
    public $phone;
    public $notes;
    public $appointment_patient;
    public $message = false;
    public $message2 = false;

    public function mount()
    {
        $this->sections = Section::get();
        $this->doctors = collect();
    }

    public function render()
    {
        return view('livewire.appointments.create', [
            'sections' => Section::get(),
        ]);
    }

    public function updatedSection($section_id)
    {
        $this->resetErrorBag('section');
        if (!Section::whereKey($section_id)->exists()) {
            $this->doctors = collect();
            $this->doctor = null;
            return;
        }

        $this->doctors = Doctor::where('section_id', $section_id)->get();
        $this->doctor = null;
    }

    public function store()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9\s().-]{6,30}$/'],
            'section' => ['required', 'integer', 'exists:sections,id'],
            'doctor' => ['required', 'integer', 'exists:doctors,id'],
            'appointment_patient' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $doctorInfo = Doctor::whereKey($this->doctor)
            ->where('section_id', $this->section)
            ->first();

        if (!$doctorInfo) {
            $this->addError('doctor', trans('validation.exists'));
            return;
        }

        $appointmentCount = Appointment::where('doctor_id', $this->doctor)
            ->where('appointment_patient', $this->appointment_patient)
            ->count();

        if ($appointmentCount >= $doctorInfo->number_of_statements) {
            $this->message2 = true;
            return;
        }

        $appointment = new Appointment();
        $appointment->doctor_id = $doctorInfo->id;
        $appointment->section_id = $doctorInfo->section_id;
        $appointment->name = $this->name;
        $appointment->email = $this->email;
        $appointment->phone = $this->phone;
        $appointment->appointment_patient = $this->appointment_patient;
        $appointment->notes = $this->notes;
        $appointment->save();

        $this->message = true;
    }
}
