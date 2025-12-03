@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="home-layout">
        <aside class="filters-sidebar">
            <div class="filters-card">
                <h3 class="filters-title">Filtre</h3>

                <div class="filters-section">
                    <h4 class="filters-section-title">Typ produktu</h4>
                    <label class="checkbox-row"><input type="checkbox" name="type[]" value="digital" checked> Digitálny model</label>
                    <label class="checkbox-row"><input type="checkbox" name="type[]" value="physical" checked> Fyzický model</label>
                </div>

                <div class="filters-divider"></div>

                <div class="filters-section">
                    <h4 class="filters-section-title">Kategórie</h4>
                    <label class="checkbox-row"><input type="checkbox" name="cat[]" value="miniatures"> Miniatúry</label>
                    <label class="checkbox-row"><input type="checkbox" name="cat[]" value="architecture"> Architektúra</label>
                    <label class="checkbox-row"><input type="checkbox" name="cat[]" value="art"> Umenie &amp; Sochy</label>
                    <label class="checkbox-row"><input type="checkbox" name="cat[]" value="functional"> Funkčné predmety</label>
                    <label class="checkbox-row"><input type="checkbox" name="cat[]" value="toys"> Hračky &amp; Figúrky</label>
                </div>

                <div class="filters-divider"></div>

                <div class="filters-section">
                    <h4 class="filters-section-title">Cenové rozpätie</h4>
                    <div class="price-range">
                        <div id="price-slider"></div>
                        <div class="price-values">
                            <span id="price-min-label">0€</span>
                            <span id="price-max-label">100€</span>
                        </div>
                    </div>
                </div>

            </div>
        </aside>

        <section class="products-area">
            <div class="products-header">
                <h2>Produkty</h2>
            </div>

            <div class="products-grid" id="products-grid">
                <!-- Placeholder cards -->
                @for ($i = 0; $i < 8; $i++)
                    <article class="product-card">
                        <div class="product-img-placeholder"></div>
                        <h3 class="product-title">Produkt {{ $i + 1 }}</h3>
                        <p class="product-price">€{{ 5 * ($i + 1) }}</p>
                    </article>
                @endfor
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/filter.js'])
@endpush
