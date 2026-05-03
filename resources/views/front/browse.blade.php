@extends('layouts.master')
@section('title', 'Browse Houses')
@section('content')
    <x-navbar />

    <main class="marketing-page">
        <section class="marketing-hero">
            <div class="marketing-panel marketing-hero-copy">
                <span class="marketing-badge">
                    <img src="{{ asset('assets/images/icons/building-3.svg') }}" alt="icon">
                    Mortgage Ready Catalog
                </span>
                <h1>Find a house that fits your KPR plan.</h1>
                <p>
                    Browse rumah demo berdasarkan lokasi, kategori, harga, dan kebutuhan keluarga sebelum lanjut ke simulasi cicilan.
                </p>
                <form action="{{ route('front.search') }}" class="marketing-search-form">
                    <label>
                        <span>Keyword</span>
                        <input type="text" name="keyword" placeholder="Nama rumah">
                    </label>
                    <label>
                        <span>Location</span>
                        <select name="city">
                            <option value="">All cities</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>Category</span>
                        <select name="category">
                            <option value="">All categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button type="submit">Search Houses</button>
                </form>
            </div>
            <div class="marketing-panel marketing-side-panel">
                <div>
                    <p class="marketing-label">Portfolio Scenario</p>
                    <h2>Customer can compare homes before sending a mortgage request.</h2>
                </div>
                <div class="marketing-stat-grid">
                    <div>
                        <strong>{{ $houses->count() }}</strong>
                        <span>Featured houses</span>
                    </div>
                    <div>
                        <strong>{{ $categories->count() }}</strong>
                        <span>Categories</span>
                    </div>
                    <div>
                        <strong>{{ $cities->count() }}</strong>
                        <span>Cities</span>
                    </div>
                </div>
                <a href="{{ route('front.rewards') }}" class="marketing-link-button">See KPR Rewards</a>
            </div>
        </section>

        <section class="marketing-section">
            <div class="marketing-section-header">
                <div>
                    <p class="marketing-label">Quick Browse</p>
                    <h2>Explore by category and city</h2>
                </div>
                <a href="{{ route('front.index') }}#Popular-Categories">View homepage categories</a>
            </div>
            <div class="quick-link-grid">
                @forelse ($categories as $category)
                    <a href="{{ route('front.category', $category->slug) }}" class="quick-link-card">
                        <img src="{{ $category->photo ? Storage::url($category->photo) : asset('assets/images/thumbnails/thumbnails-1.png') }}" alt="{{ $category->name }}">
                        <div>
                            <strong>{{ $category->name }}</strong>
                            <span>{{ $category->houses_count }} houses</span>
                        </div>
                    </a>
                @empty
                    <div class="marketing-empty">Belum ada kategori rumah.</div>
                @endforelse
            </div>
            <div class="chip-list">
                @forelse ($cities as $city)
                    <a href="{{ route('front.search', ['city' => $city->id]) }}">{{ $city->name }} <span>{{ $city->houses_count }}</span></a>
                @empty
                    <span>Belum ada data kota.</span>
                @endforelse
            </div>
        </section>

        <section class="marketing-section">
            <div class="marketing-section-header">
                <div>
                    <p class="marketing-label">Available Homes</p>
                    <h2>Mortgage ready houses</h2>
                </div>
                <a href="{{ route('front.search') }}">Open full search</a>
            </div>

            <div class="house-showcase-grid">
                @forelse ($houses as $house)
                    @php($lowestInterest = $house->interests->sortBy('interest')->first())
                    <a href="{{ route('front.details', $house->slug) }}" class="showcase-house-card">
                        <div class="showcase-house-thumb">
                            <img src="{{ $house->thumbnail ? Storage::url($house->thumbnail) : asset('assets/images/thumbnails/house-details-1.png') }}" alt="{{ $house->name }}">
                            <span>{{ $house->category?->name ?? 'House' }}</span>
                        </div>
                        <div class="showcase-house-body">
                            <div>
                                <h3>{{ $house->name }}</h3>
                                <p>{{ $house->city?->name ?? 'Location not set' }}</p>
                            </div>
                            <div class="showcase-house-meta">
                                <span>{{ $house->bedroom }} Bedroom</span>
                                <span>{{ $house->bathroom }} Bathroom</span>
                                <span>{{ $house->land_area }} m&sup2;</span>
                            </div>
                            <div class="showcase-house-price">
                                <strong>Rp {{ number_format($house->price, 0, ',', '.') }}</strong>
                                <span>
                                    @if ($lowestInterest)
                                        From {{ $lowestInterest->interest }}% with {{ $lowestInterest->bank?->name }}
                                    @else
                                        Interest data pending
                                    @endif
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="marketing-empty">Belum ada rumah demo. Jalankan seeder untuk mengisi katalog portfolio.</div>
                @endforelse
            </div>
        </section>
    </main>
@endsection
