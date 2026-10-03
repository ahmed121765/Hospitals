<?php

namespace App\Http\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Doctor;
use App\Models\Message;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chatbox extends Component
{

    public $receiver;
    public $selected_conversation;
    public $receviverUser;
    public $messages;
    public $auth_email;
    public $auth_id;
    public $event_name;
    public $chat_page;

    //protected $listeners = ['load_conversationDoctor', 'load_conversationPatient', 'pushMessage'];

    public function mount()
    {
        if (Auth::guard('patient')->check()) {
            $this->auth_email = Auth::guard('patient')->user()->email;
            $this->auth_id = Auth::guard('patient')->user()->id;
        } else {
            $this->auth_email = Auth::guard('doctor')->user()->email;
            $this->auth_id = Auth::guard('doctor')->user()->id;
        }

    }

    public function getListeners()
    {
        if (Auth::guard('patient')->check()) {
            $auth_id = Auth::guard('patient')->user()->id;
            $this->event_name = "MassageSent2";
            $this->chat_page = "chat2";

        } else {
            $auth_id = Auth::guard('doctor')->user()->id;
            $this->event_name = "MassageSent";
            $this->chat_page = "chat";
        }

        return [
            "echo-private:$this->chat_page.{$auth_id},$this->event_name" => 'broadcastMassage',
            'load_conversationPatient',
            'load_conversationDoctor',
            'pushMessage'
        ];
    }

    public function broadcastMassage($event)
    {
        validator(['message_id' => $event['message'] ?? null], ['message_id' => ['required', 'integer', 'exists:messages,id']])->validate();
        $broadcastMessage = Message::find($event['message']);
        if (!$broadcastMessage) {
            return;
        }
        $broadcastMessage->read = 1;
        $this->pushMessage($broadcastMessage->id);
    }

    public function pushMessage($messageId)
    {
        validator(['message_id' => $messageId], ['message_id' => ['required', 'integer', 'exists:messages,id']])->validate();
        $newMessage = Message::with('conversation')->findOrFail($messageId);
        if (!$newMessage->conversation || !in_array($this->currentAuthEmail(), [$newMessage->conversation->sender_email, $newMessage->conversation->receiver_email], true)) {
            abort(403);
        }
        if (!$this->messages) {
            $this->messages = collect();
        }
        $this->messages->push($newMessage);
    }


    public function load_conversationDoctor(Conversation $conversation, Doctor $receiver)
    {
        $this->authorizeConversation($conversation, $receiver);
        $this->selected_conversation = $conversation;
        $this->receviverUser = $receiver;
        $this->messages = Message::where('conversation_id', $this->selected_conversation->id)->get();
    }

    public function load_conversationPatient(Conversation $conversation, Patient $receiver)
    {
        $this->authorizeConversation($conversation, $receiver);

        $this->selected_conversation = $conversation;
        $this->receviverUser = $receiver;
        $this->messages = Message::where('conversation_id', $this->selected_conversation->id)->get();
    }

    private function authorizeConversation(Conversation $conversation, $receiver): void
    {
        $authEmail = $this->currentAuthEmail();
        if (!in_array($authEmail, [$conversation->sender_email, $conversation->receiver_email], true)) {
            abort(403);
        }
        $otherEmail = $conversation->sender_email === $authEmail
            ? $conversation->receiver_email
            : $conversation->sender_email;
        if ($receiver->email !== $otherEmail) {
            abort(403);
        }
    }

    private function currentAuthEmail(): string
    {
        if (Auth::guard('patient')->check()) {
            return Auth::guard('patient')->user()->email;
        }
        if (Auth::guard('doctor')->check()) {
            return Auth::guard('doctor')->user()->email;
        }
        abort(403);
    }


    public function render()
    {
        return view('livewire.chat.chatbox');
    }
}
