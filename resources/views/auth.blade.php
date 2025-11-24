@php
    // resources/views/auth.blade.php
@endphp
@extends('layouts.app')

@section('title', 'Login / Register')

@section('content')
    <div class="auth-container">
        <div class="auth-card">
            <h1 id="auth-title" class="auth-title">Login</h1>

            <div id="login-form" style="display:block;">
                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <div class="auth-form-group">
                        <label for="login-email" class="auth-label">Email</label>
                        <input class="auth-input" id="login-email" type="email" name="email" placeholder="your@email.com" required>
                    </div>
                    <div class="auth-form-group">
                        <label for="login-password" class="auth-label">Password</label>
                        <div class="password-field">
                            <input class="auth-input" id="login-password" type="password" name="password" placeholder="••••••••" required>
                        </div>
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
                        <input class="auth-input" id="register-name" type="text" name="name" placeholder="Your name" required>
                    </div>
                    <div class="auth-form-group">
                        <label for="register-email" class="auth-label">Email</label>
                        <input class="auth-input" id="register-email" type="email" name="email" placeholder="your@email.com" required>
                    </div>
                    <div class="auth-form-group">
                        <label for="register-password" class="auth-label">Password</label>
                        <div class="password-field">
                            <input class="auth-input" id="register-password" type="password" name="password" placeholder="••••••••" required>
                        </div>
                    </div>
                    <button type="submit" class="auth-submit">Register</button>
                </form>

                <div class="auth-divider">or</div>

                <div class="auth-toggle">
                    <span>Already have an account?</span>
                    <a href="#" id="show-login-bottom">Login</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const title = document.getElementById('auth-title');
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const showRegisterBottom = document.getElementById('show-register-bottom');
            const showLoginBottom = document.getElementById('show-login-bottom');

            function switchToRegister(e) {
                e.preventDefault();
                title.textContent = 'Register';
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
            }

            function switchToLogin(e) {
                e.preventDefault();
                title.textContent = 'Login';
                registerForm.style.display = 'none';
                loginForm.style.display = 'block';
            }

            showRegisterBottom.addEventListener('click', switchToRegister);
            showLoginBottom.addEventListener('click', switchToLogin);
        });
    </script>
@endsection
