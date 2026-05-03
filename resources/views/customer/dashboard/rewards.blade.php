@extends('layouts.customer-dashboard')
@section('title', 'Big Rewards')
@section('dashboard-active', 'rewards')
@section('dashboard-heading', 'Big Rewards')
@section('dashboard-subheading', 'Benefit demo untuk customer yang menyelesaikan milestone KPR.')
@section('dashboard-actions')
    <a href="{{ route('dashboard.mortgages.index') }}" class="dashboard-action-button primary">My Mortgages</a>
@endsection

@section('dashboard-content')
    <section class="dashboard-two-column rewards-hero-grid">
        <article class="dashboard-card reward-balance-card">
            <span>Reward Balance</span>
            <strong>{{ number_format($rewardPoints, 0, ',', '.') }} pts</strong>
            <p>{{ $currentTier }}</p>
            <div class="reward-progress">
                <span style="width: {{ min(100, ($rewardPoints / 2000) * 100) }}%"></span>
            </div>
            <small>{{ max(0, 2000 - $rewardPoints) }} points to Priority Homeowner tier.</small>
        </article>

        <article class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <span>Earn Points</span>
                    <h2>Reward rules</h2>
                </div>
            </div>
            <div class="dashboard-info-list">
                <div>
                    <span>Approved mortgage</span>
                    <strong>+500 pts</strong>
                </div>
                <div>
                    <span>Paid installment</span>
                    <strong>+100 pts</strong>
                </div>
                <div>
                    <span>Current approved</span>
                    <strong>{{ $approvedMortgages }}</strong>
                </div>
                <div>
                    <span>Paid installment</span>
                    <strong>{{ $paidInstallments }}</strong>
                </div>
            </div>
        </article>
    </section>

    <section class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <span>Benefits</span>
                <h2>Customer perks for KPR journey</h2>
            </div>
        </div>
        <div class="reward-perk-grid">
            <article>
                <img src="{{ asset('assets/images/icons/task-square.svg') }}" alt="icon">
                <strong>Document Priority Review</strong>
                <p>Customer dengan pengajuan lengkap bisa mendapatkan review dokumen lebih cepat.</p>
            </article>
            <article>
                <img src="{{ asset('assets/images/icons/cards-green.svg') }}" alt="icon">
                <strong>Installment Cashback Simulation</strong>
                <p>Demo benefit yang bisa dikembangkan menjadi cashback atau voucher partner.</p>
            </article>
            <article>
                <img src="{{ asset('assets/images/icons/security-safe-blue-fill.svg') }}" alt="icon">
                <strong>Verified Buyer Badge</strong>
                <p>Badge customer yang sudah menyelesaikan milestone pengajuan dan pembayaran.</p>
            </article>
        </div>
    </section>

    <section class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <span>Milestones</span>
                <h2>Reward progress by mortgage</h2>
            </div>
        </div>
        <div class="dashboard-list">
            @forelse ($mortgages as $mortgage)
                <a href="{{ route('dashboard.installment.details', $mortgage) }}" class="dashboard-list-item">
                    <div>
                        <strong>{{ $mortgage->house?->name ?? 'Mortgage request' }}</strong>
                        <span>{{ $mortgage->status }} - {{ $mortgage->installments->where('is_paid', true)->count() }} paid installments</span>
                    </div>
                    <em>{{ $mortgage->status === 'Approved' ? '+500 pts' : 'Pending' }}</em>
                </a>
            @empty
                <div class="dashboard-empty-state compact">Belum ada mortgage untuk dihitung reward-nya.</div>
            @endforelse
        </div>
    </section>
@endsection
