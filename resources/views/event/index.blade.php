@include('layouts.header')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid py-4">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">📅 My Events</h2>
                            <p class="text-muted mb-0">Manage your scheduled events and reminders</p>
                        </div>
                        <a href="{{ route('dashboard.events.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Event
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-pills nav-fill bg-light rounded p-1">
                <li class="nav-item">
                    <a class="nav-link active" href="#all" data-bs-toggle="pill">
                        <i class="fas fa-calendar me-2"></i>All Events
                        <span class="badge bg-secondary ms-2">{{ $events->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#daily" data-bs-toggle="pill">
                        <i class="fas fa-sun me-2"></i>Daily
                        <span class="badge bg-warning ms-2">{{ $events->where('repeat_type', 'daily')->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#weekly" data-bs-toggle="pill">
                        <i class="fas fa-calendar-week me-2"></i>Weekly
                        <span class="badge bg-info ms-2">{{ $events->where('repeat_type', 'weekly')->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#monthly" data-bs-toggle="pill">
                        <i class="fas fa-calendar-alt me-2"></i>Monthly
                        <span class="badge bg-success ms-2">{{ $events->where('repeat_type', 'monthly')->count() }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

            <!-- Events Grid -->
    <div class="tab-content">
        <!-- All Events Tab -->
        <div class="tab-pane fade show active" id="all">
            @if($events->count() > 0)
                <div class="row">
                    @foreach($events as $event)
                        <div class="col-lg-4 col-md-6 mb-4 event-card" data-type="{{ $event->repeat_type }}">
                            <div class="card h-100 shadow-sm border-0 hover-shadow">
                                <!-- Card Header with Color -->
                                <div class="card-header border-0 d-flex justify-content-between align-items-center"
                                     style="background: linear-gradient(135deg, {{ $event->colors ?? '#6c757d' }}, {{ $event->colors ?? '#6c757d' }}cc);">
                                    <div class="text-white">
                                        <span class="badge bg-white text-dark me-2">
                                            @if($event->repeat_type == 'daily')
                                                <i class="fas fa-sun"></i> Daily
                                            @elseif($event->repeat_type == 'weekly')
                                                <i class="fas fa-calendar-week"></i> Weekly
                                            @else
                                                <i class="fas fa-calendar-alt"></i> Monthly
                                            @endif
                                        </span>
                                        <small class="opacity-75">{{ ucfirst($event->status) }}</small>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-white p-0" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('dashboard.events.edit', $event->id) }}">
                                                <i class="fas fa-edit me-2"></i>Edit
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('dashboard.events.destroy', $event->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="dropdown-item text-danger" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash me-2"></i>Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="card-body">
                                    <h5 class="card-title mb-3">{{ $event->title }}</h5>
                                    <p class="card-text text-muted mb-3">{{ Str::limit($event->description, 100) }}</p>

                                    <!-- Schedule Info -->
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-2"><i class="fas fa-clock me-2"></i>Schedule:</h6>

                                        @if($event->repeat_type == 'weekly' && $event->repeat_days_moth)
                                            @php
                                                $weeklyDays = json_decode($event->repeat_days_moth, true) ?? [];
                                            @endphp
                                            <div class="d-flex flex-wrap gap-1 mb-2">
                                                @foreach($weeklyDays as $day)
                                                    <span class="badge bg-light text-dark">{{ ucfirst($day) }}</span>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if($event->repeat_type == 'monthly' && $event->repeat_days_moth)
                                            @php
                                                $monthlyDays = json_decode($event->repeat_days_moth, true) ?? [];
                                            @endphp
                                            <div class="d-flex flex-wrap gap-1 mb-2">
                                                @foreach($monthlyDays as $day)
                                                    <span class="badge bg-light text-dark">{{ $day }}</span>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if($event->start_date)
                                            @php
                                                $eventTimes = json_decode($event->start_date, true) ?? [];
                                            @endphp
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($eventTimes as $time)
                                                    <span class="badge bg-primary">{{ $time }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Card Footer -->
                                <div class="card-footer bg-transparent border-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-plus me-1"></i>
                                            Created {{ $event->created_at->diffForHumans() }}
                                        </small>
                                        @if($event->status == 'active')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-pause-circle me-1"></i>Inactive
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-calendar-times fa-5x text-muted opacity-25"></i>
                    </div>
                    <h4 class="text-muted mb-3">No Events Found</h4>
                    <p class="text-muted mb-4">You haven't created any events yet. Start by adding your first event!</p>
                    <a href="{{ route('dashboard.events.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Create Your First Event
                    </a>
                </div>
            @endif
        </div>

        <!-- Daily Events Tab -->
        <div class="tab-pane fade" id="daily">
            <div class="row">
                @foreach($events->where('repeat_type', 'daily') as $event)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <!-- Same card structure as above -->
                        <div class="card h-100 shadow-sm border-0 hover-shadow">
                            <div class="card-header border-0"
                                 style="background: linear-gradient(135deg, {{ $event->colors ?? '#ffc107' }}, {{ $event->colors ?? '#ffc107' }}cc);">
                                <span class="badge bg-white text-dark">
                                    <i class="fas fa-sun"></i> Daily
                                </span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $event->title }}</h5>
                                <p class="card-text text-muted">{{ Str::limit($event->description, 100) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Weekly Events Tab -->
        <div class="tab-pane fade" id="weekly">
            <div class="row">
                @foreach($events->where('repeat_type', 'weekly') as $event)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <!-- Same card structure -->
                        <div class="card h-100 shadow-sm border-0 hover-shadow">
                            <div class="card-header border-0"
                                 style="background: linear-gradient(135deg, {{ $event->colors ?? '#17a2b8' }}, {{ $event->colors ?? '#17a2b8' }}cc);">
                                <span class="badge bg-white text-dark">
                                    <i class="fas fa-calendar-week"></i> Weekly
                                </span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $event->title }}</h5>
                                <p class="card-text text-muted">{{ Str::limit($event->description, 100) }}</p>
                                @if($event->repeat_days_moth)
                                    @php
                                        $days = json_decode($event->repeat_days_moth, true) ?? [];
                                    @endphp
                                    <div class="mb-2">
                                        @foreach($days as $day)
                                            <span class="badge bg-light text-dark me-1">{{ ucfirst($day) }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Monthly Events Tab -->
        <div class="tab-pane fade" id="monthly">
            <div class="row">
                @foreach($events->where('repeat_type', 'monthly') as $event)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <!-- Same card structure -->
                        <div class="card h-100 shadow-sm border-0 hover-shadow">
                            <div class="card-header border-0"
                                 style="background: linear-gradient(135deg, {{ $event->colors ?? '#28a745' }}, {{ $event->colors ?? '#28a745' }}cc);">
                                <span class="badge bg-white text-dark">
                                    <i class="fas fa-calendar-alt"></i> Monthly
                                </span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $event->title }}</h5>
                                <p class="card-text text-muted">{{ Str::limit($event->description, 100) }}</p>
                                @if($event->repeat_days_moth)
                                    @php
                                        $days = json_decode($event->repeat_days_moth, true) ?? [];
                                    @endphp
                                    <div class="mb-2">
                                        @foreach($days as $day)
                                            <span class="badge bg-light text-dark me-1">{{ $day }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
  

<style>
<style>
    .hover-shadow {
        transition: all 0.3s ease;
    }

    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .nav-pills .nav-link {
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .nav-pills .nav-link:hover {
        background-color: rgba(0, 123, 255, 0.1);
    }

    .card-header {
        border-radius: 0.375rem 0.375rem 0 0 !important;
    }

    .event-card {
        transition: opacity 0.3s ease;
    }

    .badge {
        font-size: 0.75em;
    }
</style>
</style>

<script>
<script>
    // Filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const filterTabs = document.querySelectorAll('[data-bs-toggle="pill"]');
        const eventCards = document.querySelectorAll('.event-card');

        filterTabs.forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(e) {
                const targetType = e.target.getAttribute('href').substring(1);

                if (targetType === 'all') {
                    eventCards.forEach(card => {
                        card.style.display = 'block';
                        card.classList.remove('d-none');
                    });
                } else {
                    eventCards.forEach(card => {
                        const cardType = card.getAttribute('data-type');
                        if (cardType === targetType) {
                            card.style.display = 'block';
                            card.classList.remove('d-none');
                        } else {
                            card.style.display = 'none';
                            card.classList.add('d-none');
                        }
                    });
                }
            });
        });
    });

    // Success message auto-hide
    @if(session('success'))
        setTimeout(function() {
            const alert = document.querySelector('.alert-success');
            if (alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    @endif
</script>

@include('layouts.footer')
