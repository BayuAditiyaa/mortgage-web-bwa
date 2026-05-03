@extends('layouts.master')

@section('content')
    @php
        $activeMenu = trim($__env->yieldContent('dashboard-active', 'overview'));
        $menuGroups = [
            'GENERAL' => [
                ['key' => 'overview', 'label' => 'Overview', 'route' => 'dashboard.overview', 'icon' => 'overview-n.svg'],
                ['key' => 'mortgages', 'label' => 'Mortgages', 'route' => 'dashboard.mortgages.index', 'icon' => 'mortgages-y.svg'],
                ['key' => 'bank-interests', 'label' => 'Bank Interests', 'route' => 'dashboard.bank-interests', 'icon' => 'bank-interests-n.svg'],
                ['key' => 'rewards', 'label' => 'Big Rewards', 'route' => 'dashboard.rewards', 'icon' => 'big-rewards-n.svg'],
            ],
            'OTHERS' => [
                ['key' => 'help-center', 'label' => 'Help Center', 'route' => 'dashboard.help-center', 'icon' => 'help-center-n.svg'],
                ['key' => 'support', 'label' => 'Supports', 'route' => 'dashboard.support', 'icon' => 'supports-n.svg'],
                ['key' => 'settings', 'label' => 'Settings', 'route' => 'dashboard.settings', 'icon' => 'settings-n.svg'],
            ],
        ];
    @endphp

    <div class="dashboard-shell flex min-h-screen">
        <aside class="dashboard-sidebar h-screen min-w-[270px] overflow-y-auto bg-tedja-black text-white [&::-webkit-scrollbar]:hidden">
            <div class="flex h-full w-full flex-col gap-[40px] pt-[40px]">
                <div class="pl-[30px]">
                    <a href="{{ route('dashboard.overview') }}" class="shrink-0">
                        <img src="{{ asset('assets/images/logos/logo-white.svg') }}" alt="logo" />
                    </a>
                </div>
                <nav class="flex flex-col gap-[40px] pb-[40px] pl-[30px]">
                    @foreach ($menuGroups as $groupLabel => $items)
                        <section class="flex flex-col gap-[24px]">
                            <h3 class="text-sm font-semibold leading-[21px]">{{ $groupLabel }}</h3>
                            <ul class="flex flex-col gap-[24px]">
                                @foreach ($items as $item)
                                    <li>
                                        <a href="{{ route($item['route']) }}"
                                            class="dashboard-sidebar-link {{ $activeMenu === $item['key'] ? 'active' : '' }}">
                                            <img src="{{ asset('assets/images/icons/' . $item['icon']) }}" alt="icon" />
                                            <span>{{ $item['label'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endforeach
                </nav>
            </div>
        </aside>

        <div class="dashboard-main h-screen w-full overflow-y-auto pt-[30px] px-[30px]">
            <section id="NavTop" class="flex w-full items-center justify-between bg-white p-4 rounded-3xl">
                <button type="button"
                    class="dashboard-sidebar-toggle hidden rounded-full border border-tedja-black py-[12px] px-5 font-semibold">
                    Menu
                </button>
                <form action="{{ route('front.search') }}" class="relative">
                    <button type="submit" class="absolute right-5 top-1/2 shrink-0 translate-y-[-50%]">
                        <img src="{{ asset('assets/images/icons/search.svg') }}" alt="icon" />
                    </button>
                    <input type="text" name="keyword"
                        class="w-[440px] placeholder:font-normal placeholder:text-base placeholder:leading-[24px] placeholder:text-tedja-secondary rounded-full border border-tedja-black py-[14px] pl-5 focus:outline-none pr-[64px] outline-none focus:ring-[2px] focus:ring-tedja-blue focus:border-transparent transition-all duration-300"
                        placeholder="Search houses" />
                </form>
                <div class="flex items-center gap-5">
                    <div class="flex items-center gap-[12px]">
                        <a href="{{ route('dashboard.support') }}" class="shrink-0">
                            <div class="p-[13px] rounded-full border border-[#F2F2F4] hover:ring-[2px] hover:ring-tedja-blue transition-all duration-300">
                                <img src="{{ asset('assets/images/icons/device-message.svg') }}" alt="icon" />
                            </div>
                        </a>
                        <a href="{{ route('dashboard.rewards') }}" class="shrink-0">
                            <div class="p-[13px] rounded-full border border-[#F2F2F4] hover:ring-[2px] hover:ring-tedja-blue transition-all duration-300">
                                <img src="{{ asset('assets/images/icons/cup.svg') }}" alt="icon" />
                            </div>
                        </a>
                        <a href="{{ route('dashboard.settings') }}" class="shrink-0">
                            <div class="p-[13px] rounded-full border border-[#F2F2F4] hover:ring-[2px] hover:ring-tedja-blue transition-all duration-300">
                                <img src="{{ asset('assets/images/icons/folder-favorite.svg') }}" alt="icon" />
                            </div>
                        </a>
                    </div>
                    <div class="w-px bg-[#F2F2F4] h-[50px]"></div>
                    <button id="Profile" class="relative">
                        <div class="flex items-center gap-[14px]">
                            <div class="flex text-right flex-col gap-0.5">
                                <p class="text-sm text-tedja-secondary">Howdy,</p>
                                <p class="font-semibold">{{ Auth::user()->name }}</p>
                            </div>
                            <div class="flex rounded-full size-[50px] overflow-hidden">
                                <img src="{{ Auth::user()->photo ? Storage::url(Auth::user()->photo) : asset('assets/images/icons/default-avatar.svg') }}"
                                    class="w-full h-full object-cover" alt="photo">
                            </div>
                        </div>
                        <ul
                            class="hidden absolute top-full mt-[10px] right-0 flex flex-col w-[170px] shrink-0 h-fit text-left rounded-xl border border-tedja-border py-5 px-5 bg-white shadow-[0px_10px_30px_0px_#B8B8B840] gap-[14px]">
                            <li><a href="{{ route('dashboard.rewards') }}" class="hover:text-tedja-blue transition-all duration-300">Rewards</a></li>
                            <li><a href="{{ route('dashboard.mortgages.index') }}" class="hover:text-tedja-blue transition-all duration-300">My Mortgages</a></li>
                            <li><a href="{{ route('dashboard.help-center') }}" class="hover:text-tedja-blue transition-all duration-300">Help Center</a></li>
                            <li><a href="{{ route('dashboard.settings') }}" class="hover:text-tedja-blue transition-all duration-300">Settings</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}" class="hover:text-tedja-blue transition-all duration-300"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </button>
                </div>
            </section>

            <main class="dashboard-content flex w-full flex-col justify-center gap-[30px] py-[28px]">
                <header class="dashboard-page-header flex items-center justify-between">
                    <div class="flex flex-col gap-[6px]">
                        <h1 class="text-[26px] font-bold leading-[39px]">@yield('dashboard-heading', 'Dashboard')</h1>
                        <p class="text-sm leading-[21px] text-tedja-secondary">@yield('dashboard-subheading')</p>
                    </div>
                    <div class="buttons flex gap-[12px]">
                        @yield('dashboard-actions')
                    </div>
                </header>

                @yield('dashboard-content')
            </main>
        </div>
    </div>
@endsection
