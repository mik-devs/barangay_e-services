@extends('layouts.guest')

@section('title', 'Sign In - Barangay e-Services Portal')

@section('content')
<div class="bp-login">

    {{-- LEFT PANEL --}}
    <div class="bp-left d-none d-lg-flex flex-column">

        <div class="bp-mesh" aria-hidden="true">
            <span class="bp-blob bp-blob-a"></span>
            <span class="bp-blob bp-blob-b"></span>
            <svg class="bp-seal" viewBox="0 0 200 200" aria-hidden="true">
                <circle cx="100" cy="100" r="98" fill="none" stroke="rgba(255,255,255,0.14)" stroke-width="1"/>
                <circle cx="100" cy="100" r="80" fill="none" stroke="rgba(255,255,255,0.10)" stroke-width="1"/>
            </svg>
        </div>

        <div class="bp-left-inner">

            <div class="bp-brand">
                <div class="bp-brand-mark">
                    <i class="bi bi-building-fill-gear"></i>
                </div>
                <div>
                    <div class="bp-brand-name">Barangay e-Services</div>
                    <div class="bp-brand-sub">Digital Government Services for Every Resident</div>
                </div>
            </div>

            <div class="bp-hero">
                <span class="bp-eyebrow">Official enterprise portal</span>
                <h1 class="bp-headline">Local governance,<br>built for the way residents live now.</h1>

                <div class="bp-features">
                    <div class="bp-feature">
                        <i class="bi bi-file-earmark-text"></i>
                        <div>
                            <div class="bp-feature-title">Document requests</div>
                            <div class="bp-feature-sub">Clearances, permits and more</div>
                        </div>
                    </div>
                    <div class="bp-feature">
                        <i class="bi bi-calendar-check"></i>
                        <div>
                            <div class="bp-feature-title">Facility booking</div>
                            <div class="bp-feature-sub">Halls, courts and equipment</div>
                        </div>
                    </div>
                    <div class="bp-feature">
                        <i class="bi bi-shield-exclamation"></i>
                        <div>
                            <div class="bp-feature-title">Incident reports</div>
                            <div class="bp-feature-sub">Secure reporting and tracking</div>
                        </div>
                    </div>
                    <div class="bp-feature">
                        <i class="bi bi-stars"></i>
                        <div>
                            <div class="bp-feature-title">AI assistant</div>
                            <div class="bp-feature-sub">Guided help, day or night</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bp-trust">
                <span><i class="bi bi-lock-fill"></i> 256-bit SSL secured</span>
                <span><i class="bi bi-shield-fill-check"></i> Data privacy compliant</span>
            </div>

        </div>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="bp-right d-flex align-items-center justify-content-center">
        <div class="bp-right-inner">

            <div class="d-lg-none bp-brand bp-brand--mobile">
                <div class="bp-brand-mark bp-brand-mark--sm">
                    <i class="bi bi-building-fill-gear"></i>
                </div>
                <div class="bp-brand-name">Barangay e-Services</div>
            </div>

            @if (session('status'))
                <div class="alert bp-alert alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('status') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="bp-card">

                <div class="bp-card-head">
                    <h2>Welcome back</h2>
                    <p>Sign in to continue to your resident account</p>
                </div>

                <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                    @csrf

                    <div class="bp-field">
                        <label for="email">Email address</label>
                        <div class="bp-input-wrap">
                            <i class="bi bi-envelope"></i>
                            <input id="email" type="email"
                                   class="bp-input @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}"
                                   placeholder="name@example.com"
                                   required autofocus autocomplete="username">
                        </div>
                        @error('email')
                            <div class="bp-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="bp-field">
                        <div class="bp-field-row">
                            <label for="password">Password</label>
                            @if (Route::has('password.request'))
                                <a class="bp-link-sm" href="{{ route('password.request') }}">Forgot password?</a>
                            @endif
                        </div>
                        <div class="bp-input-wrap">
                            <i class="bi bi-shield-lock"></i>
                            <input id="password" type="password"
                                   class="bp-input @error('password') is-invalid @enderror"
                                   name="password"
                                   placeholder="Enter your password"
                                   required autocomplete="current-password">
                            <button class="bp-toggle-btn" type="button" id="togglePassword" aria-label="Toggle password visibility">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="bp-error"><i class="bi bi-exclamation-circle"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="bp-remember">
                        <input id="remember_me" type="checkbox" name="remember">
                        <label for="remember_me">Remember me on this device</label>
                    </div>

                    <button type="submit" class="bp-submit" id="submitBtn">
                        <span class="btn-text">Sign in</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>

                    <div class="bp-divider"><span>or</span></div>

                    <div class="bp-register">
                        <p>Don't have an account yet?</p>
                        <a href="{{ route('register') }}">Register as resident <i class="bi bi-chevron-right"></i></a>
                    </div>

                </form>
            </div>

            <p class="bp-copyright">&copy; {{ date('Y') }} Barangay e-Services Portal. All rights reserved.</p>

        </div>
    </div>

