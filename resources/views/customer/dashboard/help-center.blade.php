@extends('layouts.customer-dashboard')
@section('title', 'Help Center')
@section('dashboard-active', 'help-center')
@section('dashboard-heading', 'Help Center')
@section('dashboard-subheading', 'Panduan singkat untuk memahami proses pengajuan KPR di aplikasi.')
@section('dashboard-actions')
    <a href="{{ route('dashboard.support') }}" class="dashboard-action-button primary">Contact Support</a>
@endsection

@section('dashboard-content')
    <section class="dashboard-two-column">
        <article class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <span>Before Submitting</span>
                    <h2>Dokumen yang perlu disiapkan</h2>
                </div>
            </div>
            <div class="document-checklist">
                <div><strong>KTP</strong><span>Identitas utama pemohon.</span></div>
                <div><strong>NPWP</strong><span>Validasi administrasi pajak.</span></div>
                <div><strong>Kartu Keluarga</strong><span>Informasi keluarga dan tanggungan.</span></div>
                <div><strong>Slip Gaji</strong><span>Estimasi kemampuan cicilan bulanan.</span></div>
                <div><strong>Rekening Koran</strong><span>Riwayat transaksi 3-6 bulan terakhir.</span></div>
            </div>
        </article>

        <article class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <span>Status Guide</span>
                    <h2>Arti status KPR</h2>
                </div>
            </div>
            <ol class="dashboard-timeline">
                <li>
                    <span>01</span>
                    <div>
                        <strong>Waiting for Bank</strong>
                        <p>Pengajuan sudah masuk dan sedang menunggu review dari pihak bank/admin.</p>
                    </div>
                </li>
                <li>
                    <span>02</span>
                    <div>
                        <strong>Approved</strong>
                        <p>Pengajuan diterima. Customer bisa mulai melihat jadwal dan melakukan cicilan.</p>
                    </div>
                </li>
                <li>
                    <span>03</span>
                    <div>
                        <strong>Paid Off</strong>
                        <p>Seluruh cicilan sudah selesai dan sisa pinjaman bernilai nol.</p>
                    </div>
                </li>
            </ol>
        </article>
    </section>

    <section class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <span>FAQ</span>
                <h2>Frequently asked questions</h2>
            </div>
        </div>
        <div class="faq-grid">
            <article>
                <strong>Apakah data di portfolio ini real?</strong>
                <p>Tidak. Data rumah, bank, dan reward dibuat sebagai simulasi agar alur aplikasi mudah dipahami.</p>
            </article>
            <article>
                <strong>Kapan tombol pembayaran muncul?</strong>
                <p>Tombol pembayaran muncul ketika status mortgage request sudah Approved dan pinjaman belum lunas.</p>
            </article>
            <article>
                <strong>Mengapa dokumen harus PDF?</strong>
                <p>Format PDF lebih konsisten untuk proses review dokumen dan validasi upload.</p>
            </article>
            <article>
                <strong>Apa bedanya Help Center dan Supports?</strong>
                <p>Help Center berisi panduan mandiri, sedangkan Supports untuk menghubungi tim bantuan.</p>
            </article>
        </div>
    </section>
@endsection
