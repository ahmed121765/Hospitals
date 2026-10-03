<?php

namespace App\Http\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chatlist extends Component
{

    public $conversations;
    public $auth_email;
    public $auth_guard;
    public $receviverUser;
    public $selected_conversation;
    protected $listeners = ['chatUserSelected', 'refresh' => '$refresh'];

    public function mount()
    {
        if (Auth::guard('patient')->check()) {
            $this->auth_guard = 'patient';
            $this->auth_email = Auth::guard('patient')->user()->email;
        } elseif (Auth::guard('doctor')->check()) {
            $this->auth_guard = 'doctor';
            $this->auth_email = Auth::guard('doctor')->user()->email;
        }
    }

    public function getUsers(Conversation $conversation, $request = null)
    {
        if ($this->auth_guard === 'doctor') {
            $otherUser = $conversation->sender_email === $this->auth_email
                ? Patient::firstWhere('email', $conversation->receiver_email)
                : Patient::firstWhere('email', $conversation->sender_email);
        } else {
            $otherUser = $conversation->sender_email === $this->auth_email
                ? Doctor::firstWhere('email', $conversation->receiver_email)
                : Doctor::firstWhere('email', $conversation->sender_email);
        }

        if (!$otherUser) {
            return null;
        }

        return $request ? $otherUser->{$request} : $otherUser;
    }


    public function chatUserSelected($conversationId, $receiver_id)
    {
        validator(
            ['conversation_id' => $conversationId, 'receiver_id' => $receiver_id],
            ['conversation_id' => ['required', 'integer', 'exists:conversations,id'], 'receiver_id' => ['required', 'integer']]
        )->validate();
        $authEmail = Auth::guard('patient')->check()
            ? Auth::guard('patient')->user()->email
            : (Auth::guard('doctor')->user()->email ?? null);
        if (!$authEmail) {
            abort(403);
        }
        $conversation = Conversation::find($conversationId);
        if (!$conversation || !$receiver_id) {
            return;
        }

        if (!in_array($authEmail, [$conversation->sender_email, $conversation->receiver_email], true)) {
            abort(403);
        }

        $this->selected_conversation = $conversation;

        if ($this->auth_guard === 'doctor') {
            $this->receviverUser = Patient::find($receiver_id);
        } else {
            $this->receviverUser = Doctor::find($receiver_id);
        }

        if (!$this->receviverUser) {
            return;
        }
        $otherEmail = $conversation->sender_email === $authEmail
            ? $conversation->receiver_email
            : $conversation->sender_email;
        if ($this->receviverUser->email !== $otherEmail) {
            abort(403);
        }

        if ($this->auth_guard === 'patient') {
            $this->emitTo('chat.chatbox', 'load_conversationDoctor', $this->selected_conversation, $this->receviverUser);
            $this->emitTo('chat.send-message', 'updateMessage', $this->selected_conversation, $this->receviverUser);
        } else {
            $this->emitTo('chat.chatbox', 'load_conversationPatient', $this->selected_conversation, $this->receviverUser);
            $this->emitTo('chat.send-message', 'updateMessage2', $this->selected_conversation, $this->receviverUser);
        }
    }


    public function render()
    {
        $this->auth_email = Auth::guard('patient')->check()
            ? Auth::guard('patient')->user()->email
            : Auth::guard('doctor')->user()->email;
        $this->conversations = Conversation::where('sender_email', $this->auth_email)->orwhere('receiver_email', $this->auth_email)
            ->orderBy('created_at', 'DESC')
            ->get();
        return view('livewire.chat.chatlist');
    }
}
