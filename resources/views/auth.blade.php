@php
    // resources/views/auth.blade.php
@endphp
@extends('layouts.app')

@section('title', 'Login / Register')

@section('content')
    <style>
        .auth-input {
            padding: 0.2rem;
            border: 2px solid #ccc;
            border-radius: 6px;
        }
    </style>
    <h1 id="auth-title">Login</h1>

    <div style="margin-bottom:1rem;">
        <span>Nemáš účet?</span>
        <a href="#" id="show-register">Registrovať</a>
        <span style="margin:0 0.5rem;">|</span>
        <a href="#" id="show-login" style="display:none;">Prihlásiť</a>
    </div>

    <div id="login-form" style="display:block;">
        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div style="margin-bottom:1rem;">
                <label for="login-email">Email</label><br>
                <input class = "auth-input" id="login-email" type="email" name="email" required>
            </div>
            <div style="margin-bottom:1rem;">
                <label for="login-password">Heslo</label><br>
                <input class = "auth-input" id="login-password" type="password" name="password" required>
            </div>
            <button type="submit">Prihlásiť</button>
        </form>
    </div>

    <div id="register-form" style="display:none;">
        <form method="POST" action="{{ route('register.submit') }}">
            @csrf
            <div style="margin-bottom:1rem;">
                <label for="register-name">Meno</label><br>
                <input class = "auth-input" id="register-name" type="text" name="name" required>
            </div>
            <div style="margin-bottom:1rem;">
                <label for="register-email">Email</label><br>
                <input class = "auth-input" id="register-email" type="email" name="email" required>
            </div>
            <div style="margin-bottom:1rem;">
                <label for="register-password">Heslo</label><br>
                <input class = "auth-input" id="register-password" type="password" name="password" required>
            </div>
            <button type="submit">Registrovať</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const title = document.getElementById('auth-title');
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const showLogin = document.getElementById('show-login');
            const showRegister = document.getElementById('show-register');

            showRegister.addEventListener('click', function (e) {
                e.preventDefault();
                title.textContent = 'Registrovať';
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
                showRegister.style.display = 'none';
                showLogin.style.display = 'inline';
            });

            showLogin.addEventListener('click', function (e) {
                e.preventDefault();
                title.textContent = 'Login';
                registerForm.style.display = 'none';
                loginForm.style.display = 'block';
                showLogin.style.display = 'none';
                showRegister.style.display = 'inline';
            });
        });
    </script>
@endsection
