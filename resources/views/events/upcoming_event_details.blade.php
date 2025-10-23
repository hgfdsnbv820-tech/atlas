@extends('layouts.base')

@push('styles')
    <style>
        .event-detail-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .event-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .event-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
        }
        
        .event-meta {
            display: flex;
            justify-content: center;
            gap: 2rem;
            color: #6b7280;
            margin-bottom: 1.5rem;
        }
        
        .event-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .event-meta-item i {
            color: #4f46e5;
        }
        
        .event-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 0.75rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .event-content {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 3rem;
        }
        
        .event-description {
            line-height: 1.7;
            color: #4b5563;
            font-size: 1.1rem;
        }
        
        .event-sidebar {
            background: #f9fafb;
            padding: 2rem;
            border-radius: 0.75rem;
            height: fit-content;
        }
        
        .sidebar-section {
            margin-bottom: 2rem;
        }
        
        .sidebar-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .community-badge {
            display: inline-flex;
            align-items: center;
            background: #eef2ff;
            color: #4f46e5;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: 500;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            color: #4f46e5;
            font-weight: 500;
            margin-bottom: 1.5rem;
            transition: color 0.2s;
        }
        
        .back-button:hover {
            color: #4338ca;
        }
        
        .back-button i {
            margin-right: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .event-content {
                grid-template-columns: 1fr;
            }
            
            .event-title {
                font-size: 2rem;
            }
            
            .event-meta {
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="event-detail-container">
        <a href="{{ url()->previous() }}" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Events
        </a>
        
        <div class="event-header">
            <h1 class="event-title">{{ $event->title }}</h1>
            <div class="event-meta">
                <div class="event-meta-item">
                    <i class="far fa-calendar-alt"></i>
                    <span>{{ $event->start_date->format('F j, Y') }}</span>
                </div>
                <div class="event-meta-item">
                    <i class="far fa-clock"></i>
                    <span>{{ $event->start_date->format('g:i A') }} - {{ $event->end_date->format('g:i A') }}</span>
                </div>
                @if($event->location)
                <div class="event-meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $event->location }}</span>
                </div>
                @endif
            </div>
        </div>
        
        @if($event->thumbnail)
            <img src="{{ Storage::url($event->thumbnail) }}" alt="{{ $event->title }}" class="event-image">
        @endif
        
        <div class="event-content">
            <div class="event-main">
                <div class="event-description">
                    {!! nl2br(e($event->description)) !!}
                </div>
            </div>
            
            <div class="event-sidebar">
                @if($event->communities->isNotEmpty())
                <div class="sidebar-section">
                    <h3 class="sidebar-title">Hosted by</h3>
                    <div class="communities-list">
                        @foreach($event->communities as $community)
                            <span class="community-badge">
                                <i class="fas fa-users"></i>
                                {{ $community->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <div class="sidebar-section">
                    <h3 class="sidebar-title">Event Details</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="text-sm font-medium text-gray-500">Date</div>
                            <div>{{ $event->start_date->format('l, F j, Y') }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">Time</div>
                            <div>{{ $event->start_date->format('g:i A') }} - {{ $event->end_date->format('g:i A') }}</div>
                        </div>
                        @if($event->location)
                        <div>
                            <div class="text-sm font-medium text-gray-500">Location</div>
                            <div>{{ $event->location }}</div>
                        </div>
                        @endif
                        @if($event->max_attendees)
                        <div>
                            <div class="text-sm font-medium text-gray-500">Capacity</div>
                            <div>{{ $event->attendees_count ?? 0 }} / {{ $event->max_attendees }} attendees</div>
                        </div>
                        @endif
                    </div>
                </div>
                
                @if($event->registration_link)
                <div class="mt-6">
                    <a href="{{ $event->registration_link }}" target="_blank" 
                       class="w-full flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Register Now
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
