@extends('layouts.master')
@section('title', 'Customer Stories')
@section('content')
    <x-navbar />

    <main class="marketing-page">
        <section class="marketing-hero story-hero">
            <div class="marketing-panel marketing-hero-copy">
                <span class="marketing-badge">
                    <img src="{{ asset('assets/images/icons/book-cyan.svg') }}" alt="icon">
                    Customer Stories
                </span>
                <h1>Stories that explain the mortgage journey end to end.</h1>
                <p>
                    Halaman ini bisa menjadi konten edukasi: bagaimana pembeli memilih rumah, bagaimana developer mengelola listing, dan bagaimana admin memantau pengajuan KPR.
                </p>
                <a href="{{ route('front.browse') }}" class="marketing-link-button">Start from Browse</a>
            </div>
            <div class="story-image-panel">
                <img src="{{ asset('assets/images/backgrounds/hero-image.webp') }}" alt="family house">
            </div>
        </section>

        <section class="marketing-section">
            <div class="marketing-section-header">
                <div>
                    <p class="marketing-label">Case Studies</p>
                    <h2>Three angles for a portfolio demo</h2>
                </div>
                <a href="{{ route('front.rewards') }}">See rewards</a>
            </div>
            <div class="story-grid">
                <article class="story-card">
                    <span>Customer</span>
                    <h3>First home buyer compares KPR options</h3>
                    <p>Customer mencari rumah sesuai budget, melihat bunga bank, lalu mengirim request KPR dengan dokumen pendukung.</p>
                    <ul>
                        <li>Browse houses by category and city</li>
                        <li>Open house detail and select bank interest</li>
                        <li>Submit request from authenticated dashboard</li>
                    </ul>
                </article>
                <article class="story-card">
                    <span>Developer</span>
                    <h3>Property developer manages eligible homes</h3>
                    <p>Developer mengelola listing rumah dan hanya melihat request yang berasal dari rumah miliknya.</p>
                    <ul>
                        <li>Scoped Filament dashboard</li>
                        <li>House ownership by developer</li>
                        <li>Request visibility limited by listing</li>
                    </ul>
                </article>
                <article class="story-card">
                    <span>Admin</span>
                    <h3>Admin reviews mortgage status and payment</h3>
                    <p>Admin memantau seluruh pengajuan, status cicilan, serta transaksi pembayaran sebagai flow operasional.</p>
                    <ul>
                        <li>Centralized mortgage request review</li>
                        <li>Installment status tracking</li>
                        <li>Midtrans payment integration path</li>
                    </ul>
                </article>
            </div>
        </section>

        <section class="marketing-section">
            <div class="marketing-section-header">
                <div>
                    <p class="marketing-label">Featured Homes</p>
                    <h2>Stories connected to real demo listings</h2>
                </div>
                <a href="{{ route('front.search') }}">Search catalog</a>
            </div>
            <div class="story-list">
                @forelse ($houses as $house)
                    <a href="{{ route('front.details', $house->slug) }}" class="story-list-item">
                        <img src="{{ $house->thumbnail ? Storage::url($house->thumbnail) : asset('assets/images/thumbnails/house-details-1.png') }}" alt="{{ $house->name }}">
                        <div>
                            <span>{{ $house->category?->name ?? 'House' }} in {{ $house->city?->name ?? 'selected city' }}</span>
                            <h3>{{ $house->name }}</h3>
                            <p>{{ $house->developer?->name ?? 'Demo developer' }} can use this listing as a mortgage-ready project example.</p>
                        </div>
                    </a>
                @empty
                    <div class="marketing-empty">Belum ada listing untuk dijadikan story. Jalankan seeder demo terlebih dahulu.</div>
                @endforelse
            </div>
        </section>
    </main>
@endsection
