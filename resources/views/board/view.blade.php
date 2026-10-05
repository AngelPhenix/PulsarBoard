@php
    $showTags = Auth::user()->show_board_tags;
@endphp

<x-layout :boardList='$boardList ?? null'>
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">My Boards</h1>
            <p class="text-sm text-gray-400 mt-1">Manage your projects and track your task progress.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <!-- Bouton pour basculer l'affichage des tags -->
            <form method="POST" action="{{ route('user.toggle-board-tags') }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm font-medium px-4 py-2.5 rounded-xl border border-gray-700 transition flex items-center gap-2" title="Toggle view mode">
                    <i class="fas {{ $showTags ? 'fa-list' : 'fa-layer-group' }}"></i>
                    <span>{{ $showTags ? 'Switch to flat view' : 'Group by tags' }}</span>
                </button>
            </form>

            <!-- New Board Button -->
            <a href="/board_create" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-indigo-600/20 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> New Board
            </a>
        </div>
    </div>

    @if($showTags)
        <!-- Bouton et formulaire pour ajouter une catégorie -->
        <div x-data="{ openInput: false, categoryName: '' }" class="my-8">
            
            <!-- 1. MODE LIGNE / BOUTON -->
            <div x-show="!openInput" @click="openInput = true; $nextTick(() => $refs.categoryInput.focus())" 
                class="flex items-center gap-4 cursor-pointer group py-4">
                <div class="h-[1px] bg-gray-800 flex-grow group-hover:bg-indigo-500/50 transition-colors"></div>
                <span class="text-xs font-semibold tracking-wider text-gray-500 group-hover:text-indigo-400 uppercase transition-colors flex items-center gap-2 select-none">
                    <i class="fas fa-plus text-[10px]"></i> add new category
                </span>
                <div class="h-[1px] bg-gray-800 flex-grow group-hover:bg-indigo-500/50 transition-colors"></div>
            </div>

            <!-- 2. MODE FORMULAIRE DE SAISIE -->
            <div x-show="openInput" style="display: none;" class="max-w-md mx-auto">
                <form action="{{ route('board.category.store') }}" method="POST" class="bg-[#121620] border border-indigo-500/40 rounded-3xl p-5 shadow-2xl">
                    @csrf
                    <label class="block text-xs font-semibold text-gray-400 mb-2 uppercase tracking-wide">
                        New Category Name
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="text" name="name" x-ref="categoryInput" x-model="categoryName"
                            @keydown.escape="openInput = false"
                            placeholder="e.g. In Progress, Backlog..." 
                            class="w-full bg-[#0b0d12] border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500">
                        
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2.5 rounded-xl text-xs font-semibold transition shrink-0">
                            Add
                        </button>
                        <button type="button" @click="openInput = false" class="text-gray-500 hover:text-gray-300 px-3 py-2 text-xs transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <div class="space-y-10">
            <!-- 1. BOUCLE SUR LES CATÉGORIES (Vides ou pleines) -->
            @foreach ($categories as $category)
                <div class="bg-[#121620]/30 p-6 rounded-3xl border border-gray-800/50">
                    <!-- En-tête de la catégorie -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <h2 class="text-xl font-extrabold text-white tracking-wide uppercase">
                                {{ $category->name }}
                            </h2>
                            <span class="text-xs px-3 py-1 rounded-full bg-gray-800 text-gray-300 font-semibold border border-gray-700/50">
                                {{ $category->boards->count() }}
                            </span>
                        </div>

                        <!-- Croix de suppression (affichée uniquement si la catégorie est vide) -->
                        @if ($category->boards->isEmpty())
                            <form action="{{ route('board.category.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Supprimer cette catégorie vide ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-500 hover:text-red-400 p-2 transition-colors rounded-xl hover:bg-red-500/10" title="Delete category">
                                    <i class="fas fa-xmark text-sm"></i>
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- ZONE DE DROP POUR LES BOARDS DE CETTE CATÉGORIE -->
                    <div class="board-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 min-h-[120px]" data-tag="{{ $category->name }}">
                        @forelse ($category->boards as $board)
                            <!-- LA CARTE DE BOARD -->
                            <div class="bg-[#121620] rounded-2xl shadow-lg border border-gray-800/80 flex flex-col justify-between overflow-hidden group hover:border-indigo-500/50 transition-all duration-200 cursor-pointer" data-board-id="{{ $board->id }}" onclick="window.location.href='/board/{{ $board->id }}'">
                                
                                <div class="p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="board-handle w-10 h-10 rounded-xl bg-gray-800/50 text-gray-400 hover:text-white flex items-center justify-center cursor-grab active:cursor-grabbing transition" title="Drag to reorder" onclick="event.stopPropagation()">
                                            <i class="fas fa-grip-vertical"></i>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <span class="board-tag-badge text-xs px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-300 font-medium border border-indigo-500/20">
                                                {{ $category->name }}
                                            </span>
                                            <a href="{{ route('settings', $board->id) }}" class="text-gray-500 hover:text-gray-300 p-1 transition" title="Board settings" onclick="event.stopPropagation()">
                                                <i class="fas fa-cog"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <h2 class="text-xl font-bold text-white group-hover:text-indigo-400 transition truncate mb-1">
                                        {{ $board->name }}
                                    </h2>
                                    <p class="text-xs text-gray-400">
                                        Owner: <span class="font-medium text-gray-300">{{ $board->owner->username ?? 'Me' }}</span>
                                    </p>
                                </div>

                                <div class="bg-[#0b0d12]/50 px-6 py-4 border-t border-gray-800/80 flex justify-between items-center">
                                    <span class="text-xs font-medium text-gray-400">
                                        {{ $board->tasks()->count() ?? 0 }} tasks
                                    </span>
                                    <span class="text-indigo-400 group-hover:text-indigo-300 font-semibold text-sm flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                        Open <i class="fas fa-arrow-right text-xs"></i>
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-6 text-xs text-gray-500 italic border border-dashed border-gray-800 rounded-2xl flex items-center justify-center">
                                Drop boards here or assign them from settings
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach

            <!-- 2. BLOC POUR LES BOARDS SANS CATÉGORIE (Uncategorized) -->
            @if(isset($uncategorizedBoards) && $uncategorizedBoards->isNotEmpty())
                <div class="bg-[#121620]/30 p-6 rounded-3xl border border-gray-800/50">
                    <div class="flex items-center gap-3 mb-6">
                        <h2 class="text-xl font-extrabold text-white tracking-wide uppercase">
                            Uncategorized
                        </h2>
                        <span class="text-xs px-3 py-1 rounded-full bg-gray-800 text-gray-300 font-semibold border border-gray-700/50">
                            {{ $uncategorizedBoards->count() }}
                        </span>
                    </div>

                    <div class="board-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 min-h-[120px]" data-tag="">
                        @foreach ($uncategorizedBoards as $board)
                            <!-- Rendu de la carte similaire (ou inclus via un composant partiel) -->
                            <div class="bg-[#121620] rounded-2xl shadow-lg border border-gray-800/80 flex flex-col justify-between overflow-hidden group hover:border-indigo-500/50 transition-all duration-200 cursor-pointer" data-board-id="{{ $board->id }}" onclick="window.location.href='/board/{{ $board->id }}'">
                                <div class="p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="board-handle w-10 h-10 rounded-xl bg-gray-800/50 text-gray-400 hover:text-white flex items-center justify-center cursor-grab active:cursor-grabbing transition" title="Drag to reorder" onclick="event.stopPropagation()">
                                            <i class="fas fa-grip-vertical"></i>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('settings', $board->id) }}" class="text-gray-500 hover:text-gray-300 p-1 transition" title="Board settings" onclick="event.stopPropagation()">
                                                <i class="fas fa-cog"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <h2 class="text-xl font-bold text-white group-hover:text-indigo-400 transition truncate mb-1">
                                        {{ $board->name }}
                                    </h2>
                                    <p class="text-xs text-gray-400">
                                        Owner: <span class="font-medium text-gray-300">{{ $board->owner->username ?? 'Me' }}</span>
                                    </p>
                                </div>
                                <div class="bg-[#0b0d12]/50 px-6 py-4 border-t border-gray-800/80 flex justify-between items-center">
                                    <span class="text-xs font-medium text-gray-400">
                                        {{ $board->tasks()->count() ?? 0 }} tasks
                                    </span>
                                    <span class="text-indigo-400 group-hover:text-indigo-300 font-semibold text-sm flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                        Open <i class="fas fa-arrow-right text-xs"></i>
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Si l'utilisateur n'a absolument rien (ni catégorie, ni board) -->
            @if($categories->isEmpty() && (empty($uncategorizedBoards) || $uncategorizedBoards->isEmpty()))
                <div class="col-span-3 text-center py-16 bg-[#121620] rounded-2xl border-2 border-dashed border-gray-800">
                    <div class="w-12 h-12 rounded-full bg-indigo-500/10 text-indigo-400 flex items-center justify-center mx-auto mb-3 text-xl">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <p class="text-gray-200 font-medium mb-1">No boards found</p>
                    <p class="text-sm text-gray-400 mb-4">Create your first board to start organizing your tasks.</p>
                    <a href="/board_create" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2 rounded-xl shadow transition">
                        Create a board
                    </a>
                </div>
            @endif
        </div>
    @else
        {{-- VUE PLATE CLASSIQUE (Sans les catégories) --}}
        @if($boardList->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($boardList as $board)
                    <div class="bg-[#121620] rounded-2xl shadow-lg border border-gray-800/80 flex flex-col justify-between overflow-hidden group hover:border-indigo-500/50 transition-all duration-200">
                        
                        <!-- Card Body -->
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold">
                                    <i class="fas fa-columns"></i>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if(!empty($board->tag))
                                        <span class="text-xs px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-300 font-medium border border-indigo-500/20">
                                            {{ $board->tag }}
                                        </span>
                                    @endif
                                    <a href="{{ route('settings', $board->id) }}" class="text-gray-500 hover:text-gray-300 p-1 transition" title="Board settings">
                                        <i class="fas fa-cog"></i>
                                    </a>
                                </div>
                            </div>

                            <h2 class="text-xl font-bold text-white group-hover:text-indigo-400 transition truncate mb-1">
                                {{ $board->name }}
                            </h2>
                            <p class="text-xs text-gray-400">
                                Owner: <span class="font-medium text-gray-300">{{ $board->owner->username ?? 'Me' }}</span>
                            </p>
                        </div>

                        <!-- Card Footer -->
                        <div class="bg-[#0b0d12]/50 px-6 py-4 border-t border-gray-800/80 flex justify-between items-center">
                            <span class="text-xs font-medium text-gray-400">
                                {{ $board->tasks()->count() ?? 0 }} tasks
                            </span>
                            <a href="/board/{{ $board->id }}" class="text-indigo-400 hover:text-indigo-300 font-semibold text-sm flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                Open <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="col-span-3 text-center py-16 bg-[#121620] rounded-2xl border-2 border-dashed border-gray-800">
                <div class="w-12 h-12 rounded-full bg-indigo-500/10 text-indigo-400 flex items-center justify-center mx-auto mb-3 text-xl">
                    <i class="fas fa-folder-open"></i>
                </div>
                <p class="text-gray-200 font-medium mb-1">No boards found</p>
                <p class="text-sm text-gray-400 mb-4">Create your first board to start organizing your tasks.</p>
                <a href="/board_create" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2 rounded-xl shadow transition">
                    Create a board
                </a>
            </div>
        @endif
    @endif
</x-layout>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sélectionne tous les conteneurs de cartes
        const containers = document.querySelectorAll('.board-container');

        containers.forEach(container => {
            new Sortable(container, {
                group: 'boards-group', // Permet de faire voyager les cartes entre les différents blocs
                animation: 150,
                handle: '.board-handle', // Indique qu'on attrape la carte uniquement par la poignée
                ghostClass: 'opacity-40', // Effet visuel transparent sur la carte pendant qu'on la déplace

                // Déclenché quand une carte est déposée dans CE conteneur
                onAdd: function (evt) {
                    const boardId = evt.item.dataset.boardId; 
                    const targetContainer = evt.to; 
                    const newTag = targetContainer.dataset.tag; 

                    console.log("Board ID récupéré :", boardId); // <-- Ajoute ceci pour déboguer
                    console.log("Nouveau tag :", newTag);

                    if (!boardId) {
                        console.error("Erreur : boardId est introuvable sur l'élément !");
                        return;
                    }

                    updateBoardTag(boardId, newTag, evt.item);
                }
            });
        });

        // Fonction pour envoyer la modification à Laravel en AJAX
        function updateBoardTag(boardId, tag, boardCardElement) {
            if (!boardId) return;

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.content : '';

            fetch(`/board/${boardId}/tag`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json' // Force Laravel à répondre en JSON
                },
                body: JSON.stringify({ tag: tag === "" ? null : tag })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau ou serveur');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log('Tag mis à jour avec succès !');
                    
                    const tagBadgeContainer = boardCardElement.querySelector('.board-tag-badge');
                    const badgeContainer = boardCardElement.querySelector('.flex.items-center.gap-2'); // Conteneur du badge et de l'icône settings

                    if (tag !== "") {
                        if (tagBadgeContainer) {
                            // S'il y avait déjà un badge, on change juste son texte
                            tagBadgeContainer.textContent = tag;
                        } else {
                            // S'il n'y avait pas de badge (ex: venait d'Uncategorized), on le crée
                            const newBadge = document.createElement('span');
                            newBadge.className = 'board-tag-badge text-xs px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-300 font-medium border border-indigo-500/20';
                            newBadge.textContent = tag;
                            if (badgeContainer) {
                                badgeContainer.prepend(newBadge);
                            }
                        }
                    } else {
                        // Si on l'a déplacé dans "Uncategorized" (tag vide), on supprime le badge s'il existe
                        if (tagBadgeContainer) {
                            tagBadgeContainer.remove();
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Erreur lors du déplacement de la board :', error);
            });
        }
    });
</script>