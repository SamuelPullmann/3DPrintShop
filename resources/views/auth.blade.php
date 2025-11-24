@php
    // resources/views/auth.blade.php
@endphp
@extends('layouts.app')

@section('title', 'Login / Register')

@section('content')
    <div class="auth-container">
        <div class="auth-card">
            @auth
                {{-- User is logged in --}}
                <h1 class="auth-title">You are logged in as</h1>

                <div class="auth-user-info">
                    <p class="auth-user-name">{{ Auth::user()->name }}</p>
                    <p class="auth-user-email">{{ Auth::user()->email }}</p>
                    <p class="auth-user-role">Role: {{ Auth::user()->role }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="auth-submit auth-logout">Sign Out</button>
                </form>

                <div class="auth-divider">or</div>

                <div class="auth-toggle">
                    <a href="{{ url('/') }}">Go to Homepage</a>
                </div>
            @else
                {{-- User is NOT logged in --}}
                <h1 id="auth-title" class="auth-title">Login</h1>

                <div id="login-form" style="display:block;">
                    <form method="POST" action="{{ route('login.submit') }}">
                        @csrf
                        <div class="auth-form-group">
                            <label for="login-email" class="auth-label">Email</label>
                            <input class="auth-input"
                                   id="login-email"
                                   type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="your@email.com"
                                   required>
                        </div>
                        <div class="auth-form-group">
                            <label for="login-password" class="auth-label">Password</label>
                            <div class="password-field">
                                <input class="auth-input"
                                       id="login-password"
                                       type="password"
                                       name="password"
                                       placeholder="••••••••"
                                       required>
                            </div>
                            @error('email')
                                <span class="auth-error-text">Invalid credentials.</span>
                            @enderror
                        </div>
                        <button type="submit" class="auth-submit">Login</button>
                    </form>

                    <div class="auth-divider">or</div>

                    <div class="auth-toggle">
                        <span>Don't have an account?</span>
                        <a href="#" id="show-register-bottom">Register</a>
                    </div>
                </div>

                <div id="register-form" style="display:none;">
                    <form method="POST" action="{{ route('register.submit') }}">
                        @csrf
                        <div class="auth-form-group">
                            <label for="register-name" class="auth-label">Name</label>
                            <input class="auth-input"
                                   id="register-name"
                                   type="text"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="Your name"
                                   required>
                        </div>
                        <div class="auth-form-group">
                            <label for="register-email" class="auth-label">Email</label>
                            <input class="auth-input"
                                   id="register-email"
                                   type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="your@email.com"
                                   required>
                            @error('email')
                                <span class="auth-error-text">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="auth-form-group">
                            <label for="register-password" class="auth-label">Password</label>
                            <div class="password-field">
                                <input class="auth-input"
                                       id="register-password"
                                       type="password"
                                       name="password"
                                       placeholder="••••••••"
                                       required>
                            </div>
                            @error('password')
                                <span class="auth-error-text">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="auth-submit">Register</button>
                    </form>

                    <div class="auth-divider">or</div>

                    <div class="auth-toggle">
                        <span>Already have an account?</span>
                        <a href="#" id="show-login-bottom">Login</a>
                    </div>
                </div>
            @endauth
        </div>
    </div>
@endsection

@push('scripts')
    @guest
        @vite('resources/js/auth.js')
    @endguest
@endpush
