<div>
    <div class="p-6 bg-white rounded shadow">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Events</h3>
            <a href="{{ route('events.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">Create Event</a>
        </div>

        @if (session('status'))
            <div class="mb-4 text-green-600">{{ session('status') }}</div>
        @endif

        <table class="w-full table-auto">
            <thead>
                <tr class="text-left">
                    <th class="pb-2">Title</th>
                    <th class="pb-2">Description</th>
                    <th class="pb-2">Location</th>
                    <th class="pb-2">Start</th>
                    <th class="pb-2">End</th>
                    <th class="pb-2">Actions</th>
                    <th class="pb-2">Communities</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                    <tr class="border-t align-top">
                            <td class="py-3 flex items-start gap-3">
                                @if($event->thumbnail)
                                    <img src="{{ Storage::url($event->thumbnail) }}" alt="" class="w-14 h-10 object-cover rounded" />
                                @else
                                    <div class="w-14 h-10 bg-gray-100 rounded"></div>
                                @endif
                                <div>
                                    <div class="font-semibold">{{ $event->title }}</div>
                                    @if($event->description)
                                        <div class="text-sm text-gray-600 mt-1">{{ \Illuminate\Support\Str::limit($event->description, 120) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3">{{ $event->description ? \Illuminate\Support\Str::limit($event->description, 80) : '-' }}</td>
                            <td class="py-3">{{ $event->location ?? '-' }}</td>
                            <td class="py-3">{{ $event->start_date }}</td>
                            <td class="py-3">{{ $event->end_date }}</td>
                            <td class="py-3 text-right">
                                @can('update', $event)
                                    <a href="{{ route('events.edit', $event->id) }}" class="px-2 py-1 bg-blue-600 text-white rounded mr-2">Edit</a>
                                @endcan

                                <button wire:click.prevent="confirmDelete('{{ $event->id }}')" class="px-2 py-1 bg-red-600 text-white rounded">Delete</button>
                            </td>
                            <td class="py-3">
                                @if($event->communities && $event->communities->count())
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($event->communities as $c)
                                            <a href="{{ route('communities.show', $c) }}" class="px-2 py-1 bg-gray-100 rounded text-sm hover:bg-gray-200 transition cursor-pointer">{{ $c->name }}</a>
                                        @endforeach
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $events->links() }}
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-data="{ open: @entangle('confirmingDeletion') }">
        <div x-show="open" x-cloak wire:ignore.self class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-6 w-96">
                <h4 class="text-lg font-bold mb-4">Confirm Delete</h4>
                <p class="mb-4">Are you sure you want to delete this event? This action cannot be undone.</p>
                <div class="text-right">
                    <button x-on:click="open = false" wire:click="cancelDelete" class="px-3 py-2 mr-2 bg-gray-200 rounded">Cancel</button>
                    <button wire:click="deleteConfirmed" class="px-3 py-2 bg-red-600 text-white rounded">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>