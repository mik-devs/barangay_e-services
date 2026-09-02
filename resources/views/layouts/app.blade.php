<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'BarangayE-Portal')) - Resident Portal</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Custom App CSS / Styles -->
    <style>
    html, body {
        height: 100%;
        margin: 0;
        overflow: hidden; 
        font-family: 'Inter', sans-serif;
        background-color: #f8f9fa;
    }

    .dashboard-wrapper {
        display: flex;
        height: 100vh;
        width: 100vw;
        overflow: hidden;
    }
    
    .sidebar {
        width: 280px;
        min-width: 280px;
        height: 100vh;
        position: relative;
        z-index: 1040;
        transition: all 0.3s ease-in-out;
        background-color: #ffffff;
        border-right: 1px solid rgba(0, 0, 0, 0.075);
    }

    .main-content {
        flex-grow: 1;
        min-width: 0;
        height: 100vh;
        overflow-y: auto; 
    }

    .hover-bg-light:hover {
        background-color: rgba(13, 110, 253, 0.05) !important;
        color: #0d6efd !important;
    }

    .hover-bg-light:hover i {
        color: #0d6efd !important;
    }

    .hover-bg-danger-light:hover {
        background-color: rgba(220, 53, 69, 0.08) !important;
    }

    .x-small {
        font-size: 0.75rem;
    }

    @media (max-width: 991.98px) {
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: -280px;
        }
        .sidebar.show {
            left: 0;
        }
    }
</style>

    @stack('styles')
