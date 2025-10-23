<div>
    <div class="p-6 bg-white rounded shadow">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Communities</h3>
            <a href="{{ route('community.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Create Community
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        @if($communities->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="pb-3 font-semibold">Cover</th>
                            <th class="pb-3 font-semibold">Name</th>
                            <th class="pb-3 font-semibold">Description</th>
                            <th class="pb-3 font-semibold">Associated Events</th>
                            <th class="pb-3 font-semibold">Members</th>
                            <th class="pb-3 font-semibold">Status</th>
                            <th class="pb-3 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($communities as $community)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3">
                                    @if($community->cover_image)
                                        <img src="{{ Storage::url($community->cover_image) }}" alt="Cover" class="w-16 h-12 object-cover rounded border" />
                                    @else
                                        <div class="w-16 h-12 bg-gray-100 rounded border flex items-center justify-center text-gray-400">N/A</div>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <div class="font-semibold text-gray-800">{{ $community->name }}</div>
                                    @if($community->slug)
                                        <div class="text-sm text-gray-500">{{ $community->slug }}</div>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <div class="text-sm text-gray-600">
                                        {{ $community->description ? Str::limit($community->description, 100) : '-' }}
                                    </div>
                                </td>
                                    <td class="py-3">
                                        @if(!empty($community->events) && $community->events->count())
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($community->events as $ev)
                                                    <span class="px-2 py-1 bg-gray-100 rounded text-sm">{{ $ev->title }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                <td class="py-3">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">
                                        {{ $community->members_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    @if($community->is_public)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">
                                            <i class="fas fa-globe-americas mr-1"></i>Public
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-sm">
                                            <i class="fas fa-lock mr-1"></i>Private
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('communities.show', $community) }}" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm mr-2">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </a>
                                    <a href="{{ route('community.edit', $community->id) }}" class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm mr-2">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <button wire:click.prevent="confirmDelete('{{ $community->id }}')" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $communities->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-users text-6xl text-slate-300 mb-4"></i>
                <p class="text-slate-600 mb-4">No communities found.</p>
                <a href="{{ route('community.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 inline-block">
                    <i class="fas fa-plus mr-2"></i>Create Your First Community
                </a>
            </div>
        @endif
    </div>


    <!-- Delete Confirmation Modal -->
    <div x-data="{ open: @entangle('confirmingDeletion') }">
        <div x-show="open" x-cloak wire:ignore.self class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-6 w-96">
                <h4 class="text-lg font-bold mb-4">Confirm Delete</h4>
                <p class="mb-4">Are you sure you want to delete this community? This action cannot be undone.</p>
                <div class="text-right">
                    <button x-on:click="open = false" wire:click="cancelDelete" class="px-3 py-2 mr-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                    <button wire:click="deleteConfirmed" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>