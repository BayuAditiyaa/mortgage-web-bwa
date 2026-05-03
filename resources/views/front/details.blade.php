@extends('layouts.master')
@section('title', 'House Details page')
@section('content')
    <x-navbar />
    <div class="mt-[164px] flex flex-col gap-5 text-center items-center">
        <p class="flex items-center gap-[6px] rounded-full py-[6px] px-3 bg-white border border-tedja-border">
            <img src="{{ asset('assets/images/icons/crown.svg') }}" class="flex shrink-0 size-5" alt="icon">
            <span class="font-semibold text-sm">Top Well-Designed House</span>
        </p>
        <h1 class="font-bold text-4xl leading-[54px]">{{ $houseDetails->name }}</h1>
        <div class="flex items-center justify-center gap-5">
            <div class="flex items-center gap-[6px]">
                <img src="{{ asset('assets/images/icons/location.svg') }}" class="size-6 flex shrink-0" alt="icon">
                <p class="font-semibold">{{ $houseDetails->category->name }}, {{ $houseDetails->city->name }}</p>
            </div>
            <div class="flex items-center gap-[6px]">
                <img src="{{ asset('assets/images/icons/security-user.svg') }}" class="size-6 flex shrink-0" alt="icon">
                <p class="font-semibold">Certified Developer</p>
            </div>
        </div>
    </div>
    @php
        $mapQuery = trim(($houseDetails->name ?? '') . ' ' . ($houseDetails->city->name ?? ''));
        $googleMapsKey = config('services.google_maps.key');
    @endphp

    <section id="Gallery" class="flex gap-5 w-full max-w-[1280px] h-[450px] px-[75px] mt-[50px] mx-auto">
        <button class="show-modal-btn relative group flex w-full h-[450px] rounded-[30px] overflow-hidden">
            <img src="{{ Storage::url($houseDetails->thumbnail) }}" class="w-full h-full object-cover"
                alt="house thumbnail">
            <div
                class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                <img src="{{ asset('assets/images/icons/eye-white-fill.svg') }}" class="size-[50px]" alt="icon">
            </div>
        </button>
        <div class="grid grid-cols-2 gap-5 w-[450px] shrink-0">
            @foreach ($houseDetails->photos as $photo)
                <button class="show-modal-btn relative group flex size-[215px] shrink-0 rounded-[22px] overflow-hidden">
                    <img src="{{ Storage::url($photo->photo) }}" class="w-full h-full object-cover" alt="house details">
                    <div
                        class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                        <img src="{{ asset('assets/images/icons/eye-white-fill.svg') }}" class="size-[50px]" alt="icon">
                    </div>
                </button>
            @endforeach

        </div>
    </section>
    <section id="specs" class="w-full max-w-[1280px] px-[75px] mt-[30px] mx-auto">
        <div class="flex items-center justify-between rounded-[20px] border border-tedja-border py-5 px-[30px] bg-white">
            <div class="flex flex-col w-fit gap-3">
                <p class="text-sm text-tedja-secondary">Bedroom</p>
                <div class="flex items-center gap-[6px]">
                    <img src="{{ asset('assets/images/icons/slider-vertical.svg') }}" class="size-6 flex shrink-0"
                        alt="icon">
                    <p class="font-semibold">{{ $houseDetails->bedroom }} Bedroom</p>
                </div>
            </div>
            <div class="h-[60px] border border-tedja-border"></div>
            <div class="flex flex-col w-fit gap-3">
                <p class="text-sm text-tedja-secondary">Bathroom</p>
                <div class="flex items-center gap-[6px]">
                    <img src="{{ asset('assets/images/icons/slider-vertical.svg') }}" class="size-6 flex shrink-0"
                        alt="icon">
                    <p class="font-semibold">{{ $houseDetails->bathroom }} Bathroom</p>
                </div>
            </div>
            <div class="h-[60px] border border-tedja-border"></div>
            <div class="flex flex-col w-fit gap-3">
                <p class="text-sm text-tedja-secondary">Certificate</p>
                <div class="flex items-center gap-[6px]">
                    <img src="{{ asset('assets/images/icons/note-favorite.svg') }}" class="size-6 flex shrink-0"
                        alt="icon">
                    <p class="font-semibold">{{ $houseDetails->certificate }}</p>
                </div>
            </div>
            <div class="h-[60px] border border-tedja-border"></div>
            <div class="flex flex-col w-fit gap-3">
                <p class="text-sm text-tedja-secondary">Land of Area</p>
                <div class="flex items-center gap-[6px]">
                    <img src="{{ asset('assets/images/icons/maximize-3.svg') }}" class="size-6 flex shrink-0"
                        alt="icon">
                    <p class="font-semibold">{{ $houseDetails->land_area }} m&sup2;</p>
                </div>
            </div>
            <div class="h-[60px] border border-tedja-border"></div>
            <div class="flex flex-col w-fit gap-3">
                <p class="text-sm text-tedja-secondary">Land of Building</p>
                <div class="flex items-center gap-[6px]">
                    <img src="{{ asset('assets/images/icons/building-3.svg') }}" class="size-6 flex shrink-0"
                        alt="icon">
                    <p class="font-semibold">{{ $houseDetails->building_area }} m&sup2;</p>
                </div>
            </div>
            <div class="h-[60px] border border-tedja-border"></div>
            <div class="flex flex-col w-fit gap-3">
                <p class="text-sm text-tedja-secondary">Electric Power</p>
                <div class="flex items-center gap-[6px]">
                    <img src="{{ asset('assets/images/icons/flash.svg') }}" class="size-6 flex shrink-0" alt="icon">
                    <p class="font-semibold">{{ $houseDetails->electric }} Watt</p>
                </div>
            </div>
        </div>
    </section>
    <section id="Details" class="w-full flex gap-[70px] max-w-[1280px] px-[75px] my-[50px] mx-auto">
        <div class="flex flex-col gap-[30px]">
            <div id="About" class="flex flex-col gap-[14px]">
                <h2 class="font-semibold text-[22px] leading-[33px]">About Project</h2>
                <p class="leading-8">{{ $houseDetails->about }}.</p>
            </div>
            <div id="Nerby-Facilities" class="flex flex-col gap-[14px]">
                <h2 class="font-semibold text-[22px] leading-[33px]">Nearby Facilities</h2>
                <div class="grid grid-cols-4 gap-5">
                    @foreach ($houseDetails->facilities as $facility)
                        <div
                            class="flex flex-col min-h-[140px] rounded-[20px] border border-tedja-border p-5 gap-5 bg-white">
                            <img src="{{ Storage::url($facility->facility->photo) }}" class="size-8 flex shrink-0"
                                alt="icon">
                            <p class="font-semibold">{{ $facility->facility->name }}</p>
                        </div>
                    @endforeach

                </div>
            </div>
            <div id="Location" class="flex flex-col gap-[14px]">
                <h2 class="font-semibold text-[22px] leading-[33px]">Strategic Location</h2>
                <div class="overflow-hidden w-full h-[320px]">
                    <div id="my-map-display" class="h-full w-full max-w-[none] bg-none">
                        @if ($googleMapsKey)
                            <iframe class="h-full w-full border-0" frameborder="0"
                                src="https://www.google.com/maps/embed/v1/place?q={{ urlencode($mapQuery) }}&key={{ $googleMapsKey }}"></iframe>
                        @else
                            <div class="flex h-full w-full flex-col items-center justify-center gap-4 rounded-[20px] border border-tedja-border bg-white p-6 text-center">
                                <p class="font-semibold">Map preview is not configured yet.</p>
                                <a class="rounded-full bg-tedja-green px-5 py-[14px] font-semibold"
                                    href="https://www.google.com/maps/search/?api=1&query={{ urlencode($mapQuery) }}"
                                    target="_blank" rel="noopener">
                                    Open in Google Maps
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col w-[400px] shrink-0 h-fit rounded-[30px] border border-tedja-border p-5 gap-5 bg-white">
            <p class="font-bold text-[38px] leading-[57px] text-center text-tedja-blue">Rp {{ number_format($houseDetails->price, 0, ' ', '.') }}</p>
            <hr class="border-x-tedja-border">
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-[6px]">
                    <img src="{{ asset('assets/images/icons/tick-circle.svg') }}" class="size-6 flex shrink-0"
                        alt="icon">
                    <p class="font-semibold">Dibangun developer handal</p>
                </div>
                <div class="flex items-center gap-[6px]">
                    <img src="{{ asset('assets/images/icons/tick-circle.svg') }}" class="size-6 flex shrink-0"
                        alt="icon">
                    <p class="font-semibold">Jaminan uang kembali 100%</p>
                </div>
                <div class="flex items-center gap-[6px]">
                    <img src="{{ asset('assets/images/icons/tick-circle.svg') }}" class="size-6 flex shrink-0"
                        alt="icon">
                    <p class="font-semibold">Gratis biaya balik nama</p>
                </div>
            </div>
            <hr class="border-x-tedja-border">
            <p class="font-semibold">Available Mortgages</p>
            @forelse ($houseDetails->interests as $interest )
                            <div
                class="group flex items-center justify-between rounded-[20px] ring-1 ring-tedja-border p-4 bg-white hover:ring-2 hover:ring-tedja-blue transition-all duration-300">
                <div class="flex items-center gap-[14px]">
                    <div class="flex items-center w-[71px] h-[50px] shrink-0 overflow-hidden">
                        <img src="{{ Storage::url($interest->bank->photo) }}" class="w-full h-full object-contain"
                            alt="bank logo">
                    </div>
                    <div>
                        <p class="font-semibold">{{ $interest->bank->name }}</p>
                        <p class="text-sm text-tedja-secondary mt-0.5">Interest {{ $interest->interest }}%</p>
                    </div>
                </div>
                <a href="{{ route('front.interest', $interest->id) }}"
                    class="rounded-full py-[6px] px-3 bg-tedja-green font-semibold text-sm opacity-0 group-hover:opacity-100 transition-all duration-300">
                    Simulate
                </a>
            </div>
            @empty
                Belum ada interest
            @endforelse

            <hr class="border-x-tedja-border">
            <section id="PublicMortgageSimulator" class="flex flex-col gap-4 rounded-[20px] border border-tedja-border p-4">
                <div class="flex flex-col gap-1">
                    <p class="font-semibold">Quick KPR Simulator</p>
                    <p class="text-sm text-tedja-secondary">Estimate monthly payment before signing in.</p>
                </div>
                <input id="simulator-house-price" type="hidden" value="{{ $houseDetails->price }}">
                <label class="flex flex-col gap-2">
                    <span class="text-sm text-tedja-secondary">Down Payment</span>
                    <select id="simulator-dp"
                        class="appearance-none outline-none w-full rounded-full ring-1 ring-tedja-black py-[14px] px-5 font-semibold focus:ring-2 focus:ring-tedja-blue transition-all duration-300">
                        <option value="10">10%</option>
                        <option value="20" selected>20%</option>
                        <option value="40">40%</option>
                        <option value="50">50%</option>
                        <option value="60">60%</option>
                        <option value="80">80%</option>
                    </select>
                </label>
                <label class="flex flex-col gap-2">
                    <span class="text-sm text-tedja-secondary">Annual Interest</span>
                    <select id="simulator-interest"
                        class="appearance-none outline-none w-full rounded-full ring-1 ring-tedja-black py-[14px] px-5 font-semibold focus:ring-2 focus:ring-tedja-blue transition-all duration-300">
                        @forelse ($houseDetails->interests as $interest)
                            <option value="{{ $interest->interest }}" data-duration="{{ $interest->duration }}">
                                {{ $interest->bank->name }} - {{ $interest->interest }}% / {{ $interest->duration }} years
                            </option>
                        @empty
                            <option value="6" data-duration="10">Sample 6% / 10 years</option>
                        @endforelse
                    </select>
                </label>
                <div class="flex flex-col gap-3 rounded-[18px] bg-[#F2F2F4] p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-tedja-secondary">Loan Amount</span>
                        <strong id="simulator-loan">Rp 0</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-tedja-secondary">Monthly Payment</span>
                        <strong id="simulator-monthly" class="text-tedja-blue">Rp 0</strong>
                    </div>
                </div>
            </section>

            <hr class="border-x-tedja-border">
            <div class="flex items-center justify-center gap-[6px]">
                <img src="{{ asset('assets/images/icons/security-safe-blue-fill.svg') }}" class="size-6 flex shrink-0"
                    alt="icon">
                <p class="font-semibold">All your privacy data secured</p>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div id="Gallery-Modal" class="fixed inset-0 items-center justify-center bg-tedja-black/50 flex z-30 hidden">
        <div id="Modal-Content" class="rounded-[50px] flex flex-col gap-5 py-[40px]">
            <div class="flex max-w-[900px] max-h-[600px] overflow-hidden">
                <img src="{{ Storage::url($houseDetails->thumbnail) }}" class="object-contain"
                    alt="thumbnail">
            </div>
            <button id="closeModal"
                class="px-5 mx-auto py-[14px] !w-fit bg-tedja-red rounded-full font-semibold text-white">
                Close
            </button>
        </div>
    </div>
