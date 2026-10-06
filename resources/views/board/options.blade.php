<x-layout :boardList='$boardList ?? null'>
    <div class="relative space-y-6">

        <!-- Flèche de retour positionnée en absolute (hors du flux, ne décale rien) -->
        <a href="{{ route('board.show', $board->id) }}" class="absolute -left-14 top-0 w-10 h-10 bg-gray-900 hover:bg-gray-600 border border-gray-700 rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition shadow-lg" title="Back to boards">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
    
        <div>
            <div class="ui-caption">Board settings</div>
            <h1 class="ui-h1"><span class="ui-muted">{{ $board->name }}</span></h1>
        </div>

        @if (session('board_renamed'))
            <div class="ui-card-soft p-3 border border-sky-500/25 text-sky-200 text-sm">
                {{ session('board_renamed') }}
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
            <div class="ui-card p-5">
                <div class="ui-h2">Collaborators</div>
                <div class="ui-caption mt-1">Invite one of your friends to this board.</div>

                <form method="post" action="/board_addfriend/{{$board->id}}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                    @csrf
                    <div class="ui-field flex-1">
                        <label class="ui-label" for="mail">Friend</label>
                        @php
                            $firstFriend = $friends->first();
                        @endphp

                        <div
                            class="relative"
                            x-data="{
                                open: false,
                                selectedLabel: @js($firstFriend?->username ?? 'No friends available'),
                                selectedValue: @js($firstFriend?->email ?? ''),
                                select(label, value) { this.selectedLabel = label; this.selectedValue = value; this.open = false; },
                            }"
                        >
                            <input type="hidden" name="mail" :value="selectedValue">

                            <button
                                type="button"
                                class="ui-select flex items-center justify-between gap-3 text-left"
                                :class="selectedValue ? '' : 'opacity-60 cursor-not-allowed'"
                                @click="if (selectedValue) open = !open"
                            >
                                <span class="truncate" x-text="selectedLabel"></span>
                                <i class="fa-solid fa-chevron-down ui-muted text-xs"></i>
                            </button>

                            <div
                                x-show="open"
                                x-transition
                                @click.outside="open = false"
                                class="absolute z-50 mt-2 w-full ui-card-soft p-1 backdrop-blur"
                            >
                                <div class="max-h-64 overflow-auto">
                                    @foreach ($friends as $friend)
                                        <button
                                            type="button"
                                            class="w-full rounded-xl px-3 py-2 text-left transition-colors hover:bg-white/5"
                                            :class="selectedValue === @js($friend->email) ? 'bg-sky-400/10 border border-sky-400/20' : 'border border-transparent'"
                                            @click="select(@js($friend->username), @js($friend->email))"
                                        >
                                            <div class="flex items-center justify-between gap-3">
                                                <span class="truncate">{{ $friend->username }}</span>
                                                <span class="ui-caption truncate">{{ $friend->email }}</span>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <button class="ui-btn ui-btn-neon sm:shrink-0" type="submit">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Add collaborator</span>
                    </button>
                </form>

                @if (session('user_added'))
                    <div class="mt-3 ui-card-soft p-3 border border-red-500/30 text-red-300 text-sm">
                        {{ session('user_added') }}
                    </div>
                @endif
            </div>
        @endcan

        @can('delete', $board)
            <div class="ui-card p-5 border border-red-500/20">
                <div class="ui-h2">Danger zone</div>
                <div class="ui-caption mt-1">Deleting a board is permanent.</div>

                <form method="post" action="/delete_board/{{$board->id}}" class="mt-4">
                    @csrf
                    @method('DELETE')
                    <button class="ui-btn ui-btn-danger" type="submit">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Delete this board</span>
                    </button>
                </form>
            </div>
        @endcan
    </div>
</x-layout>