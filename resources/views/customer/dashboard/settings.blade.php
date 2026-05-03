@extends('layouts.customer-dashboard')
@section('title', 'Settings')
@section('dashboard-active', 'settings')
@section('dashboard-heading', 'Settings')
@section('dashboard-subheading', 'Kelola profil, keamanan akun, dan kesiapan data customer.')
@section('dashboard-actions')
    <a href="{{ route('profile.edit') }}" class="dashboard-action-button primary">Edit Profile</a>
@endsection

@section('dashboard-content')
    <section class="dashboard-two-column">
        <article class="dashboard-card profile-card">
            <div class="profile-avatar">
                <img src="{{ $user->photo ? Storage::url($user->photo) : asset('assets/images/icons/default-avatar.svg') }}" alt="{{ $user->name }}">
            </div>
            <div>
                <span>Customer Account</span>
                <h2>{{ $user->name }}</h2>
                <p>{{ $user->email }}</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="dashboard-action-button">Update Profile</a>
        </article>

        <article class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <span>Profile Readiness</span>
                    <h2>Checklist data customer</h2>
                </div>
            </div>
            <div class="settings-checklist">
                @foreach ($profileChecks as $label => $isCompleted)
                    <div class="{{ $isCompleted ? 'completed' : '' }}">
                        <span>{{ $isCompleted ? 'Done' : 'Todo' }}</span>
                        <strong>{{ $label }}</strong>
                    </div>
                @endforeach
            </div>
        </article>
    </section>

    <section class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <span>Security</span>
                <h2>Account safety settings</h2>
            </div>
            <a href="{{ route('profile.edit') }}">Open profile page</a>
        </div>
        <div class="dashboard-tip-grid">
            <div>
                <strong>Password</strong>
                <p>Gunakan halaman profile untuk mengganti password akun customer demo.</p>
            </div>
            <div>
                <strong>Email</strong>
                <p>Email dipakai untuk login dan identitas pengajuan KPR.</p>
            </div>
            <div>
                <strong>Documents</strong>
                <p>Dokumen pengajuan tetap diupload pada flow request mortgage, bukan di settings.</p>
            </div>
        </div>
    </section>
@endsection
