@props(['board', 'boardList'])

<aside x-data="{ 
        open: {{ Auth::check() && Auth::user()->sidebar_collapsed ? 'false' : 'true' }},
        toggleSidebar() {
            this.open = !this.open;
            @auth
            fetch('{{ route('user.sidebar') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ collapsed: !this.open })
            });
            @endauth
        }
    }" 
    :class="open ? 'w-72' : 'w-20'" 
    class="ui-sidebar sticky top-0 h-dvh p-4 flex flex-col justify-between transition-all duration-300 ease-in-out relative {{ Auth::check() && Auth::user()->sidebar_collapsed ? 'w-20' : 'w-72' }}">
    
    <!-- Bouton pour toggle -->
    <button @click="toggleSidebar()" 
            class="absolute -right-3 top-6 bg-gray-800 border border-gray-700 text-gray-300 hover:text-white rounded-full w-6 h-6 flex items-center justify-center shadow-md z-10 transition-transform">
        <i class="fa-solid text-xs" :class="open ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
    </button>

    <!-- SECTION HAUTE -->
    <div class="flex flex-col gap-y-4 overflow-hidden">
        <div class="px-2 flex items-center gap-3">
            <div class="ui-h2 whitespace-nowrap transition-opacity duration-200" x-show="open" x-transition>PulsarBoard</div>
        </div>

        <div class="flex flex-col gap-y-2">
            <a href="/" 
               :class="open ? 'px-4 py-2.5 justify-start gap-3 w-full' : 'w-10 h-10 justify-center mx-auto p-0'" 
               class="flex items-center rounded-xl text-gray-300 hover:text-white hover:bg-gray-800/60 transition {{ request()->is('/') ? 'bg-gray-800/60 text-white' : '' }}" 
               title="Home">
                <i class="fas fa-home w-5 text-center text-indigo-400 shrink-0"></i>
                <span class="font-medium text-sm whitespace-nowrap" x-show="open" x-transition>Home</span>
            </a>
        </div>
        <div class="ui-divider"></div>

        <!-- SECTION CENTRALE CONDITIONNELLE -->
        @auth
            <div class="flex flex-col gap-y-4 overflow-y-auto overflow-x-hidden pr-1">
                <!-- Collaboration -->
                <div class="flex flex-col gap-y-2">
                    <p class="text-xs ui-muted px-2 whitespace-nowrap" x-show="open" x-transition>Collaboration</p>
                    @if (isset($boardList))
                        @foreach ($boardList as $b)
                            @if (Auth::user()->id != $b->owner_id)
                                <a :class="[
                                       {{ (request()->is('board/'.$b->id) || request()->is('board/'.$b->id.'/*')) ? 'true' : 'false' }} ? 'is-active' : '',
                                       open ? 'px-4 py-2.5 justify-between w-full' : 'w-10 h-10 justify-center mx-auto p-0'
                                   ]" 
                                   class="ui-link flex items-center rounded-xl transition" 
                                   href="/board/{{$b->id}}" 
                                   title="{{ $b->name }}">
                                    <span class="flex min-w-0 items-center gap-2">
                                        <i class="fa-regular fa-handshake ui-muted shrink-0 w-5 text-center"></i>
                                        <span class="truncate" x-show="open" x-transition>{{ $b->name }}</span>
                                    </span>
                                    <span class="ui-badge ui-badge-collab shrink-0" x-show="open" x-transition>
                                        <i class="fa-solid fa-users"></i>
                                        <span>Collab</span>
                                    </span>
                                </a>
                            @endif
                        @endforeach
                    @endif
                </div>

                <!-- My boards -->
                <div class="flex flex-col gap-y-2">
                    <p class="text-xs ui-muted px-2 whitespace-nowrap" x-show="open" x-transition>My boards</p>
                    @if (isset($boardList))                    
                        @foreach ($boardList as $b)
                            @if (Auth::user()->id == $b->owner_id)
                                <a :class="[
                                       {{ (request()->is('board/'.$b->id) || request()->is('board/'.$b->id.'/*')) ? 'true' : 'false' }} ? 'is-active' : '',
                                       open ? 'px-4 py-2.5 justify-between w-full' : 'w-10 h-10 justify-center mx-auto p-0'
                                   ]" 
                                   class="ui-link flex items-center rounded-xl transition" 
                                   href="/board/{{$b->id}}" 
                                   title="{{ $b->name }}">
                                    <span class="flex min-w-0 items-center gap-2">
                                        <i class="fa-solid fa-crown text-sky-300 shrink-0 w-5 text-center"></i>
                                        <span class="truncate" x-show="open" x-transition>{{ $b->name }}</span>
                                    </span>
                                    <span class="ui-badge ui-badge-owner shrink-0" x-show="open" x-transition>
                                        <i class="fa-solid fa-user-check"></i>
                                        <span>Owner</span>
                                    </span>
                                </a>
                            @endif
                        @endforeach
                    @endif
                </div>

                <div class="pt-1">
                    <a :class="open ? 'px-4 py-2.5 w-full justify-center gap-2' : '!w-10 !h-10 !p-0 justify-center mx-auto'" 
                    class="ui-btn ui-btn-neon flex items-center transition" 
                    href="/board_create" 
                    title="New board">
                        <i class="fa-solid fa-plus shrink-0"></i>
                        <span class="whitespace-nowrap" x-show="open" x-transition>New board</span>
                    </a>
                </div>
            </div>
        @endauth
    </div>

    <!-- SECTION BASSE -->
    <div class="flex flex-col gap-y-2 overflow-hidden">
        <div class="ui-divider"></div>
        @guest
            <a :class="open ? 'px-4 py-2.5 justify-start gap-3 w-full' : 'w-10 h-10 justify-center mx-auto p-0'" 
               class="ui-link flex items-center rounded-xl transition {{ request()->is('login') ? 'is-active' : '' }}" 
               href="/login" 
               title="Login">
                <i class="fa-solid fa-right-to-bracket ui-muted shrink-0 w-5 text-center"></i>
                <span x-show="open" x-transition>Login</span>
            </a>
            <a :class="open ? 'px-4 py-2.5 justify-start gap-3 w-full' : 'w-10 h-10 justify-center mx-auto p-0'" 
               class="ui-link flex items-center rounded-xl transition {{ request()->is('register') ? 'is-active' : '' }}" 
               href="/register" 
               title="Register">
                <i class="fa-solid fa-user-plus ui-muted shrink-0 w-5 text-center"></i>
                <span x-show="open" x-transition>Register</span>
            </a>
        @endguest

        @auth
            <a :class="open ? 'px-4 py-2.5 justify-start gap-3 w-full' : 'w-10 h-10 justify-center mx-auto p-0'" 
               class="ui-link flex items-center rounded-xl transition {{ request()->is('profile') ? 'is-active' : '' }}" 
               href="/profile" 
               title="My profile">
                <i class="fa-regular fa-user ui-muted shrink-0 w-5 text-center"></i>
                <span x-show="open" x-transition>My profile</span>
            </a>
            <a :class="open ? 'px-4 py-2.5 justify-start gap-3 w-full' : 'w-10 h-10 justify-center mx-auto p-0'" 
               class="ui-link flex items-center rounded-xl transition" 
               href="/logout" 
               title="Logout">
                <i class="fa-solid fa-arrow-right-from-bracket ui-muted shrink-0 w-5 set-center"></i>
                <span x-show="open" x-transition>Logout</span>
            </a>
        @endauth
    </div>
</aside>