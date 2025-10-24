<div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
    <!-- Chat Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-3 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <i class="fas fa-comments text-white text-xl"></i>
            <h3 class="text-lg font-semibold text-white">Community Chat</h3>
            <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                {{ count($messages) }} messages
            </span>
        </div>
        <button wire:click="toggleChat" class="text-white hover:text-gray-200 transition-colors">
            <i class="fas {{ $showChat ? 'fa-chevron-down' : 'fa-chevron-up' }}"></i>
        </button>
    </div>

    @if($showChat)
        <!-- Chat Messages Area -->
        <div class="h-96 overflow-y-auto p-4 bg-gray-50 space-y-3" 
             id="chat-messages" 
             wire:poll.5s="loadMessages">
            @forelse($messages as $message)
                <div class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="flex items-start space-x-2 max-w-xs {{ $message->user_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }}">
                        <!-- User Avatar -->
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-sm font-semibold overflow-hidden">
                                @if($message->user->profile_photo)
                                    <img src="{{ Storage::url($message->user->profile_photo) }}" alt="{{ $message->user->name }}" class="h-full w-full object-cover">
                                @else
                                    {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                @endif
                            </div>
                        </div>
                        
                        <!-- Message Bubble -->
                        <div class="flex flex-col {{ $message->user_id === auth()->id() ? 'items-end' : 'items-start' }}">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-xs font-semibold text-gray-700">{{ $message->user->name }}</span>
                                <span class="text-xs text-gray-500">{{ $message->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="px-4 py-2 rounded-lg {{ $message->user_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-white text-gray-800 border border-gray-200' }} shadow-sm">
                                <p class="text-sm break-words">{{ $message->message }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center h-full text-center">
                    <i class="fas fa-comments text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg font-medium">No messages yet</p>
                    <p class="text-gray-400 text-sm mt-2">Be the first to start the conversation!</p>
                </div>
            @endforelse
        </div>

        <!-- Message Input Area -->
        <div class="border-t border-gray-200 p-4 bg-white">
            @auth
                @if($this->isMember())
                    <form wire:submit.prevent="sendMessage" class="flex space-x-2">
                        <div class="flex-1">
                            <input 
                                type="text" 
                                wire:model.defer="newMessage" 
                                placeholder="Type your message..." 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                maxlength="1000"
                            >
                            @error('newMessage') 
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                            @enderror
                        </div>
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold flex items-center space-x-2"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="sendMessage">
                                <i class="fas fa-paper-plane"></i>
                            </span>
                            <span wire:loading wire:target="sendMessage">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                            <span class="hidden sm:inline">Send</span>
                        </button>
                    </form>
                @else
                    <div class="text-center py-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <i class="fas fa-lock text-yellow-600 text-2xl mb-2"></i>
                        <p class="text-yellow-800 font-medium">You must be a member to participate in this chat</p>
                        <p class="text-yellow-600 text-sm mt-1">Join the community to start chatting!</p>
                    </div>
                @endif
            @else
                <div class="text-center py-4 bg-blue-50 rounded-lg border border-blue-200">
                    <i class="fas fa-sign-in-alt text-blue-600 text-2xl mb-2"></i>
                    <p class="text-blue-800 font-medium">Please log in to participate in the chat</p>
                    <a href="{{ route('login') }}" class="inline-block mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        <i class="fas fa-sign-in-alt mr-1"></i> Log In
                    </a>
                </div>
            @endauth
        </div>
    @endif

    @if(session()->has('error'))
        <div class="px-4 py-2 bg-red-100 text-red-700 text-sm border-t border-red-200">
            <i class="fas fa-exclamation-circle mr-1"></i>
            {{ session('error') }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    window.addEventListener('scroll-to-bottom', event => {
        const chatMessages = document.getElementById('chat-messages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });

    // Auto-scroll to bottom on initial load
    document.addEventListener('DOMContentLoaded', function() {
        const chatMessages = document.getElementById('chat-messages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });

    // Auto-scroll when new messages are loaded
    Livewire.hook('message.processed', (message, component) => {
        const chatMessages = document.getElementById('chat-messages');
        if (chatMessages) {
            setTimeout(() => {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }, 100);
        }
    });
</script>
@endpush
