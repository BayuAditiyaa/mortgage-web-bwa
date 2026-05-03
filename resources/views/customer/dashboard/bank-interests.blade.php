@extends('layouts.customer-dashboard')
@section('title', 'Bank Interests')
@section('dashboard-active', 'bank-interests')
@section('dashboard-heading', 'Bank Interests')
@section('dashboard-subheading', 'Bandingkan bunga, tenor, dan rumah yang sudah terhubung dengan program KPR.')
@section('dashboard-actions')
    <a href="{{ route('front.browse') }}" class="dashboard-action-button primary">Browse Houses</a>
@endsection

@section('dashboard-content')
    @php
        $lowestInterest = $interests->first();
    @endphp

    <section class="dashboard-stat-grid three">
        <article class="dashboard-stat-card">
            <span>Partner Banks</span>
            <strong>{{ $banks->count() }}</strong>
            <p>Bank demo yang memiliki program bunga KPR.</p>
        </article>
        <article class="dashboard-stat-card">
            <span>KPR Programs</span>
            <strong>{{ $interests->count() }}</strong>
            <p>Opsi bunga dan tenor yang bisa dipilih customer.</p>
        </article>
        <article class="dashboard-stat-card">
            <span>Lowest Interest</span>
            <strong>{{ $lowestInterest ? $lowestInterest->interest . '%' : '-' }}</strong>
            <p>{{ $lowestInterest?->bank?->name ?? 'Belum ada data bunga bank.' }}</p>
        </article>
    </section>

    <section class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <span>Bank Comparison</span>
                <h2>Available mortgage programs</h2>
            </div>
        </div>

        <div class="interest-program-grid">
            @forelse ($interests as $interest)
                <article class="interest-program-card">
                    <div class="interest-bank">
                        <img src="{{ $interest->bank?->photo ? Storage::url($interest->bank->photo) : asset('assets/images/logos/mandiri.svg') }}" alt="{{ $interest->bank?->name }}">
                        <div>
                            <strong>{{ $interest->bank?->name ?? 'Bank partner' }}</strong>
                            <span>{{ $interest->duration }} years tenor</span>
                        </div>
                    </div>
                    <div class="interest-rate">
                        <strong>{{ $interest->interest }}%</strong>
                        <span>annual interest</span>
                    </div>
                    <div class="interest-house">
                        <span>{{ $interest->house?->category?->name ?? 'House' }}</span>
                        <h3>{{ $interest->house?->name ?? 'Mortgage ready house' }}</h3>
                        <p>{{ $interest->house?->city?->name ?? 'Location pending' }} - Rp {{ number_format($interest->house?->price ?? 0, 0, ',', '.') }}</p>
                    </div>
                    @if ($interest->house)
                        <a href="{{ route('front.details', $interest->house->slug) }}" class="dashboard-action-button">View House</a>
                    @endif
                </article>
            @empty
                <div class="dashboard-empty-state compact">Belum ada program bunga bank. Tambahkan Interest melalui Filament atau seeder demo.</div>
            @endforelse
        </div>
    </section>

    <section class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <span>How to Decide</span>
                <h2>Tips memilih program KPR</h2>
            </div>
        </div>
        <div class="dashboard-tip-grid">
            <div>
                <strong>Interest</strong>
                <p>Bunga lebih rendah biasanya membuat cicilan lebih ringan, tapi tetap cek tenor dan syarat bank.</p>
            </div>
            <div>
                <strong>Tenor</strong>
                <p>Tenor panjang menurunkan cicilan bulanan, tetapi total pembayaran bisa lebih besar.</p>
            </div>
            <div>
                <strong>DP</strong>
                <p>DP lebih besar mengurangi pokok pinjaman dan membantu customer terlihat lebih siap secara finansial.</p>
            </div>
        </div>
    </section>
@endsection
