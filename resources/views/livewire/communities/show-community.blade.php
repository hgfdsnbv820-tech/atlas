<div class="max-w-7xl mx-auto">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Header Section -->
        <div class="p-6 bg-gray-50 border-b">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $community->name }}</h1>
                    @if($community->is_public)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                            Public Community
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mt-2">
                            Private Community
                        </span>
                    @endif
                </div>
                @if(auth()->check() && auth()->user()->can('update', $community))
                    <a href="{{ route('community.edit', $community->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Edit Community
                    </a>
                @endif
            </div>
            @if($community->description)
                <p class="mt-4 text-gray-600">{{ $community->description }}</p>
            @endif
        </div>

        <!-- Stats Section -->
        <div class="grid grid-cols-3 gap-px bg-gray-200">
            <dl class="bg-white p-4">
                <dt class="text-base font-normal text-gray-900">Members</dt>
                <dd class="mt-1 flex justify-between items-baseline md:block lg:flex">
                    <div class="flex items-baseline text-2xl font-semibold text-indigo-600">
                        {{ $community->communityMembers->count() }}
                    </div>
                </dd>
            </dl>

            <dl class="bg-white p-4">
                <dt class="text-base font-normal text-gray-900">Events</dt>
                <dd class="mt-1 flex justify-between items-baseline md:block lg:flex">
                    <div class="flex items-baseline text-2xl font-semibold text-indigo-600">
                        {{ $community->events->count() }}
                    </div>
                </dd>
            </dl>

            <dl class="bg-white p-4">
                <dt class="text-base font-normal text-gray-900">Created</dt>
                <dd class="mt-1 flex justify-between items-baseline md:block lg:flex">
                    <div class="flex items-baseline text-2xl font-semibold text-indigo-600">
                        {{ $community->created_at->diffForHumans() }}
                    </div>
                </dd>
            </dl>
        </div>

        <!-- Content Section -->
        <div class="p-6">
            <!-- Chat Section -->
            <div class="mb-8">
                @livewire('community-chat', ['community' => $community])
            </div>

            <!-- Members Section -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Members</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @forelse($members as $member)
                        <div class="flex flex-col items-center space-y-2">
                            <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                @if($member->profile_photo)
                                    <img src="{{ Storage::url($member->profile_photo) }}" alt="{{ $member->name }}" class="h-full w-full object-cover">
                                @else
                                    <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                @endif
                            </div>
                            <span class="text-sm text-gray-900">{{ $member->name }}</span>
                        </div>
                    @empty
                        <p class="col-span-full text-gray-500 text-center py-4">No members yet</p>
                    @endforelse
                </div>
            </div>

            <!-- Events Section -->
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Events</h2>
                <div class="space-y-4">
                    @forelse($community->events()->orderBy('start_date', 'asc')->get() as $event)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="sm:flex sm:justify-between sm:items-start">
                                <div>
                                    <h3 class="text-base font-medium text-gray-900">{{ $event->title }}</h3>
                                    @if($event->description)
                                        <p class="mt-1 text-sm text-gray-500">{{ Str::limit($event->description, 100) }}</p>
                                    @endif
                                    <div class="mt-2 text-sm text-gray-500 space-x-2">
                                        <span>{{ $event->start_date ? $event->start_date->format('M d, Y h:i A') : 'Date TBA' }}</span>
                                        @if($event->location)
                                            <span>•</span>
                                            <span>{{ $event->location }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2 sm:mt-0 sm:ml-4">
                                    <a href="{{ route('events.show', $event->id) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No events scheduled</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
