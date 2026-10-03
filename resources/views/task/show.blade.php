<x-layout :boardList='$boardList ?? null' :board='$board ?? null' :friends='$friends ?? null'>

    <div class="space-y-6">
        @if ($errors->any())
            <div class="ui-card-soft p-4 border border-red-500/30">
                <div class="ui-h2">Something went wrong</div>
                <ul class="mt-2 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-red-400 text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="min-w-0">
                <div class="ui-caption">Board</div>
                <h1 class="ui-h1 truncate">{{ $board->name }}</h1>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="ui-caption mr-1">Collaborators</span>
                    @foreach ($board->users as $user)
                        @if ($user->username == $board->owner->username)
                            <span class="ui-chip border-white/10 bg-white/5">
                                <i class="fa-solid fa-crown text-sky-300"></i>
                                <span class="font-semibold">{{ $user->username }}</span>
                            </span>
                        @else
                            <span class="ui-chip">
                                <i class="fa-regular fa-user ui-muted"></i>
                                <span>{{ $user->username }}</span>
                            </span>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-2">
                @can('delete', $board)
                    <a href="{{ route('settings', ['board' => $board->id]) }}" class="ui-btn ui-btn-neon">
                        <i class="fa-solid fa-gear"></i>
                        <span class="hidden sm:inline">Settings</span>
                    </a>
                @endcan
            </div>
        </div>

        <div class="ui-card ui-glow p-5">
            <form method="post" action="{{ route('post_action', ['board' => $board->id])}}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                @csrf

                <div class="ui-field flex-1">
                    <label class="ui-label" for="name">New task</label>
                    <input class="ui-input" type="text" id="name" name="name" placeholder="e.g. Draft the landing copy" autocomplete="off" autofocus/>
                </div>

                <button class="ui-btn ui-btn-neon sm:shrink-0" type="submit">
                    <i class="fa-solid fa-plus"></i>
                    <span>Add task</span>
                </button>
            </form>
        </div>

        <section class="ui-card overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-white/5">
                <div>
                    <div class="ui-h2">Tasks</div>
                    <div class="ui-caption">{{ $tasks->count() }} total</div>
                </div>
            </div>

            <ul class="divide-y divide-white/5">
                @foreach ($tasks as $task)
                    <li class="group flex items-stretch gap-0 hover:bg-white/3 transition-colors {{ $task->is_completed ? 'opacity-60' : '' }}">
                        <form class="flex items-center px-4" method="post" action="{{ route('completed_task', ['task' => $task->id])}}">
                            @csrf
                            @method('PATCH')
                            <input type="checkbox" name="is_completed" id="is_completed" class="ui-checkbox" {{ $task->is_completed ? 'checked' : ''}} onchange="this.form.submit()">
                        </form>

                        <div class="flex-1 min-w-0 py-3 pr-4">
                            <div class="grid grid-cols-[1fr_auto] items-center gap-x-3">
                                <div class="min-w-0">
                                    <p class="pl-1 font-semibold leading-snug break-words {{ $task->is_completed ? 'line-through ui-muted' : '' }}">
                                        {{ $task->name }}
                                    </p>

                                    <div class="mt-2 flex flex-wrap items-center gap-2">
                                        @foreach ($task->labels as $label)
                                            <span class="ui-tag" style="--tag: {{ $label->color }};">
                                                {{ $label->name }}
                                            </span>
                                        @endforeach
                                        <x-modal :task="$task" :labels="$labels" />
                                    </div>
                                </div>

                                <form method="post" action="/delete_task/{{ $task->id }}" class="self-center">
                                    @csrf
                                    @method('DELETE')
                                    <button class="ui-btn ui-btn-danger ui-icon-btn" type="submit" title="Delete task">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

</x-layout>