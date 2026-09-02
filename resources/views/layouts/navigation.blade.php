<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
    <div class="container-fluid px-4">
        
        <!-- Barangay Brand & Icon (Pinalitan ang Laravel Logo) -->
        <a class="navbar-brand d-flex align-items-center fw-bold fs-5" href="{{ route('dashboard') }}">
            <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 38px; height: 38px;">
                <i class="bi bi-building-fill fs-5"></i>
            </div>
            <span>Barangay E-Portal</span>
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#barangayNavbar" aria-controls="barangayNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Items -->
        <div class="collapse navbar-collapse" id="barangayNavbar">
            
            <!-- Left Side Navigation Links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                <li class="nav-item">
                    <a class="nav-link text-white opacity-75 active fw-semibold" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                </li>
            </ul>

            <!-- Right Side User Dropdown & Dark Mode Toggle -->
            <div class="d-flex align-items-center gap-3">
                
                <!-- Dark Mode Toggle Button -->
                <button class="btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center" id="themeToggleBtn" style="width: 35px; height: 35px;">
                    <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                </button>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle bg-white bg-opacity-10 px-3 py-1-5 rounded-pill" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="bg-white text-primary rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.85rem;">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="fw-semibold me-1 small">{{ Auth::user()->name }}</span>
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0 mt-2" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2 text-primary"></i> Profile Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger border-0 bg-transparent">
                                <i class="bi bi-box-arrow-right me-2"></i> Log Out
                            </button>
                        </form>
                        </li>
                    </ul>
                </div>

            </div>

        </div>
    </div>
</nav>