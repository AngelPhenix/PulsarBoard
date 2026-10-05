@php
    $showTags = Auth::user()->show_board_tags;
@endphp

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<x-layout :boardList='$boardList ?? null'>
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">My Boards</h1>
            <p class="text-sm text-gray-400 mt-1">Manage your projects and track your task progress.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <!-- Bouton pour basculer l'affichage des tags -->
            <form method="POST" action="{{ route('user.toggle-board-tags') }}" class="m-0 inline-block">
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
        <div class="space-y-12">
            <!-- 1. BOUCLE SUR LES CATÉGORIES -->
            @foreach ($categories as $category)
                <div class="category-block relative pt-3 px-4 pb-3 rounded-3xl border border-gray-800/50 bg-[#121620]/20">
                    
                    <!-- EN-TÊTE INTÉGRÉ SUR LA BORDURE SUPÉRIEURE -->
                    <div class="absolute -top-3.5 left-6 right-6 flex items-center gap-4 select-none">
                        <!-- Mode Affichage (le h2) -->
                        <h2 class="category-name-display h-[25px] text-xs font-extrabold text-gray-400 tracking-wider uppercase bg-[#0b0d12] px-3 rounded-md border border-gray-800 flex items-center gap-2 cursor-pointer hover:border-indigo-500/50 transition select-none">
                            <span class="text-indigo-400">#</span> 
                            <span class="cat-text">{{ $category->name }}</span>
                        </h2>

                        <!-- Mode Édition (l'input masqué par défaut) -->
                        <input 
                            type="text" 
                            value="{{ $category->name }}" 
                            data-category-id="{{ $category->id }}"
                            data-update-url="{{ route('board.category.update', $category->id) }}"
                            class="category-name-input hidden h-[25px] text-xs font-extrabold text-white tracking-wider uppercase bg-[#0b0d12] px-3 rounded-md border border-indigo-500 focus:outline-none w-full"
                        >

                        <!-- Ligne horizontale de séparation au milieu -->
                        <div class="h-[1px] bg-gray-000 flex-grow"></div>

                        <!-- Croix de suppression avec zone cliquable sur tout le bouton -->
                        <form action="{{ route('board.category.destroy', $category->id) }}" method="POST" class="inline delete-category-form bg-[#0b0d12] h-[25px] rounded-md border border-gray-800 flex items-center justify-center m-0 overflow-hidden">
                            @csrf
                            @method('DELETE')
                            <button 
                                type="button" 
                                class="category-delete-btn text-gray-500 hover:text-red-400 hover:bg-gray-800/50 w-full h-full px-2.5 transition flex items-center justify-center cursor-pointer"
                                data-empty="{{ $category->boards->isEmpty() ? 'true' : 'false' }}"
                                title="Delete category"
                            >
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </form>
                    </div>

                    <!-- ZONE DE DROP POUR LES BOARDS DE CETTE CATÉGORIE -->
                    <div class="board-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 min-h-[120px] mt-2" data-tag="{{ $category->name }}">
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
                            <div class="empty-state col-span-full text-center py-6 text-xs text-gray-500 italic border border-dashed border-gray-800 rounded-2xl flex items-center justify-center">
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

        // 1. Activer le mode édition au clic sur le nom de la catégorie
        document.querySelectorAll('.category-name-display').forEach(displayEl => {
            displayEl.addEventListener('click', function() {
                // On cherche le conteneur parent global (la div ou le header)
                const wrapper = this.closest('.category-wrapper') || this.parentElement;
                if (!wrapper) return;

                const displayH2 = wrapper.querySelector('.category-name-display');
                const inputField = wrapper.querySelector('.category-name-input');
                
                if (displayH2 && inputField) {
                    displayH2.classList.add('hidden');
                    inputField.classList.remove('hidden');
                    inputField.focus();
                    inputField.select(); // Sélectionne tout le texte pour aller plus vite
                }
            });
        });

        // 2. Gérer la sauvegarde (touche Entrée ou perte de focus)
        document.querySelectorAll('.category-name-input').forEach(inputEl => {
            inputEl.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    saveCategoryName(this);
                } else if (e.key === 'Escape') {
                    cancelCategoryEdit(this);
                }
            });

            inputEl.addEventListener('blur', function() {
                saveCategoryName(this);
            });
        });

        const containers = document.querySelectorAll('.board-container');

        containers.forEach(container => {
            new Sortable(container, {
                group: 'boards-group',
                animation: 150,
                handle: '.board-handle',
                ghostClass: 'opacity-40',

                onAdd: function (evt) {
                    const boardCard = evt.item.closest('[data-board-id]') || evt.item;
                    const boardId = boardCard.dataset.boardId; 
                    const targetContainer = evt.to; 
                    const newTag = targetContainer.dataset.tag; 

                    if (!boardId) {
                        console.error("Erreur : boardId est introuvable sur l'élément !", evt.item);
                        return;
                    }

                    updateBoardTag(boardId, newTag, boardCard, targetContainer);
                }
            });
        });

        // --- GESTION DU CLIC SUR LA CROIX DE SUPPRESSION ---
        document.addEventListener('click', function (event) {
            const deleteBtn = event.target.closest('.category-delete-btn');
            if (!deleteBtn) return;

            const categoryBlock = deleteBtn.closest('.category-block') || deleteBtn.closest('section') || deleteBtn.parentElement.parentElement;
            if (!categoryBlock) return;

            const container = categoryBlock.querySelector('.board-container');
            if (!container) return;

            const boardsCount = container.querySelectorAll('[data-board-id]').length;
            const isEmpty = boardsCount === 0;

            if (!isEmpty) {
                alert("This category has to be empty to be deleted");
            } else {
                deleteBtn.closest('form').submit();
            }
        });

        function updateBoardTag(boardId, tag, boardCardElement, targetContainer) {
            if (!boardId) return;

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.content : '';

            fetch(`/board/${boardId}/tag`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
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
                    const targetEmptyState = targetContainer.querySelector('.empty-state');
                    if (targetEmptyState) {
                        targetEmptyState.remove();
                    }

                    containers.forEach(container => {
                        const cards = container.querySelectorAll('[data-board-id]');
                        const hasEmptyState = container.querySelector('.empty-state');
                        
                        const categoryBlock = container.closest('.category-block');
                        const deleteBtn = categoryBlock ? categoryBlock.querySelector('.category-delete-btn') : null;

                        if (cards.length === 0) {
                            container.dataset.empty = 'true';
                            if (deleteBtn) deleteBtn.dataset.empty = 'true';

                            if (!hasEmptyState) {
                                const emptyDiv = document.createElement('div');
                                emptyDiv.className = 'empty-state col-span-full text-center py-6 text-xs text-gray-500 italic border border-dashed border-gray-800 rounded-2xl flex items-center justify-center';
                                emptyDiv.textContent = 'Drop boards here or assign them from settings';
                                container.appendChild(emptyDiv);
                            }
                        } else {
                            container.dataset.empty = 'false';
                            if (deleteBtn) deleteBtn.dataset.empty = 'false';

                            if (hasEmptyState) {
                                hasEmptyState.remove();
                            }
                        }
                    });

                    const tagBadgeContainer = boardCardElement.querySelector('.board-tag-badge');
                    const badgeContainer = boardCardElement.querySelector('.flex.items-center.gap-2');

                    if (tag !== "") {
                        if (tagBadgeContainer) {
                            tagBadgeContainer.textContent = tag;
                        } else {
                            const newBadge = document.createElement('span');
                            newBadge.className = 'board-tag-badge text-xs px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-300 font-medium border border-indigo-500/20';
                            newBadge.textContent = tag;
                            if (badgeContainer) {
                                badgeContainer.prepend(newBadge);
                            }
                        }
                    } else {
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

    function saveCategoryName(inputEl) {
        const wrapper = inputEl.closest('.category-wrapper') || inputEl.parentElement;
        if (!wrapper) return;

        const displayH2 = wrapper.querySelector('.category-name-display');
        const spanText = displayH2 ? displayH2.querySelector('.cat-text') : null;
        const newName = inputEl.value.trim();
        const updateUrl = inputEl.dataset.updateUrl;

        if (!newName || (spanText && newName === spanText.textContent)) {
            cancelCategoryEdit(inputEl);
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(updateUrl, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: newName })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || data.name) {
                if (spanText) {
                    spanText.textContent = newName;
                }
                
                const categoryBlock = wrapper.closest('.category-block');
                if (categoryBlock) {
                    categoryBlock.querySelectorAll('.board-tag-badge').forEach(badge => {
                        badge.textContent = newName;
                    });
                }
            }
            cancelCategoryEdit(inputEl);
        })
        .catch(error => {
            console.error('Erreur lors de la mise à jour:', error);
            cancelCategoryEdit(inputEl);
        });
    }

    function cancelCategoryEdit(inputEl) {
        const wrapper = inputEl.closest('.category-wrapper') || inputEl.parentElement;
        if (!wrapper) return;

        const displayH2 = wrapper.querySelector('.category-name-display');
        const spanText = displayH2 ? displayH2.querySelector('.cat-text') : null;
        
        if (spanText) {
            inputEl.value = spanText.textContent;
        }
        inputEl.classList.add('hidden');
        if (displayH2) {
            displayH2.classList.remove('hidden');
        }
    }
</script>