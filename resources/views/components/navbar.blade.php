        @php
            $navItems = [
                ['label' => 'Home', 'route' => 'front.index', 'active' => 'front.index'],
                ['label' => 'Browse', 'route' => 'front.browse', 'active' => ['front.browse', 'front.category', 'front.search', 'front.details']],
                ['label' => 'Rewards', 'route' => 'front.rewards', 'active' => 'front.rewards'],
                ['label' => 'Stories', 'route' => 'front.stories', 'active' => 'front.stories'],
            ];
        @endphp
        <nav id="SiteNav" class="relative w-full flex items-center justify-center px-[75px]">
            <div
                class="fixed top-0 flex items-center justify-between w-full max-w-[1130px] rounded-3xl p-4 bg-white mt-[30px] z-30">
                <a href="{{ route('front.index') }}" class="flex shrink-0">
                    <img src="{{ asset('assets/images/logos/logo-black.svg') }}" alt="logo">
                </a>
                <ul class="flex items-center gap-[10px]">
                    @foreach ($navItems as $item)
                        @php($activePatterns = (array) $item['active'])
                        <li class="group {{ request()->routeIs(...$activePatterns) ? 'active' : '' }}">
                            <a href="{{ route($item['route']) }}"
                                class="hover:font-bold group-[.active]:font-bold transition-all duration-300 md:text-base xs:text-sm">{{ $item['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
                @guest
                    <div class="nav-auth-actions flex items-center">
                        <a href="{{ route('login') }}"
                            class="nav-auth-link group rounded-full border border-tedja-black hover:bg-tedja-black flex items-center transition-all duration-300">
                            <span class="font-semibold group-hover:text-white transition-all duration-300 md:text-base xs:text-sm">Sign In</span>
                        </a>
                        <a href="{{ route('register') }}"
                            class="nav-auth-link group rounded-full border flex items-center bg-tedja-green">
                            <span class="font-semibold md:text-base xs:text-sm">Sign Up</span>
                        </a>
                    </div>
                @endguest
                @auth
                    <button id="Profile" class="relative">
                        <div class="flex items-center gap-[14px]">
                            <div class="flex text-right flex-col gap-0.5">
                                <p class="text-sm text-tedja-secondary">Howdy,</p>
                                <p class="font-semibold">{{ Auth::user()->name }}</p>
                            </div>
                            <div class="flex rounded-full size-[50px] overflow-hidden">
                                <img src="{{ Auth::user()->photo ? Storage::url(Auth::user()->photo) : asset('assets/images/icons/default-avatar.svg') }}" class="w-full h-full object-cover"
                                    alt="photo">
                            </div>
                        </div>
                        <ul
                            class="hidden absolute top-full mt-[10px] right-0 flex flex-col w-[170px] shrink-0 h-fit text-left rounded-xl border border-tedja-border py-5 px-5 bg-white shadow-[0px_10px_30px_0px_#B8B8B840] gap-[14px]">
                            <li>
                                <a href="{{ route('dashboard.rewards') }}" class="hover:text-tedja-blue transition-all duration-300">Rewards</a>
                            </li>
                            <li>
                                <a href="{{ route('dashboard.mortgages.index') }}"
                                    class="hover:text-tedja-blue transition-all duration-300">My
                                    Mortgages</a>
                            </li>
                            <li>
                                <a href="{{ route('front.stories') }}" class="hover:text-tedja-blue transition-all duration-300">Learn
                                    Property</a>
                            </li>
                            <li>
                                <a href="{{ route('dashboard.settings') }}" class="hover:text-tedja-blue transition-all duration-300">Settings</a>
                            </li>
                            <li>
                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <a href="{{ route('logout') }}" class="hover:text-tedja-blue transition-all duration-300"
                                        onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </button>
                @endauth
            </div>
        </nav>
