<x-layout :boardList='$boardList ?? null'>
    <div class="mx-auto max-w-lg space-y-6" x-data="{ tasks: [''] }">
        <div>
            <div class="ui-caption">Boards</div>
            <h1 class="ui-h1">Create a new board</h1>
        </div>

        <div class="ui-card p-5">
            <form class="space-y-6" method="post" action="/board">
                @csrf
                
                <!-- Nom du Board -->
                <div class="ui-field">
                    <label class="ui-label" for="name">Board name</label>
                    <input class="ui-input" type="text" name="name" id="name" placeholder="e.g. Sprint Planning" autocomplete="off" value="{{ old('name') }}" />
                    <x-form-error fieldname='name'/>
                </div>

                <!-- Tag de la board -->
                <div class="ui-field">
                    <label class="ui-label" for="tag">Tag / Category (optional)</label>
                    <input class="ui-input" type="text" name="tag" id="tag" placeholder="e.g. Work, Personal, Hobby..." autocomplete="off" value="{{ old('tag') }}" />
                    <x-form-error fieldname='tag'/>
                </div>

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