<x-layout :boardList="$boardList ?? null" :board="$board ?? null" :friends="$friends ?? null">
    <div class="relative space-y-6">

        <!-- Un simple conteneur flex : la flèche est toujours dans le flux, bien alignée avec le titre -->
        <div class="flex items-center gap-4">
            
            <a href="{{ route('board.show', $board->id) }}" class="shrink-0 w-10 h-10 bg-gray-900 hover:bg-gray-600 border border-gray-700 rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition shadow-lg" title="Back to boards">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
        
            <div>
                <div class="ui-caption">Board settings</div>
                <h1 class="ui-h1"><span class="ui-muted">{{ $board->name }}</span></h1>
            </div>
        </div>

        @if (session('success'))
            <div class="ui-card-soft p-3 border border-sky-500/25 text-sky-200 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @can('delete', $board)
            <div class="ui-card p-5">
                <div class="ui-h2">Rename board</div>
                <div class="ui-caption mt-1">Update the board name (visible to all collaborators).</div>

                <form method="post" action="{{ route('board.rename', ['board' => $board->id]) }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                    @csrf
                    @method('PATCH')

                    <div class="ui-field flex-1">
                        <label class="ui-label" for="name">Board name</label>
                        <input class="ui-input" type="text" id="name" name="name" value="{{ old('name', $board->name) }}" autocomplete="off" />
                        <x-form-error fieldname="name" />
                    </div>

                    <button class="ui-btn ui-btn-neon sm:shrink-0" type="submit">
                        <i class="fa-solid fa-pen"></i>
                        <span>Rename</span>
                    </button>
                </form>
            </div>
        @endcan

        @can('delete', $board)
            <div class="ui-card p-5" x-data="{ tag: @js($board->category?->name ?? '') }">
                <div class="ui-h2">Board Tag</div>
                <div class="ui-caption mt-1">Assign or update a category tag for this board.</div>

                <form method="post" action="{{ route('board.update-tag', ['board' => $board->id]) }}" class="mt-4 space-y-3">
                    @csrf
                    @method('PATCH')

                    <div class="ui-field">
                        <label class="ui-label" for="tag">Tag / Category</label>
                        <input class="ui-input" type="text" id="tag" name="tag" x-model="tag" placeholder="e.g. Work, Personal..." autocomplete="off" />
                        <x-form-error fieldname="tag" />
                    </div>

                    <!-- Suggestions de tags existants -->
                    @if(isset($existingTags) && $existingTags->isNotEmpty())
                        <div class="flex flex-wrap items-center gap-1.5 pt-1">
                            <span class="text-xs text-gray-400 mr-1 font-medium">Suggestions :</span>
                            @foreach ($existingTags as $existingTag)
                                <button 
                                    type="button" 
                                    @click="tag = @js($existingTag)"
                                    class="text-xs px-2.5 py-1 rounded-lg bg-white/5 hover:bg-indigo-500/20 text-gray-300 hover:text-indigo-300 border border-white/10 transition-colors"
                                >
                                    {{ $existingTag }}
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <div class="pt-2">
                        <button class="ui-btn ui-btn-neon w-full sm:w-auto" type="submit">
                            <i class="fa-solid fa-tag"></i>
                            <span>Update tag</span>
                        </button>
                    </div>
                </form>
            </div>
        @endcan

        @can('addFriend', $board)
            <div class="ui-card p-6 space-y-4" x-data="{ copied: false }">
                <h2 class="text-lg font-semibold text-white">Invite Collaborators</h2>
                <p class="text-sm text-gray-400">Anyone with this link will be able to join this board as a collaborator.</p>
                
                <div class="flex gap-2">
                    <input type="text" readonly value="{{ route('board.join', $board->invite_token) }}" 
                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 text-sm text-gray-300" />
                    
                    <button type="button" 
                            @click="
                                navigator.clipboard.writeText('{{ route('board.join', $board->invite_token) }}');
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                            "
                            class="ui-btn ui-btn-neon shrink-0 flex items-center gap-2">
                        <i class="fa-solid" :class="copied ? 'fa-check text-green-400' : 'fa-copy'"></i>
                        <span x-text="copied ? 'Copied!' : 'Copy link'"></span>
                    </button>
                </div>
            </div>
        @endcan

        @can('delete', $board)
            <!-- On initialise un état Alpine pour gérer l'ouverture de la modale et la saisie de l'utilisateur -->
            <div class="ui-card p-5 border border-red-500/20" x-data="{ openModal: false, confirmName: '' }">
                <div class="ui-h2">Danger zone</div>
                <div class="ui-caption mt-1">Deleting a board is permanent.</div>

                <div class="mt-4">
                    <button @click="openModal = true" class="ui-btn ui-btn-danger" type="button">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Delete this board</span>
                    </button>
                </div>

                <!-- MODALE DE CONFIRMATION -->
                <div x-show="openModal" 
                    style="display: none;"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm"
                    @keydown.escape.window="openModal = false">
                    
                    <div @click.away="openModal = false" class="bg-[#121620] border border-gray-800 rounded-3xl p-6 max-w-md w-full mx-4 shadow-2xl space-y-4">
                        <div class="flex items-center gap-3 text-red-400">
                            <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                            <h3 class="text-lg font-bold text-white">Delete Board ?</h3>
                        </div>

                        <p class="text-xs text-gray-300 leading-relaxed">
                            This action is irreversible. Please type <span class="font-bold text-white bg-white/10 px-1.5 py-0.5 rounded select-all">{{ $board->name }}</span> to confirm.
                        </p>

                        <form method="post" action="/delete_board/{{ $board->id }}" class="space-y-4">
                            @csrf
                            @method('DELETE')

                            <div>
                                <input type="text" 
                                    x-model="confirmName" 
                                    placeholder="Type the board name here..." 
                                    class="ui-input w-full" 
                                    autocomplete="off" />
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button type="button" 
                                        @click="openModal = false; confirmName = ''" 
                                        class="ui-btn bg-gray-800 text-gray-300 hover:bg-gray-700">
                                    Cancel
                                </button>

                                <!-- Le bouton de suppression est désactivé (disabled) tant que le texte tapé ne correspond pas exactement au nom de la board -->
                                <button type="submit" 
                                        :disabled="confirmName !== @js($board->name)"
                                        :class="confirmName === @js($board->name) ? 'opacity-150 cursor-pointer' : 'opacity-40 cursor-not-allowed'"
                                        class="ui-btn ui-btn-danger transition-opacity">
                                    Yes, delete it
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    </div>
</x-layout>