@extends('layouts.master')
@section('title', 'Mortgage Rewards')
@section('content')
    <x-navbar />

    <main class="marketing-page">
        <section class="marketing-hero">
            <div class="marketing-panel marketing-hero-copy">
                <span class="marketing-badge">
                    <img src="{{ asset('assets/images/icons/big-rewards-n.svg') }}" alt="icon">
                    Mortgage Rewards
                </span>
                <h1>Benefits that make the KPR journey easier to finish.</h1>
                <p>
                    Halaman ini menjelaskan value proposition untuk customer: dari review dokumen, simulasi cicilan, sampai tracking pembayaran setelah pengajuan disetujui.
                </p>
                <div class="reward-highlight">
                    <div>
                        <span>Best demo interest</span>
                        <strong>{{ $bestInterest ? $bestInterest->interest . '%' : 'Pending' }}</strong>
                    </div>
                    <div>
                        <span>Partner banks</span>
                        <strong>{{ $banks->count() }}</strong>
                    </div>
                </div>
            </div>
            <div class="marketing-panel marketing-side-panel">
                <p class="marketing-label">Reward Flow</p>
                <div class="reward-step-list">
                    <div>
                        <span>01</span>
                        <strong>Browse eligible houses</strong>
                        <p>Customer memilih rumah yang sudah memiliki opsi cicilan dari bank.</p>
                    </div>
                    <div>
                        <span>02</span>
                        <strong>Submit KPR request</strong>
                        <p>Data pengajuan dan dokumen masuk ke dashboard admin/developer.</p>
                    </div>
                    <div>
                        <span>03</span>
                        <strong>Track installment</strong>
                        <p>Customer dapat melihat status kontrak dan cicilan dari dashboard.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="marketing-section">
            <div class="marketing-section-header">
                <div>
                    <p class="marketing-label">Customer Benefits</p>
                    <h2>Reward content for a KPR product</h2>
                </div>
                <a href="{{ route('front.browse') }}">Browse houses</a>
            </div>
            <div class="reward-card-grid">
                <article class="reward-card">
                    <img src="{{ asset('assets/images/icons/task-square.svg') }}" alt="icon">
                    <h3>Document Review</h3>
                    <p>Checklist dokumen membantu customer memahami kebutuhan pengajuan sebelum diproses admin.</p>
                </article>
                <article class="reward-card">
                    <img src="{{ asset('assets/images/icons/bank-interests-n.svg') }}" alt="icon">
                    <h3>Bank Comparison</h3>
                    <p>Opsi bunga dan tenor dari beberapa bank bisa dibandingkan sebelum customer memilih cicilan.</p>
                </article>
                <article class="reward-card">
                    <img src="{{ asset('assets/images/icons/cards-green.svg') }}" alt="icon">
                    <h3>Payment Tracking</h3>
                    <p>Status cicilan dan pembayaran Midtrans dapat ditampilkan sebagai bukti flow fullstack.</p>
                </article>
                <article class="reward-card">
                    <img src="{{ asset('assets/images/icons/security-safe-blue-fill.svg') }}" alt="icon">
                    <h3>Safer Demo Flow</h3>
                    <p>Data demo tetap terpisah dari produksi dan cocok ditunjukkan sebagai simulasi portfolio.</p>
                </article>
            </div>
        </section>

        <section class="marketing-section">
            <div class="marketing-section-header">
                <div>
                    <p class="marketing-label">Bank Partners</p>
                    <h2>Available demo mortgage programs</h2>
                </div>
                <a href="{{ route('front.search') }}">Find eligible homes</a>
            </div>
            <div class="bank-program-grid">
                @forelse ($banks as $bank)
                    <article class="bank-program-card">
                        <img src="{{ $bank->photo ? Storage::url($bank->photo) : asset('assets/images/logos/mandiri.svg') }}" alt="{{ $bank->name }}">
                        <div>
                            <h3>{{ $bank->name }}</h3>
                            <p>{{ $bank->interest_count }} KPR option{{ $bank->interest_count > 1 ? 's' : '' }} available in demo catalog.</p>
                        </div>
                    </article>
                @empty
                    <div class="marketing-empty">Belum ada program bank. Tambahkan data interest melalui seeder atau Filament.</div>
                @endforelse
            </div>
        </section>
    </main>
@endsection
