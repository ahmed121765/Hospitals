<div>
    <div class="main-chat-list" id="ChatList">
        @foreach($conversations as $conversation)
            @php $receiver = $this->getUsers($conversation); @endphp
            <div class="media new" wire:click="chatUserSelected({{ $conversation->id }}, {{ $receiver?->id ?? 'null' }})">
                <div class="media-body">
                    <div class="media-contact-name">
                        <span>{{ $receiver?->name }}</span>
                        <span>{{ optional($conversation->messages->last()?->created_at)->shortAbsoluteDiffForHumans() }}</span>
                    </div>
                    <p>{{ $conversation->messages->last()?->body }}</p>
                </div>
            </div>
        @endforeach
    </div><!-- main-chat-list -->
</div>