<x-layout :boardList='$boardList ?? null'>
    <div class="mx-auto max-w-lg space-y-6" x-data="{ tag: @js(old('tag', '')), tasks: [''] }">
        <div class="flex items-start gap-4">
            <a href="{{ route('board.view') }}" class="shrink-0 w-10 h-10 bg-gray-900 hover:bg-gray-600 border border-gray-700 rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition shadow-lg" title="Back to boards">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <h1 class="ui-h1">Create a new board</h1>
        </div>

        <div class="ui-card p-5">
            <form class="space-y-6" method="post" action="/board">
                @csrf
                
                <!-- Nom du Board -->
                <div class="ui-field">
                    <label class="ui-label" for="name">Board name</label>
                    <input class="ui-input" type="text" name="name" id="name" placeholder="e.g. Sprint Planning" autocomplete="off" autofocus value="{{ old('name') }}" />
                    <x-form-error fieldname='name'/>
                </div>

                <!-- Tag de la board -->
                <div class="ui-field">
                    <label class="ui-label" for="tag">Tag / Category (optional)</label>
                    <input class="ui-input" type="text" name="tag" id="tag" x-model="tag" placeholder="e.g. Work, Personal, Hobby..." autocomplete="off" />
                    <x-form-error fieldname='tag'/>
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

                <!-- Tâches initiales -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="ui-label">Initial tasks (optional)</label>
                        <button type="button" @click="tasks.push('')" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold flex items-center gap-1">
                            <i class="fa-solid fa-plus"></i> Add another task
                        </button>
                    </div>

                    <template x-for="(task, index) in tasks" :key="index">
                        <div class="flex items-center gap-2">
                            <input class="ui-input" type="text" :name="'tasks[]'" x-model="tasks[index]" :placeholder="'Task #' + (index + 1)" autocomplete="off" />
                            <button type="button" @click="tasks.splice(index, 1)" x-show="tasks.length > 1" class="ui-btn ui-btn-danger ui-icon-btn shrink-0" title="Remove">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <button class="ui-btn ui-btn-neon w-full" type="submit">
                    <i class="fa-solid fa-plus"></i>
                    <span>Create Board & Tasks</span>
                </button>
            </form>
        </div>
    </div>
</x-layout>