@endsection

@push('after-styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
@endpush

@push('after-scripts')
    <script src="{{ asset('js/gallery.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const priceInput = document.getElementById('simulator-house-price');
            const dpSelect = document.getElementById('simulator-dp');
            const interestSelect = document.getElementById('simulator-interest');
            const loanOutput = document.getElementById('simulator-loan');
            const monthlyOutput = document.getElementById('simulator-monthly');

            if (!priceInput || !dpSelect || !interestSelect || !loanOutput || !monthlyOutput) {
                return;
            }

            const formatCurrency = (value) => `Rp ${Math.round(value).toLocaleString('id-ID')}`;

            const updateSimulator = () => {
                const housePrice = parseFloat(priceInput.value) || 0;
                const dpPercentage = parseFloat(dpSelect.value) || 0;
                const annualInterest = parseFloat(interestSelect.value) || 0;
                const durationYears = parseFloat(interestSelect.selectedOptions[0]?.dataset.duration) || 1;
                const loanAmount = housePrice - (housePrice * (dpPercentage / 100));
                const totalPayments = durationYears * 12;
                const monthlyInterestRate = annualInterest / 100 / 12;
                const denominator = Math.pow(1 + monthlyInterestRate, totalPayments) - 1;
                const monthlyPayment = denominator > 0
                    ? (loanAmount * monthlyInterestRate * Math.pow(1 + monthlyInterestRate, totalPayments)) / denominator
                    : loanAmount / totalPayments;

                loanOutput.textContent = formatCurrency(loanAmount);
                monthlyOutput.textContent = formatCurrency(monthlyPayment);
            };

            dpSelect.addEventListener('change', updateSimulator);
            interestSelect.addEventListener('change', updateSimulator);
            updateSimulator();
        });
    </script>
@endpush
