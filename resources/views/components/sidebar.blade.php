@props(['board', 'boardList'])

<aside class="ui-sidebar sticky top-0 h-dvh p-4 flex flex-col justify-between w-72">
    
    <!-- SECTION HAUTE -->
    <div class="flex flex-col gap-y-4 overflow-hidden">
        <div class="px-2 flex items-center gap-3">
            <div class="ui-h2 whitespace-nowrap">PulsarBoard</div>
        </div>

        <div class="flex flex-col gap-y-2">
            <a href="/" 
               class="flex items-center px-4 py-2.5 justify-start gap-3 w-full rounded-xl text-gray-300 hover:text-white hover:bg-gray-800/60 transition {{ request()->is('/') ? 'bg-gray-800/60 text-white' : '' }}" 
               title="Home">
                <i class="fas fa-home w-5 text-center text-indigo-400 shrink-0"></i>
                <span class="font-medium text-sm whitespace-nowrap">Home</span>
            </a>
        </div>
        <div class="ui-divider"></div>

        <!-- SECTION CENTRALE CONDITIONNELLE -->
        @auth
            <div class="flex flex-col gap-y-4 overflow-y-auto overflow-x-hidden pr-1">
                <!-- Collaboration -->
                <div class="flex flex-col gap-y-2">
                    <p class="text-xs ui-muted px-2 whitespace-nowrap">Collaboration</p>
                    @if (isset($boardList))
                        @foreach ($boardList as $b)
                            @if (Auth::user()->id != $b->owner_id)
                                <a class="ui-link flex items-center px-4 py-2.5 justify-between w-full rounded-xl transition {{ (request()->is('board/'.$b->id) || request()->is('board/'.$b->id.'/*')) ? 'is-active' : '' }}" 
                                    href="/board/{{$b->id}}" 
                                    title="{{ $b->name }}">
                                     <span class="flex min-w-0 items-center gap-2">
                                         <i class="fa-regular fa-handshake ui-muted shrink-0 w-5 text-center"></i>
                                         <span class="truncate">{{ $b->name }}</span>
                                     </span>
                                     <span class="ui-badge ui-badge-collab shrink-0">
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
                    <p class="text-xs ui-muted px-2 whitespace-nowrap">My boards</p>
                    @if (isset($boardList))                    
                        @foreach ($boardList as $b)
                            @if (Auth::user()->id == $b->owner_id)
                                <a class="ui-link flex items-center px-4 py-2.5 justify-between w-full rounded-xl transition {{ (request()->is('board/'.$b->id) || request()->is('board/'.$b->id.'/*')) ? 'is-active' : '' }}" 
                                    href="/board/{{$b->id}}" 
                                    title="{{ $b->name }}">
                                     <span class="flex min-w-0 items-center gap-2">
                                         <i class="fa-solid fa-crown text-sky-300 shrink-0 w-5 text-center"></i>
                                         <span class="truncate">{{ $b->name }}</span>
                                     </span>
                                     <span class="ui-badge ui-badge-owner shrink-0">
                                         <i class="fa-solid fa-user-check"></i>
                                         <span>Owner</span>
                                     </span>
                                </a>
                            @endif
                        @endforeach
                    @endif
                </div>

                <div>
                    <a class="ui-btn ui-btn-neon flex items-center px-4 py-2.5 w-full justify-center gap-2 transition" 
                       href="/board_create" 
                       title="New board">
                        <i class="fa-solid fa-plus shrink-0"></i>
                        <span class="whitespace-nowrap">New board</span>
                    </a>
                </div>
            </div>
        @endauth
    </div>

    <!-- SECTION BASSE -->
    <div class="flex flex-col gap-y-2 overflow-hidden">
        <div class="ui-divider"></div>
        @guest
            <a class="ui-link flex items-center px-4 py-2.5 justify-start gap-3 w-full rounded-xl transition {{ request()->is('login') ? 'is-active' : '' }}" 
               href="/login" 
               title="Login">
                <i class="fa-solid fa-right-to-bracket ui-muted shrink-0 w-5 text-center"></i>
                <span>Login</span>
            </a>
            <a class="ui-link flex items-center px-4 py-2.5 justify-start gap-3 w-full rounded-xl transition {{ request()->is('register') ? 'is-active' : '' }}" 
               href="/register" 
               title="Register">
                <i class="fa-solid fa-user-plus ui-muted shrink-0 w-5 text-center"></i>
                <span>Register</span>
            </a>
        @endguest

        @auth
            <a class="ui-link flex items-center px-4 py-2.5 justify-start gap-3 w-full rounded-xl transition {{ request()->is('profile') ? 'is-active' : '' }}" 
               href="/profile" 
               title="My profile">
                <i class="fa-regular fa-user ui-muted shrink-0 w-5 text-center"></i>
                <span>My profile</span>
            </a>
            <a class="ui-link flex items-center px-4 py-2.5 justify-start gap-3 w-full rounded-xl transition" 
               href="/logout" 
               title="Logout">
                <i class="fa-solid fa-arrow-right-from-bracket ui-muted shrink-0 w-5 set-center"></i>
                <span>Logout</span>
            </a>
        @endauth
    </div>
</aside>