</head>
<body class="bg-light text-dark antialiased">

    <div class="dashboard-wrapper">
        <!-- Sidebar Navigation -->
        <nav id="sidebar" class="sidebar d-flex flex-column flex-shrink-0 p-3 shadow-sm">
            <!-- Sidebar Brand Header -->
            <div class="d-flex align-items-center justify-content-between mb-3 px-2 pt-2">
                <a href="{{ route('resident.dashboard') }}" class="d-flex align-items-center text-decoration-none text-dark">
                    <div class="bg-primary text-white p-2 rounded-3 me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-house-heart-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 lh-1">Barangay E-Portal</h6>
                        <span class="text-muted x-small">Resident Portal</span>
                    </div>
                </a>
            </div>

            <hr class="text-muted opacity-25 my-2">

            <!-- Sidebar Links / Navigation Items (Resident Focus) -->
            <div class="flex-grow-1 overflow-y-auto py-2">
                <ul class="nav flex-column gap-1">
                    <li class="nav-item">
                        <a href="{{ route('resident.dashboard') }}" class="nav-link d-flex align-items-center px-3 py-2.5 rounded-3 fw-semibold {{ request()->routeIs('resident.dashboard') ? 'text-primary bg-primary bg-opacity-10 active' : 'text-dark hover-bg-light' }}">
                            <i class="bi bi-grid-1x2-fill fs-5 me-3 {{ request()->routeIs('resident.dashboard') ? 'text-primary' : 'text-muted' }}"></i> Dashboard
                        </a>
                    </li>

                    <!-- Added Digital ID Link Here -->
                    <li class="nav-item">
                        <a href="{{ route('resident.digital-id') }}" class="nav-link d-flex align-items-center px-3 py-2.5 rounded-3 fw-medium {{ request()->routeIs('resident.digital-id') ? 'text-primary bg-primary bg-opacity-10 active' : 'text-dark hover-bg-light' }}">
                            <i class="bi bi-person-badge fs-5 me-3 {{ request()->routeIs('resident.digital-id') ? 'text-primary' : 'text-muted' }}"></i> Digital ID
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('resident.documents.create') }}" class="nav-link d-flex align-items-center px-3 py-2.5 rounded-3 fw-medium {{ request()->routeIs('resident.documents.create') ? 'text-primary bg-primary bg-opacity-10 active' : 'text-dark hover-bg-light' }}">
                            <i class="bi bi-file-earmark-plus fs-5 me-3 text-muted"></i> Request Document
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('resident.documents.index') }}" class="nav-link d-flex align-items-center px-3 py-2.5 rounded-3 fw-medium {{ request()->routeIs('resident.documents.index') ? 'text-primary bg-primary bg-opacity-10 active' : 'text-dark hover-bg-light' }}">
                            <i class="bi bi-file-text fs-5 me-3 text-muted"></i> My Requests
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('resident.announcements.index') }}" class="nav-link d-flex align-items-center px-3 py-2.5 rounded-3 fw-medium {{ request()->routeIs('resident.announcements.*') ? 'text-primary bg-primary bg-opacity-10 active' : 'text-dark hover-bg-light' }}">
                            <i class="bi bi-megaphone fs-5 me-3 text-muted"></i> Announcements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('resident.incidents.index') }}" class="nav-link d-flex align-items-center px-3 py-2.5 rounded-3 fw-medium {{ request()->routeIs('resident.incidents.*') ? 'text-primary bg-primary bg-opacity-10 active' : 'text-dark hover-bg-light' }}">
                            <i class="bi bi-exclamation-triangle fs-5 me-3 text-muted"></i> Incident Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('resident.bookings.index') }}" class="nav-link d-flex align-items-center px-3 py-2.5 rounded-3 fw-medium {{ request()->routeIs('resident.bookings.*') ? 'text-primary bg-primary bg-opacity-10 active' : 'text-dark hover-bg-light' }}">
                            <i class="bi bi-calendar-check fs-5 me-3 text-muted"></i> Facility Booking
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('resident.hotlines.directory') }}" class="nav-link d-flex align-items-center px-3 py-2.5 rounded-3 fw-medium {{ request()->routeIs('resident.hotlines.*') ? 'text-primary bg-primary bg-opacity-10 active' : 'text-dark hover-bg-light' }}">
                            <i class="bi bi-telephone-outbound fs-5 me-3 text-muted"></i> Emergency Hotlines
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('resident.payments.index') }}" class="nav-link d-flex align-items-center px-3 py-2.5 rounded-3 fw-medium {{ request()->routeIs('resident.payments.*') ? 'text-primary bg-primary bg-opacity-10 active' : 'text-dark hover-bg-light' }}">
                            <i class="bi bi-wallet2 fs-5 me-3 text-muted"></i> My Payments
                        </a>
                    </li>
                </ul>

                <span class="text-uppercase text-muted fw-bold px-3 mt-4 mb-2 d-block" style="font-size: 0.65rem; letter-spacing: 0.08em;">Account Settings</span>
                <ul class="nav flex-column gap-1">
                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}" class="nav-link d-flex align-items-center px-3 py-2.5 rounded-3 fw-medium {{ request()->routeIs('profile.*') ? 'text-primary bg-primary bg-opacity-10 active' : 'text-dark hover-bg-light' }}">
                            <i class="bi bi-person-circle fs-5 me-3 text-muted"></i> My Profile
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Sidebar Footer / Logout Form -->
            <div class="sidebar-footer pt-3 border-top">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link w-100 border-0 bg-transparent d-flex align-items-center px-3 py-2.5 rounded-3 fw-medium text-danger hover-bg-danger-light text-start">
                        <i class="bi bi-box-arrow-right fs-5 me-3"></i> Logout
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Content Wrapper Area -->
        <div class="main-content d-flex flex-column">
            
            <!-- Top Navbar with Mobile Toggle and Notification Bell -->
            <header class="navbar navbar-expand bg-white border-bottom px-4 py-2 sticky-top shadow-sm">
                <div class="container-fluid px-0">
                    <!-- Mobile Sidebar Toggle Button -->
                    <button id="sidebarToggle" class="btn btn-outline-secondary d-lg-none me-3 border-0" type="button">
                        <i class="bi bi-list fs-4"></i>
                    </button>

                    <span class="navbar-brand fw-semibold fs-6 text-secondary d-none d-sm-block">Welcome back, {{ auth()->user()->first_name ?? 'Resident' }}!</span>

                    <!-- Right Navigation Elements (Notifications & User Profile) -->
                    <ul class="navbar-nav ms-auto align-items-center gap-3">
                        
                        <!-- Notification Bell Dropdown -->
                        <li class="nav-item dropdown">
                            @php
                                $totalNotificationsCount = auth()->user()->notifications()->count();
                                $unreadNotificationsCount = auth()->user()->unreadNotifications->count();
                            @endphp
                            <a class="nav-link position-relative text-dark px-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="bellIcon" data-total-count="{{ $totalNotificationsCount }}">
                                <i class="bi bi-bell fs-5"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationBadge" style="font-size: 0.65rem; padding: 0.25em 0.5em; display: none;">
                                    0
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow border-0 py-3 rounded-4 p-3" style="width: 380px;">
                                
                                <!-- Header Title -->
                                <div class="mb-2 px-1">
                                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.25rem;">Notifications</h5>
                                </div>

                                <!-- Filter Tabs (All / Unread) -->
                                <div class="d-flex gap-2 mb-3 px-1">
                                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold" id="filterAllBtn" onclick="filterNotifications('all')" style="background-color: #e7f3ff; color: #0d6efd; font-size: 0.85rem;">All</button>
                                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold" id="filterUnreadBtn" onclick="filterNotifications('unread')" style="background-color: #f0f2f5; color: #65676b; font-size: 0.85rem;">Unread <span class="ms-1 px-1.5 py-0.5 rounded-pill bg-secondary bg-opacity-20 text-dark" style="font-size: 0.7rem;">{{ $unreadNotificationsCount }}</span></button>
                                </div>

                                <!-- Notification List Container -->
                                <div class="notification-list" style="max-height: 380px; overflow-y: auto;">
                                    @forelse(auth()->user()->notifications()->take(6)->get() as $notification)
                                        @php
                                            $isUnread = is_null($notification->read_at);
                                            $readRoute = route('resident.notifications.read', $notification->id);

                                            $type = $notification->data['type'] ?? 'default';
                                            $iconClass = 'bi-bell bg-secondary';

                                            if (str_contains(strtolower($notification->data['title'] ?? ''), 'document') || str_contains(strtolower($type), 'document')) {
                                                $iconClass = 'bi-file-text bg-info text-white';
                                            } elseif (str_contains(strtolower($notification->data['title'] ?? ''), 'incident') || str_contains(strtolower($type), 'incident')) {
                                                $iconClass = 'bi-exclamation-triangle bg-warning text-dark';
                                            } elseif (str_contains(strtolower($notification->data['title'] ?? ''), 'booking') || str_contains(strtolower($type), 'booking')) {
                                                $iconClass = 'bi-calendar-check bg-success text-white';
                                            } else {
                                                $iconClass = 'bi-bell bg-primary text-white';
                                            }
                                        @endphp

                                        <a href="{{ $readRoute }}" class="notification-item d-flex align-items-center gap-3 p-2 rounded-3 text-decoration-none text-dark mb-1 position-relative {{ $isUnread ? 'unread-notif' : '' }}" data-read="{{ $isUnread ? '0' : '1' }}" style="transition: background-color 0.2s;">
                                            <!-- Icon Avatar -->
                                            <div class="position-relative flex-shrink-0">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm {{ $iconClass }}" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                                    <i class="bi {{ explode(' ', $iconClass)[0] }}"></i>
                                                </div>
                                            </div>

                                            <!-- Content Text -->
                                            <div class="flex-grow-1 overflow-hidden">
                                                <p class="mb-0 text-truncate fw-semibold text-dark" style="font-size: 0.9rem;">
                                                    {{ $notification->data['title'] ?? 'Notification' }}
                                                </p>
                                                <p class="mb-0 text-muted text-truncate" style="font-size: 0.8rem; max-width: 220px;">
                                                    {{ $notification->data['message'] ?? '' }}
                                                </p>
                                                <span class="text-primary fw-medium" style="font-size: 0.75rem;">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </span>
                                            </div>

                                            <!-- Blue Dot Indicator for Unread -->
                                            @if($isUnread)
                                                <div class="flex-shrink-0 pe-1">
                                                    <span class="rounded-circle bg-primary d-inline-block" style="width: 10px; height: 10px;"></span>
                                                </div>
                                            @endif
                                        </a>
                                    @empty
                                        <div class="text-center py-4 text-muted" style="font-size: 0.85rem;">
                                            <i class="bi bi-bell-slash fs-3 d-block mb-1 opacity-50"></i>
                                            No new notifications
                                        </div>
                                    @endforelse
                                </div>

                                <!-- Footer Button -->
                                <div class="mt-2 pt-2 border-top text-center">
                                    <a href="{{ route('resident.documents.index') }}" class="btn btn-light w-100 py-2 fw-semibold text-dark rounded-3" style="font-size: 0.85rem; background-color: #f0f2f5;">
                                        See previous notifications
                                    </a>
                                </div>

                            </div>
                        </li>

                        <!-- User Info / Profile Dropdown Menu -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-dark text-decoration-none px-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 38px; height: 38px; font-size: 0.9rem;">
                                    {{ strtoupper(substr(auth()->user()->first_name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="d-none d-md-inline-block fw-semibold text-secondary" style="font-size: 0.9rem;">
                                    {{ auth()->user()->first_name ?? 'Resident' }}
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2 rounded-3" style="width: 220px;">
                                <li class="px-3 py-1.5 border-bottom mb-1">
                                    <span class="d-block text-muted" style="font-size: 0.7rem;">Signed in as</span>
                                    <span class="d-block fw-bold text-dark text-truncate" style="font-size: 0.85rem;">{{ auth()->user()->email ?? '' }}</span>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-person-gear text-primary"></i> My Profile
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </header>

            <!-- Page Content Yield Area -->
            <div id="app" class="flex-grow-1 p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Layout & Mobile Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');

            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('show');
                });

                document.addEventListener('click', function (event) {
                    if (window.innerWidth < 992) {
                        if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                            sidebar.classList.remove('show');
                        }
                    }
                });
            }

            const notificationDropdownMenu = document.querySelector('.dropdown-menu');
            if (notificationDropdownMenu) {
                notificationDropdownMenu.addEventListener('click', function (event) {
                    if (event.target.closest('#filterAllBtn') || event.target.closest('#filterUnreadBtn')) {
                        event.stopPropagation();
                    }
                });
            }

            const bellIcon = document.getElementById('bellIcon');
            const badge = document.getElementById('notificationBadge');

            if (bellIcon && badge) {
                const currentTotalCount = parseInt(bellIcon.getAttribute('data-total-count')) || 0;
                const storageKey = 'resident_notifications_last_count_' + '{{ auth()->id() }}';
                let lastSeenCount = parseInt(localStorage.getItem(storageKey));

                if (isNaN(lastSeenCount)) {
                    lastSeenCount = 0;
                }

                let newNotificationsCount = currentTotalCount - lastSeenCount;
                if (newNotificationsCount < 0) {
                    newNotificationsCount = 0;
                }

                if (newNotificationsCount > 0) {
                    badge.innerText = newNotificationsCount;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }

                bellIcon.addEventListener('click', function () {
                    badge.style.display = 'none';
                    badge.innerText = '0';
                    localStorage.setItem(storageKey, currentTotalCount);
                });
            }
        });

        function filterNotifications(type) {
            const allBtn = document.getElementById('filterAllBtn');
            const unreadBtn = document.getElementById('filterUnreadBtn');
            const items = document.querySelectorAll('.notification-item');

            if (type === 'all') {
                allBtn.style.backgroundColor = '#e7f3ff';
                allBtn.style.color = '#0d6efd';
                unreadBtn.style.backgroundColor = '#f0f2f5';
                unreadBtn.style.color = '#65676b';

                items.forEach(item => {
                    item.style.setProperty('display', 'flex', 'important');
                });
            } else {
                unreadBtn.style.backgroundColor = '#e7f3ff';
                unreadBtn.style.color = '#0d6efd';
                allBtn.style.backgroundColor = '#f0f2f5';
                allBtn.style.color = '#65676b';

                items.forEach(item => {
                    const isRead = item.getAttribute('data-read');
                    if (isRead === '0') {
                        item.style.setProperty('display', 'flex', 'important');
                    } else {
                        item.style.setProperty('display', 'none', 'important');
                    }
                });
            }
        }
    </script>
        
    @stack('scripts')
</body>
</html>