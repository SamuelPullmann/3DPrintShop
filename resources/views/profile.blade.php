@extends('layouts.app')

@section('title', 'Profile Settings')

@push('styles')
    @vite(['resources/css/profile.css'])
@endpush

@section('content')
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="profile-icon">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <h1 class="profile-title">Profile Settings</h1>
            </div>

            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" class="profile-form">
                @csrf

                <div class="profile-section">
                    <h2 class="section-title">Personal Information</h2>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text"
                                   id="first_name"
                                   name="first_name"
                                   class="form-input"
                                   value="{{ old('first_name', explode(' ', $user->name)[0] ?? '') }}"
                                   placeholder="First name">
                            @error('first_name')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text"
                                   id="last_name"
                                   name="last_name"
                                   class="form-input"
                                   value="{{ old('last_name', implode(' ', array_slice(explode(' ', $user->name), 1)) ?? '') }}"
                                   placeholder="Last name">
                            @error('last_name')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="label-icon">
                                    <path d="M2.667 2.667h10.666c.734 0 1.334.6 1.334 1.333v8c0 .733-.6 1.333-1.334 1.333H2.667c-.734 0-1.334-.6-1.334-1.333V4c0-.733.6-1.333 1.334-1.333z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M14.667 4 8 8.667 1.333 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg><span> Email</span>
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="form-input form-input-disabled"
                                   value="{{ $user->email }}"
                                   placeholder="mail@example.com"
                                   disabled
                                   readonly>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="label-icon">
                                    <path d="M14.667 11.28v2a1.333 1.333 0 0 1-1.454 1.333 13.2 13.2 0 0 1-5.753-2.046 13.001 13.001 0 0 1-4-4 13.2 13.2 0 0 1-2.047-5.78A1.333 1.333 0 0 1 2.74 1.333h2a1.333 1.333 0 0 1 1.333 1.147c.084.64.24 1.267.466 1.867a1.333 1.333 0 0 1-.3 1.406l-.846.847a10.667 10.667 0 0 0 4 4l.846-.847a1.333 1.333 0 0 1 1.407-.3c.6.227 1.227.383 1.867.467a1.333 1.333 0 0 1 1.147 1.353z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg><span> Phone</span>
                            </label>
                            <input type="tel"
                                   id="phone"
                                   name="phone"
                                   class="form-input"
                                   value="{{ old('phone', $user->phone) }}"
                                   placeholder="+421 900 123 456">
                            @error('phone')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="profile-section">
                    <h2 class="section-title">Address</h2>

                    <div class="form-group">
                        <label for="street_address" class="form-label">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="label-icon">
                                <path d="M14 6.667c0 4.666-6 8.666-6 8.666s-6-4-6-8.666a6 6 0 1 1 12 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 8.667a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg><span> Street Address</span>
                        </label>
                        <input type="text"
                               id="street_address"
                               name="street_address"
                               class="form-input"
                               value="{{ old('street_address', $user->street_address) }}"
                               placeholder="Main Street 123">
                        @error('street_address')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city" class="form-label">City</label>
                            <input type="text"
                                   id="city"
                                   name="city"
                                   class="form-input"
                                   value="{{ old('city', $user->city) }}"
                                   placeholder="Bratislava">
                            @error('city')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="postal_code" class="form-label">Postal Code</label>
                            <input type="text"
                                   id="postal_code"
                                   name="postal_code"
                                   class="form-input"
                                   value="{{ old('postal_code', $user->postal_code) }}"
                                   placeholder="811 01">
                            @error('postal_code')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="country" class="form-label">Country</label>
                        <input type="text"
                               id="country"
                               name="country"
                               class="form-input"
                               value="{{ old('country', $user->country) }}"
                               placeholder="Slovakia">
                        @error('country')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M12.667 14H3.333c-.733 0-1.333-.6-1.333-1.333V3.333c0-.733.6-1.333 1.333-1.333h7.334L14 5.333v7.334c0 .733-.6 1.333-1.333 1.333z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.333 14v-5.333H4.667V14M4.667 2v3.333h5.333" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Save Changes
                    </button>

                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="btn-logout">Sign Out</button>
                    </form>
                </div>
            </form>
        </div>
    </div>
@endsection
