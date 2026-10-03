<?php

namespace App\Http\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Doctor;
use App\Models\Message;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SendMessage extends Component
{
    public $selected_conversation;
    public $receiver;
    public $body = '';

    protected $listeners = [
        'updateMessage',
        'updateMessage2',
    ];

    public function updateMessage(Conversation $conversation, Doctor $receiver)
    {
        $this->selected_conversation = $conversation;
        $this->receiver = $receiver;
    }

    public function updateMessage2(Conversation $conversation, Patient $receiver)
    {
        $this->selected_conversation = $conversation;
        $this->receiver = $receiver;
    }

    public function sendMessage()
    {
        $this->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        if (!$this->selected_conversation) {
            return;
        }

        if (Auth::guard('doctor')->check()) {
            $senderEmail = Auth::guard('doctor')->user()->email;
        } elseif (Auth::guard('patient')->check()) {
            $senderEmail = Auth::guard('patient')->user()->email;
        } else {
            $senderEmail = null;
        }

        if (!$senderEmail) {
            return;
        }

        $conversation = Conversation::findOrFail($this->selected_conversation->id);
        if (!in_array($senderEmail, [$conversation->sender_email, $conversation->receiver_email], true)) {
            abort(403);
        }

        $receiverEmail = $conversation->sender_email === $senderEmail
            ? $conversation->receiver_email
            : $conversation->sender_email;

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_email' => $senderEmail,
            'receiver_email' => $receiverEmail,
            'body' => $this->body,
        ]);

        $this->body = '';
        $this->emitTo('chat.chatbox', 'pushMessage', $message->id);
    }

    public function render()
    {
        return view('livewire.chat.send-message');
    }
}
