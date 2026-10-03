<?php

namespace App\Http\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Doctor;
use App\Models\Message;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Createchat extends Component
{
    public $users;
    public $auth_email;

    public function mount()
    {
        if (Auth::guard('patient')->check()) {
            $this->auth_email = Auth::guard('patient')->user()->email;
        } elseif (Auth::guard('doctor')->check()) {
            $this->auth_email = Auth::guard('doctor')->user()->email;
        } else {
            abort(403);
        }
    }

    public function createConversation($receiver_email)
    {

        validator(['receiver_email' => $receiver_email], ['receiver_email' => ['required', 'email']])->validate();
        if (Auth::guard('patient')->check()) {
            $sender = Auth::guard('patient')->user();
            $receiverExists = Doctor::where('email', $receiver_email)->exists();
        } elseif (Auth::guard('doctor')->check()) {
            $sender = Auth::guard('doctor')->user();
            $receiverExists = Patient::where('email', $receiver_email)->exists();
        } else {
            abort(403);
        }
        if (!$receiverExists || $sender->email === $receiver_email) {
            $this->addError('receiver_email', trans('validation.exists', [
                'attribute' => trans('validation.attributes.receiver_email'),
            ]));
            return;
        }

        $chek_Conversation = Conversation::chekConversation($sender->email, $receiver_email)->get();
        if ($chek_Conversation->isEmpty()) {
            DB::beginTransaction();
            try {
                // $createConversation
                $createConversation = Conversation::create([
                    'sender_email' => $sender->email,
                    'receiver_email' => $receiver_email,
                    'last_time_message' => null,
                ]);
                // create message
                Message::create([
                    'conversation_id' => $createConversation->id,
                    'sender_email' => $sender->email,
                    'receiver_email' => $receiver_email,
                    'body' => 'السلام عليكم',
                ]);
                DB::commit();
                $this->emitSelf('render');
            } catch (\Exception $e) {
                DB::rollBack();
            }
        } else {

            $this->emitSelf('render');
        }

    }

    public function render()
    {
        if (Auth::guard('patient')->check()) {
            $this->users = Doctor::all();
        } else {
            $this->users = Patient::all();
        }
        return view('livewire.chat.createchat')->extends('Dashboard.layouts.master');
    }
}