</div>

<style>
    :root{
        --bp-ink: #0b1220;
        --bp-ink-soft: #4b5563;
        --bp-mist: #8a94a3;
        --bp-line: #e6e9ee;
        --bp-emerald: #0f6e56;
        --bp-emerald-deep: #0a4d3c;
        --bp-blue: #185fa5;
        --bp-bg: #f7f9f8;
        --bp-radius: 16px;
    }

    body{ font-family: 'Inter', system-ui, -apple-system, sans-serif; background: var(--bp-bg); }

    .bp-login{ min-height: 100vh; display: flex; }

    /* LEFT PANEL */
    .bp-left{
        flex: 0 0 46%;
        position: relative;
        overflow: hidden;
        background: linear-gradient(160deg, #0a4d3c 0%, #0f6e56 45%, #185fa5 100%);
        color: #fff;
        padding: 3rem;
    }
    .bp-mesh{ position: absolute; inset: 0; overflow: hidden; }
    .bp-blob{ position: absolute; border-radius: 50%; filter: blur(70px); opacity: 0.35; }
    .bp-blob-a{ width: 420px; height: 420px; background: #3ddc97; top: -120px; left: -100px; }
    .bp-blob-b{ width: 480px; height: 480px; background: #4a9bf0; bottom: -160px; right: -140px; }
    .bp-seal{ position: absolute; width: 460px; height: 460px; right: -120px; bottom: -80px; opacity: 0.6; }

    .bp-left-inner{ position: relative; z-index: 1; height: 100%; display: flex; flex-direction: column; justify-content: space-between; max-width: 460px; }

    .bp-brand{ display: flex; align-items: center; gap: 14px; }
    .bp-brand-mark{
        width: 50px; height: 50px; border-radius: 14px;
        background: rgba(255,255,255,0.14);
        border: 1px solid rgba(255,255,255,0.24);
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; flex: none;
    }
    .bp-brand-name{ font-weight: 600; font-size: 15px; letter-spacing: -0.01em; }
    .bp-brand-sub{ font-size: 12.5px; color: rgba(255,255,255,0.72); margin-top: 1px; }

    .bp-hero{ padding: 2.5rem 0; }
    .bp-eyebrow{
        display: inline-block; font-size: 12px; font-weight: 600; letter-spacing: 0.04em;
        color: #d7fbe9; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2);
        padding: 5px 12px; border-radius: 999px; margin-bottom: 18px;
    }
    .bp-headline{ font-size: 30px; line-height: 1.28; font-weight: 600; letter-spacing: -0.02em; margin-bottom: 2rem; }

    .bp-features{ display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .bp-feature{
        display: flex; align-items: flex-start; gap: 12px;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.16);
        border-radius: 12px; padding: 14px;
        backdrop-filter: blur(14px);
        transition: background 0.2s ease, transform 0.2s ease;
    }
    .bp-feature:hover{ background: rgba(255,255,255,0.14); transform: translateY(-2px); }
    .bp-feature i{ font-size: 18px; margin-top: 2px; color: #eafff5; }
    .bp-feature-title{ font-size: 13.5px; font-weight: 600; }
    .bp-feature-sub{ font-size: 12px; color: rgba(255,255,255,0.68); margin-top: 1px; }

    .bp-trust{ display: flex; gap: 22px; font-size: 12.5px; color: rgba(255,255,255,0.68); }
    .bp-trust i{ margin-right: 5px; }

    /* RIGHT PANEL */
    .bp-right{ flex: 1; padding: 2.5rem 1.5rem; }
    .bp-right-inner{ width: 100%; max-width: 400px; }

    .bp-brand--mobile{ justify-content: center; margin-bottom: 1.75rem; }
    .bp-brand-mark--sm{ width: 42px; height: 42px; background: var(--bp-emerald); color: #fff; border: none; font-size: 18px; }
    .bp-brand--mobile .bp-brand-name{ color: var(--bp-ink); }

    .bp-alert{
        display: flex; align-items: center; gap: 10px;
        background: #ecfaf3; border: 1px solid #bfe9d3; color: var(--bp-emerald-deep);
        border-radius: 12px; padding: 12px 14px; font-size: 13.5px; margin-bottom: 1.25rem;
    }

    .bp-card{
        background: rgba(255,255,255,0.9);
        border: 1px solid rgba(255,255,255,0.9);
        border-radius: var(--bp-radius);
        box-shadow: 0 24px 60px -18px rgba(15, 30, 25, 0.18), 0 2px 8px rgba(15,30,25,0.04);
        backdrop-filter: blur(20px);
        padding: 2.25rem 2rem;
    }

    .bp-card-head{ margin-bottom: 1.75rem; }
    .bp-card-head h2{ font-size: 22px; font-weight: 600; letter-spacing: -0.01em; color: var(--bp-ink); margin: 0 0 4px; }
    .bp-card-head p{ font-size: 13.5px; color: var(--bp-ink-soft); margin: 0; }

    .bp-field{ margin-bottom: 1.1rem; }
    .bp-field label{ display: block; font-size: 12px; font-weight: 600; color: var(--bp-ink-soft); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px; }
    .bp-field-row{ display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
    .bp-field-row label{ margin-bottom: 0; }

    .bp-input-wrap{ position: relative; display: flex; align-items: center; }
    .bp-input-wrap > i{ position: absolute; left: 14px; color: var(--bp-mist); font-size: 15px; }
    .bp-input{
        width: 100%; height: 44px; border-radius: 12px; border: 1px solid var(--bp-line);
        background: #f9fafb; padding: 0 14px 0 40px; font-size: 14px; color: var(--bp-ink);
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }
    .bp-input:focus{ outline: none; background: #fff; border-color: var(--bp-emerald); box-shadow: 0 0 0 3px rgba(15,110,86,0.14); }
    .bp-input.is-invalid{ border-color: #e0645a; }
    .bp-toggle-btn{
        position: absolute; right: 6px; width: 32px; height: 32px; border: none; background: transparent;
        color: var(--bp-mist); border-radius: 8px; display: flex; align-items: center; justify-content: center;
    }
    .bp-toggle-btn:hover{ background: var(--bp-line); color: var(--bp-ink-soft); }

    .bp-error{ display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: #c0392b; margin-top: 6px; }

    .bp-remember{ display: flex; align-items: center; gap: 8px; margin: 0.25rem 0 1.5rem; }
    .bp-remember input{ width: 16px; height: 16px; accent-color: var(--bp-emerald); }
    .bp-remember label{ font-size: 13px; color: var(--bp-ink-soft); }

    .bp-submit{
        width: 100%; height: 46px; border: none; border-radius: 12px; color: #fff; font-weight: 600; font-size: 14px;
        background: linear-gradient(135deg, var(--bp-emerald) 0%, var(--bp-emerald-deep) 100%);
        display: flex; align-items: center; justify-content: center; gap: 8px;
        box-shadow: 0 10px 20px -8px rgba(15,110,86,0.45);
        transition: transform 0.15s cubic-bezier(0.16,1,0.3,1), box-shadow 0.15s ease;
    }
    .bp-submit:hover{ transform: translateY(-1px); box-shadow: 0 14px 26px -8px rgba(15,110,86,0.5); }
    .bp-submit:active{ transform: translateY(0); }
    .bp-submit:disabled{ opacity: 0.75; }

    .bp-divider{ position: relative; text-align: center; margin: 1.5rem 0; border-top: 1px solid var(--bp-line); }
    .bp-divider span{ position: absolute; top: -9px; left: 50%; transform: translateX(-50%); background: #fff; padding: 0 10px; font-size: 12px; color: var(--bp-mist); }

    .bp-register{ text-align: center; }
    .bp-register p{ font-size: 13px; color: var(--bp-ink-soft); margin: 0 0 2px; }
    .bp-register a{ font-size: 14px; font-weight: 600; color: var(--bp-emerald); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
    .bp-register a:hover{ color: var(--bp-emerald-deep); }

    .bp-link-sm{ font-size: 12.5px; font-weight: 600; color: var(--bp-emerald); text-decoration: none; }
    .bp-link-sm:hover{ color: var(--bp-emerald-deep); text-decoration: underline; }

    .bp-copyright{ text-align: center; font-size: 12px; color: var(--bp-mist); margin-top: 1.5rem; }

    @media (max-width: 991px){
        .bp-right{ padding: 3rem 1.25rem; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (togglePassword && passwordInput && toggleIcon) {
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                toggleIcon.classList.toggle('bi-eye', type === 'password');
                toggleIcon.classList.toggle('bi-eye-slash', type === 'text');
            });
        }

        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');

        if (loginForm && submitBtn) {
            loginForm.addEventListener('submit', function () {
                if (loginForm.checkValidity()) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="btn-text">Signing in...</span>';
                }
            });
        }
    });
</script>
@endsection