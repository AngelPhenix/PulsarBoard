@props(['board', 'boardList'])

<aside class="ui-sidebar sticky top-0 w-72 h-dvh p-4 flex flex-col justify-between">
    <!-- SECTION HAUTE : Titre "PulsarBoard" + Lien "Home" communs à tous -->
    <div class="flex flex-col gap-y-4">
        <div class="px-2">
            <div class="ui-h2">PulsarBoard</div>
        </div>
        <div class="ui-divider"></div>

        <div class="flex flex-col gap-y-2">
            <a href="/" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-300 hover:text-white hover:bg-gray-800/60 transition {{ request()->is('/') ? 'bg-gray-800/60 text-white' : '' }}">
                <i class="fas fa-home w-5 text-center text-indigo-400"></i>
                <span class="font-medium text-sm">Home</span>
            </a>
        </div>
        <div class="ui-divider"></div>

        <!-- SECTION CENTRALE CONDITIONNELLE -->
        @auth
            <!-- Si connecté : Liste des boards -->
            <div class="flex flex-col gap-y-4">
                <div class="flex flex-col gap-y-2">
                    <p class="text-xs ui-muted px-2">Collaboration</p>
                    @if (isset($boardList))
                        @foreach ($boardList as $b)
                            @if (Auth::user()->id != $b->owner_id)
                            <!-- Ajout du wildcard /* pour cibler aussi les settings et autres sous-pages -->
                            <a class="ui-link {{ (request()->is('board/'.$b->id) || request()->is('board/'.$b->id.'/*')) ? 'is-active' : '' }} justify-between" href="/board/{{$b->id}}">
                                <span class="flex min-w-0 items-center gap-2">
                                    <i class="fa-regular fa-handshake ui-muted"></i>
                                    <span class="truncate">{{ $b->name }}</span>
                                </span>
                                <span class="ui-badge ui-badge-collab">
                                    <i class="fa-solid fa-users"></i>
                                    <span>Collab</span>
                                </span>
                            </a>
                            @endif
                        @endforeach
                    @endif
                </div>

                <div class="flex flex-col gap-y-2">
                    <p class="text-xs ui-muted px-2">My boards</p>
                    @if (isset($boardList))                    
                        @foreach ($boardList as $b)
                            @if (Auth::user()->id == $b->owner_id)
                            <!-- Idem ici pour les boards dont on est owner -->
                            <a class="ui-link {{ (request()->is('board/'.$b->id) || request()->is('board/'.$b->id.'/*')) ? 'is-active' : '' }} justify-between" href="/board/{{$b->id}}">
                                <span class="flex min-w-0 items-center gap-2">
                                    <i class="fa-solid fa-crown text-sky-300"></i>
                                    <span class="truncate">{{ $b->name }}</span>
                                </span>
                                <span class="ui-badge ui-badge-owner">
                                    <i class="fa-solid fa-user-check"></i>
                                    <span>Owner</span>
                                </span>
                            </a>
                            @endif
                        @endforeach
                    @endif
                </div>

                <div class="px-2 pt-1">
                    <a class="ui-btn ui-btn-neon w-full" href="/board_create">
                        <i class="fa-solid fa-plus"></i>
                        <span>New board</span>
                    </a>
                </div>
            </div>
        @endauth
    </div>

    <!-- SECTION BASSE -->
    <div class="flex flex-col gap-y-2">
        <div class="ui-divider"></div>
        @guest
            <a class="ui-link {{ request()->is('login') ? 'is-active' : '' }}" href="/login">
                <i class="fa-solid fa-right-to-bracket ui-muted"></i>
                <span>Login</span>
            </a>
            <a class="ui-link {{ request()->is('register') ? 'is-active' : '' }}" href="/register">
                <i class="fa-solid fa-user-plus ui-muted"></i>
                <span>Register</span>
            </a>
        @endguest

        @auth
            <a class="ui-link {{ request()->is('profile') ? 'is-active' : '' }}" href="/profile">
                <i class="fa-regular fa-user ui-muted"></i>
                <span>My profile</span>
            </a>
            <a class="ui-link" href="/logout">
                <i class="fa-solid fa-arrow-right-from-bracket ui-muted"></i>
                <span>Logout</span>
            </a>
        @endauth
    </div>
</aside>