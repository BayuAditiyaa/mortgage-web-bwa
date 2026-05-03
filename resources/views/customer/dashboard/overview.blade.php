@extends('layouts.customer-dashboard')
@section('title', 'Customer Overview')
@section('dashboard-active', 'overview')
@section('dashboard-heading', 'Overview')
@section('dashboard-subheading', 'Ringkasan perjalanan KPR dan cicilan rumah kamu.')
@section('dashboard-actions')
    <a href="{{ route('front.browse') }}" class="dashboard-action-button primary">Browse Houses</a>
@endsection

@section('dashboard-content')
    <section class="dashboard-stat-grid">
        <article class="dashboard-stat-card">
            <span>Total KPR</span>
            <strong>{{ $totalMortgages }}</strong>
            <p>Semua pengajuan yang pernah kamu buat.</p>
        </article>
        <article class="dashboard-stat-card">
            <span>Active Requests</span>
            <strong>{{ $activeMortgages }}</strong>
            <p>Pengajuan yang masih berjalan atau menunggu bank.</p>
        </article>
        <article class="dashboard-stat-card">
            <span>Approved</span>
            <strong>{{ $approvedMortgages }}</strong>
            <p>KPR yang sudah disetujui dan siap dicicil.</p>
        </article>
        <article class="dashboard-stat-card">
            <span>Paid Installments</span>
            <strong>{{ $paidInstallments }}</strong>
            <p>Total cicilan yang sudah tercatat lunas.</p>
        </article>
    </section>

    <section class="dashboard-two-column">
        <article class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <span>Current Snapshot</span>
                    <h2>Mortgage health</h2>
                </div>
                <a href="{{ route('dashboard.mortgages.index') }}">Open list</a>
            </div>
            @if ($latestMortgage)
                <div class="dashboard-feature-row">
                    <img src="{{ $latestMortgage->house?->thumbnail ? Storage::url($latestMortgage->house->thumbnail) : asset('assets/images/thumbnails/house-details-1.png') }}" alt="{{ $latestMortgage->house?->name }}">
                    <div>
                        <h3>{{ $latestMortgage->house?->name ?? 'Mortgage request' }}</h3>
                        <p>{{ $latestMortgage->bank_name }} - {{ $latestMortgage->interest }}% / {{ $latestMortgage->duration }} years</p>
                        <strong>Rp {{ number_format($latestMortgage->monthly_amount, 0, ',', '.') }} / month</strong>
                    </div>
                </div>
                <div class="dashboard-info-list">
                    <div>
                        <span>Status</span>
                        <strong>{{ $latestMortgage->status }}</strong>
                    </div>
                    <div>
                        <span>Remaining loan</span>
                        <strong>Rp {{ number_format($remainingLoanAmount, 0, ',', '.') }}</strong>
                    </div>
                </div>
            @else
                <div class="dashboard-empty-state">
                    <h3>Belum ada pengajuan KPR.</h3>
                    <p>Mulai dari katalog rumah, pilih program bank, lalu ajukan request KPR pertama kamu.</p>
                    <a href="{{ route('front.browse') }}" class="dashboard-action-button primary">Find a House</a>
                </div>
            @endif
        </article>

        <article class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <span>Recommended Flow</span>
                    <h2>KPR journey</h2>
                </div>
            </div>
            <ol class="dashboard-timeline">
                <li>
                    <span>01</span>
                    <div>
                        <strong>Compare rumah dan bunga bank</strong>
                        <p>Gunakan Browse dan Bank Interests untuk menemukan kombinasi rumah, DP, dan tenor yang cocok.</p>
                    </div>
                </li>
                <li>
                    <span>02</span>
                    <div>
                        <strong>Submit dokumen pengajuan</strong>
                        <p>Upload dokumen PDF pada halaman request mortgage agar admin bisa melakukan review.</p>
                    </div>
                </li>
                <li>
                    <span>03</span>
                    <div>
                        <strong>Bayar cicilan setelah approved</strong>
                        <p>Setelah status disetujui, pembayaran bisa dilanjutkan lewat dashboard cicilan.</p>
                    </div>
                </li>
            </ol>
        </article>
    </section>

    <section class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <span>Recent Requests</span>
                <h2>Latest mortgage activity</h2>
            </div>
        </div>
        <div class="dashboard-list">
            @forelse ($mortgages->take(4) as $mortgage)
                <a href="{{ route('dashboard.installment.details', $mortgage) }}" class="dashboard-list-item">
                    <div>
                        <strong>{{ $mortgage->house?->name ?? 'Mortgage request' }}</strong>
                        <span>{{ $mortgage->bank_name }} - Rp {{ number_format($mortgage->monthly_amount, 0, ',', '.') }} / month</span>
                    </div>
                    <em>{{ $mortgage->status }}</em>
                </a>
            @empty
                <div class="dashboard-empty-state compact">Belum ada aktivitas mortgage.</div>
            @endforelse
        </div>
    </section>
@endsection
