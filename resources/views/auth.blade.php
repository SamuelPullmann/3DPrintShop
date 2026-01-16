@php
    // resources/views/auth.blade.php
@endphp
@extends('layouts.app')

@section('title', 'Login / Register')

@section('content')
    <div class="auth-container">
        <div class="auth-card">
            <h1 id="auth-title" class="auth-title">Login</h1>

            <div id="login-form">
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

            <div id="register-form">
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
                        <span class="auth-error-text" id="register-password-error">Password needs to be at least 8 characters long, use at least 1 number, 1 uppercase and 1 lowercase character.</span>
                    </div>
                    <button type="submit" class="auth-submit" id="register-button">Register</button>
                </form>

                <div class="auth-divider">or</div>

                <div class="auth-toggle">
                    <span>Already have an account?</span>
                    <a href="#" id="show-login-bottom">Login</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/auth.js')
@endpush
