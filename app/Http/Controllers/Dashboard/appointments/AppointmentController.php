<?php

namespace App\Http\Controllers\Dashboard\appointments;

use App\Http\Controllers\Controller;
use App\Mail\AppointmentConfirmation;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    public function index()
    {
        $unconfirmedType = "\u{00D8}\u{00BA}\u{00D9}\u{0160}\u{00D8}\u{00B1} \u{00D9}\u{2026}\u{00D8}\u{00A4}\u{00D9}\u{0192}\u{00D8}\u{00AF}";
        $appointments = Appointment::where('type', $unconfirmedType)->get();
        return view('Dashboard.appointments.index', compact('appointments'));
    }

    public function index2()
    {
        $confirmedType = "\u{00D9}\u{2026}\u{00D8}\u{00A4}\u{00D9}\u{0192}\u{00D8}\u{00AF}";
        $appointments = Appointment::where('type', $confirmedType)->get();
        return view('Dashboard.appointments.index2', compact('appointments'));
    }

    public function approval(Request $request, $id)
    {
        validator(['id' => $id], ['id' => ['required', 'integer', 'exists:appointments,id']])->validate();
        $request->validate([
            'id' => ['sometimes', 'integer', 'same:' . $id],
            'appointment' => ['required', 'date_format:Y-m-d H:i', 'after_or_equal:today'],
        ]);

        $unconfirmedType = "\u{00D8}\u{00BA}\u{00D9}\u{0160}\u{00D8}\u{00B1} \u{00D9}\u{2026}\u{00D8}\u{00A4}\u{00D9}\u{0192}\u{00D8}\u{00AF}";
        $appointment = Appointment::where('type', $unconfirmedType)->findOrFail($id);
        $appointment->update([
            'type' => "\u{00D9}\u{2026}\u{00D8}\u{00A4}\u{00D9}\u{0192}\u{00D8}\u{00AF}",
            'appointment' => $request->appointment,
        ]);

        Mail::to($appointment->email)->send(new AppointmentConfirmation($appointment->name, $appointment->appointment));

        session()->flash('add');
        return back();
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => ['required', 'integer', 'exists:appointments,id']]);
        try {
            Appointment::destroy($request->id);
            session()->flash('delete');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
