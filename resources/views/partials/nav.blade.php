<header>
    <nav class="nav-container">
        <div class="nav-brand">
            <a href="{{ url('/') }}" class="nav-brand-link">
                <img src="{{ url('images/logo.png') }}" alt="3DPrintShop Logo" class="nav-brand-logo">
                <div class="nav-brand-text">
                    <span class="nav-brand-title">3DPrintShop</span>
                    <span class="nav-brand-subtitle">DIGITAL AND PHYSICAL</span>
                </div>
            </a>
        </div>

        <form action="{{ route('search') }}" method="get" class="nav-search">
            <label for="nav-search-input" class="visually-hidden">Search</label>
            <div class="nav-search-wrap">
                <img src="{{ asset('images/magnifying-glass.png') }}"
                     alt="Search"
                     class="nav-search-icon">
                <input id="nav-search-input"
                       name="q"
                       type="search"
                       value="{{ request('q') }}"
                       class="nav-search-input"
                       placeholder="Search products...">
            </div>
        </form>

        <div class="nav-actions">
            <a href="{{ route('search') }}" class="nav-link nav-link-search" aria-label="Search">
                <img src="{{ asset('images/magnifying-glass.png') }}" alt="Search" class="nav-img">
            </a>
            <a href="{{ route('cart.show') }}" class="nav-link" aria-label="Cart">
                <img src="{{ asset('images/cart.png') }}" alt="Cart" class="nav-img">
            </a>
            <a href="{{ route('auth.show') }}" class="nav-link" aria-label="Account">
                <img src="{{ asset('images/account.png') }}" alt="Account" class="nav-img">
            </a>
        </div>
    </nav>
</header>
