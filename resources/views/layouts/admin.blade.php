<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Barangay E-Portal</title>
    
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Font: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --govt-blue: #0d6efd;
            --govt-sidebar: #0f172a;
            --govt-bg: #f8fafc;
        }
        body { 
            background-color: var(--govt-bg); 
            font-family: 'Inter', system-ui, sans-serif; 
            color: #334155;
        }
        .sidebar { 
            min-height: 100vh; 
            background-color: var(--govt-sidebar); 
            color: #fff; 
            transition: all 0.3s ease; 
            box-shadow: 4px 0 10px rgba(0,0,0,0.03);
        }
        .sidebar .nav-link { 
            color: #94a3b8; 
            padding: 0.75rem 1rem; 
            border-radius: 0.5rem; 
            margin-bottom: 0.25rem; 
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { 
            background-color: var(--govt-blue); 
            color: #fff; 
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }
        .content-area { 
            flex-grow: 1; 
            transition: all 0.3s ease; 
        }
        .card { 
            border: none; 
            border-radius: 1rem; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -2px rgba(0,0,0,0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        /* Modern Notification Dropdown Styling */
        .custom-notification-dropdown {
            width: 400px;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(226, 232, 240, 0.8);
            overflow: hidden;
        }
        .notification-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .notification-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .notification-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .unread-item {
            background-color: #f0f7ff !important;
            transition: background-color 0.15s ease;
        }
        .unread-item:hover {
            background-color: #e2f0ff !important;
        }
        .read-item {
            background-color: #ffffff !important;
            transition: background-color 0.15s ease;
        }
        .read-item:hover {
            background-color: #f8fafc !important;
        }

        @media (max-width: 768px) {
            .sidebar { position: fixed; z-index: 1050; left: -100%; }
            .sidebar.show { left: 0; }
            .custom-notification-dropdown { width: 100vw !important; max-width: 350px; }
        }
    </style>
</head>
<body class="d-flex">

    <!-- Sidebar -->
    <nav class="sidebar p-3 d-flex flex-column" style="width: 270px;" id="sidebar">
        <div class="d-flex align-items-center mb-4 px-2 pt-2">
            <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-2 text-primary">
                <i class="bi bi-bank fs-4"></i>
            </div>
            <div>
                <span class="fs-6 fw-bold d-block text-white">Barangay Portal</span>
                <span class="text-muted" style="font-size: 11px;">Admin Command Center</span>
            </div>
        </div>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.residents.index') }}" class="nav-link {{ request()->routeIs('admin.residents.*') ? 'active' : '' }}">
                    <i class="bi bi-people me-2"></i> Resident Management
                </a>
            </li>
            <li>
                <a href="{{ route('admin.documents.index') }}" class="nav-link {{ request()->routeIs('admin.documents.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text me-2"></i> Document Requests
                </a>
            </li>
            <li>
                <a href="{{ route('admin.incidents.index') }}" class="nav-link {{ request()->routeIs('admin.incidents.*') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle me-2"></i> Incident Reports
                </a>
            </li>
            <li>
                <a href="{{ route('admin.bookings.index') }}" class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check me-2"></i> Facility Bookings
                </a>
            </li>
            <li>
                <a href="{{ route('admin.announcements.index') }}" class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone me-2"></i> Announcements
                </a>
            </li>
            <li>
                <a href="{{ route('admin.hotlines.index') }}" class="nav-link {{ request()->routeIs('admin.hotlines.*') ? 'active' : '' }}">
                    <i class="bi bi-telephone me-2"></i> Hotlines
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line me-2"></i> Reports
                </a>
            </li>
            <li>
                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear me-2"></i> Settings
                </a>
            </li>
            <li>
                <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history me-2"></i> Activity Logs
                </a>
            </li>
            <!-- Digital Signature Link Added -->
            <li>
                <a href="{{ route('admin.signature.form') }}" class="nav-link {{ request()->routeIs('admin.signature.*') ? 'active' : '' }}">
                    <i class="bi bi-pen me-2"></i> Digital Signature
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content Area -->
    <div class="content-area d-flex flex-column overflow-auto vh-100 w-100">
        <!-- Topbar -->
        <header class="navbar navbar-expand bg-white shadow-sm px-4 py-2 sticky-top">
            <button class="btn btn-light d-md-none me-3 border" id="sidebarToggle"><i class="bi bi-list"></i></button>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="d-none d-md-block ms-2 mt-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item text-muted"><small>Admin Portal</small></li>
                    <li class="breadcrumb-item active fw-semibold text-dark" aria-current="page">@yield('title')</li>
                </ol>
            </nav>

            <ul class="navbar-nav ms-auto align-items-center">
                @php
                    $admin = auth()->user();
                    $unreadCount = $admin ? $admin->unreadNotifications->count() : 0;
                @endphp

                <!-- Modern Bell Notifications Dropdown -->
                <li class="nav-item dropdown me-3">
                    <a class="nav-link text-dark position-relative d-flex align-items-center justify-content-center bg-light rounded-circle shadow-sm" href="#" data-bs-toggle="dropdown" aria-expanded="false" onclick="dismissBadgePermanent()" style="width: 40px; height: 40px;">
                        <i class="bi bi-bell fs-5 text-secondary"></i>
                        <span class="position-absolute top-0 start-75 translate-middle badge rounded-pill bg-danger border border-white" id="notification-badge" style="font-size: 9px; padding: 0.3em 0.5em; display: none;">
                            0
                            <span class="visually-hidden">New alerts</span>
                        </span>
                    </a>
                    
                    <!-- Notification Dropdown Container -->
                    <ul class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0 custom-notification-dropdown mt-2">
                        <!-- Header Title & Mark All as Read Button -->
                        <li>
                            <div class="px-4 pt-3 pb-2 bg-white border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="m-0 fw-bold text-dark">Notifications</h5>
                                @if($unreadCount > 0)
                                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 fw-semibold text-primary" onclick="markAllAsReadDatabase(); event.stopPropagation();" style="font-size: 13px;">
                                        Mark all as read
                                    </button>
                                @endif
                            </div>
                        </li>

                        <!-- Filter Tabs (All / Unread) -->
                        <li>
                            <div class="px-3 py-2.5 d-flex gap-2 border-bottom bg-white" onclick="event.stopPropagation();">
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold active shadow-sm" id="filter-all-btn" onclick="filterNotifications('all'); event.stopPropagation();">All</button>
                                <button type="button" class="btn btn-sm btn-light rounded-pill px-3 py-1 fw-semibold text-secondary bg-light border-0" id="filter-unread-btn" onclick="filterNotifications('unread'); event.stopPropagation();">Unread</button>
                            </div>
                        </li>

                        <!-- Notifications Items List -->
                        <li>
                            <div class="notification-scrollbar" style="max-height: 380px; overflow-y: auto;">
                                @forelse($admin->notifications as $notification)
                                    <a class="dropdown-item py-3 px-3 border-bottom text-wrap notification-item {{ is_null($notification->read_at) ? 'unread-item' : 'read-item' }}" href="{{ route('admin.notifications.read', $notification->id) }}" style="white-space: normal;">
                                        <div class="d-flex align-items-start">
                                            <!-- Avatar / Category Icon -->
                                            <div class="flex-shrink-0 me-3 position-relative">
                                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px; font-size: 16px;">
                                                    <i class="bi bi-file-earmark-text"></i>
                                                </div>
                                                <span class="position-absolute bottom-0 end-0 p-1 bg-primary text-white rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 16px; height: 16px;">
                                                    <i class="bi bi-bell-fill" style="font-size: 8px;"></i>
                                                </span>
                                            </div>
                                            
                                            <!-- Message & Time -->
                                            <div class="flex-grow-1 pe-2">
                                                <p class="mb-1 text-dark fw-semibold" style="line-height: 1.35; font-size: 13px;">
                                                    {{ $notification->data['message'] ?? 'New notification received' }}
                                                </p>
                                                <small class="text-muted d-flex align-items-center mt-1" style="font-size: 11px;">
                                                    <i class="bi bi-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                                </small>
                                            </div>

                                            <!-- Unread Blue Dot Indicator -->
                                            @if(is_null($notification->read_at))
                                                <div class="ms-1 align-self-center notification-dot">
                                                    <span class="bg-primary rounded-circle" style="width: 8px; height: 8px; display: inline-block;"></span>
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-center py-5 text-muted px-4">
                                        <div class="rounded-circle bg-light d-inline-flex p-3 mb-2 text-secondary">
                                            <i class="bi bi-bell-slash fs-3"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">No notifications yet</h6>
                                        <small class="text-muted">When you get notifications, they will show up here.</small>
                                    </div>
                                @endforelse
                            </div>
                        </li>

                        <!-- Sticky Footer: View All Link -->
                        <li>
                            <div class="p-2.5 bg-white border-top text-center">
                                <a href="{{ route('admin.notifications.index') }}" class="text-decoration-none fw-bold text-primary d-block py-1" style="font-size: 13px;">
                                    View all notifications
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>

                <!-- User Profile Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center bg-light px-3 py-1.5 rounded-pill border" href="#" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->full_name ?? 'Admin') }}&background=0d6efd&color=fff" class="rounded-circle me-2 shadow-sm" width="28" alt="Admin">
                        <span class="text-dark fw-semibold" style="font-size: 13.5px;">{{ auth()->user()->full_name ?? 'Admin' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3 p-1">
                        <li><a class="dropdown-item rounded-2 py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2 text-primary"></i> Profile</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item rounded-2 py-2 text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </header>

        <!-- Page Content -->
        <main class="p-4 flex-grow-1">
            @yield('content')
        </main>
        
        <footer class="bg-white py-3 text-center text-muted shadow-sm mt-auto border-top">
            <small>&copy; {{ date('Y') }} Barangay E-Portal. All rights reserved.</small>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sidebar Toggle for Mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Independent Notification Badge Logic
        document.addEventListener("DOMContentLoaded", function() {
            updateBadgeDisplay();
        });

        function updateBadgeDisplay() {
            const badge = document.getElementById('notification-badge');
            if (!badge) return;

            const totalUnread = parseInt("{{ $unreadCount }}") || 0;
            let lastSeenTotal = localStorage.getItem('admin_last_seen_unread_count');

            let badgeCount = 0;

            if (lastSeenTotal === null) {
                badgeCount = totalUnread;
            } else {
                badgeCount = totalUnread - parseInt(lastSeenTotal);
            }

            if (badgeCount < 0) {
                badgeCount = 0;
            }

            if (badgeCount > 0) {
                badge.textContent = badgeCount > 99 ? '99+' : badgeCount;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }

        // Reset badge count to 0 when the notification bell is clicked
        function dismissBadgePermanent() {
            const badge = document.getElementById('notification-badge');
            if (badge) {
                badge.style.display = 'none';
                
                const currentTotalUnread = "{{ $unreadCount }}";
                localStorage.setItem('admin_last_seen_unread_count', currentTotalUnread);
            }
        }

        // Toggle Filter Function for All and Unread Notification Tabs
        function filterNotifications(type) {
            const allBtn = document.getElementById('filter-all-btn');
            const unreadBtn = document.getElementById('filter-unread-btn');
            const items = document.querySelectorAll('.notification-item');

            if (type === 'all') {
                allBtn.classList.add('btn-primary', 'active', 'shadow-sm');
                allBtn.classList.remove('btn-light', 'text-secondary', 'bg-light');
                unreadBtn.classList.add('btn-light', 'text-secondary', 'bg-light');
                unreadBtn.classList.remove('btn-primary', 'active', 'shadow-sm');

                items.forEach(item => item.style.display = 'block');
            } else {
                unreadBtn.classList.add('btn-primary', 'active', 'shadow-sm');
                unreadBtn.classList.remove('btn-light', 'text-secondary', 'bg-light');
                allBtn.classList.add('btn-light', 'text-secondary', 'bg-light');
                allBtn.classList.remove('btn-primary', 'active', 'shadow-sm');

                items.forEach(item => {
                    if (item.classList.contains('unread-item')) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }
        }

        function markAllAsReadDatabase() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch("{{ route('admin.notifications.readAllAjax') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }   
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    localStorage.setItem('admin_last_seen_unread_count', '0');
                    document.querySelectorAll('.notification-dot').forEach(dot => dot.remove());
                    document.querySelectorAll('.unread-item').forEach(item => {
                        item.classList.remove('unread-item');
                        item.classList.add('read-item');
                    });
                    updateBadgeDisplay();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
    @stack('scripts')
</body>
</html>