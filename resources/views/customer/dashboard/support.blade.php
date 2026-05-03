@extends('layouts.customer-dashboard')
@section('title', 'Supports')
@section('dashboard-active', 'support')
@section('dashboard-heading', 'Supports')
@section('dashboard-subheading', 'Kanal bantuan demo untuk pertanyaan pengajuan, dokumen, dan pembayaran.')
@section('dashboard-actions')
    <a href="{{ route('dashboard.help-center') }}" class="dashboard-action-button primary">Open Help Center</a>
@endsection

@section('dashboard-content')
    <section class="dashboard-stat-grid three">
        <article class="dashboard-stat-card">
            <span>Response Target</span>
            <strong>1 day</strong>
            <p>Simulasi SLA untuk pertanyaan pengajuan KPR.</p>
        </article>
        <article class="dashboard-stat-card">
            <span>Support Topics</span>
            <strong>3</strong>
            <p>Dokumen, status bank, dan pembayaran cicilan.</p>
        </article>
        <article class="dashboard-stat-card">
            <span>Your Requests</span>
            <strong>{{ $mortgages->count() }}</strong>
            <p>Mortgage terbaru yang bisa kamu rujuk saat menghubungi support.</p>
        </article>
    </section>

    <section class="dashboard-two-column">
        <article class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <span>Contact Options</span>
                    <h2>Pilih bantuan yang sesuai</h2>
                </div>
            </div>
            <div class="support-channel-list">
                <a href="mailto:support@mortgage.test">
                    <img src="{{ asset('assets/images/icons/sms.svg') }}" alt="icon">
                    <div>
                        <strong>Email Support</strong>
                        <span>support@mortgage.test</span>
                    </div>
                </a>
                <a href="{{ route('dashboard.help-center') }}">
                    <img src="{{ asset('assets/images/icons/messages.svg') }}" alt="icon">
                    <div>
                        <strong>Guided Help</strong>
                        <span>Baca panduan dokumen dan status KPR.</span>
                    </div>
                </a>
                <a href="{{ route('dashboard.bank-interests') }}">
                    <img src="{{ asset('assets/images/icons/bank-interests-n.svg') }}" alt="icon">
                    <div>
                        <strong>Bank Program Question</strong>
                        <span>Cek bunga dan tenor sebelum membuat pengajuan baru.</span>
                    </div>
                </a>
            </div>
        </article>

        <article class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <span>Ticket Draft</span>
                    <h2>Informasi yang perlu dikirim</h2>
                </div>
            </div>
            <div class="support-ticket-card">
                <p>Untuk portfolio, form tiket ini bisa dikembangkan menjadi fitur CRUD support ticket.</p>
                <ul>
                    <li>Nama rumah atau nomor pengajuan.</li>
                    <li>Status terakhir yang terlihat di dashboard.</li>
                    <li>Screenshot error pembayaran atau upload dokumen.</li>
                    <li>Kontak email yang aktif.</li>
                </ul>
            </div>
        </article>
    </section>

    <section class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <span>Recent Mortgage References</span>
                <h2>Attach a request to your message</h2>
            </div>
        </div>
        <div class="dashboard-list">
            @forelse ($mortgages as $mortgage)
                <a href="{{ route('dashboard.installment.details', $mortgage) }}" class="dashboard-list-item">
                    <div>
                        <strong>{{ $mortgage->house?->name ?? 'Mortgage request' }}</strong>
                        <span>{{ $mortgage->status }} - {{ $mortgage->bank_name }}</span>
                    </div>
                    <em>Open</em>
                </a>
            @empty
                <div class="dashboard-empty-state compact">Belum ada pengajuan yang bisa dilampirkan.</div>
            @endforelse
        </div>
    </section>
@endsection
