@props(['task', 'labels'])

<div x-data="{ open: false }">
    <!-- Button to open the modal -->
    <button @click="open = true" type="button" class="ui-tag ui-tag-btn">
        <i class="fa-solid fa-tag text-sky-300"></i>
        <span>Labels</span>
        <span class="ui-muted">+</span>
    </button>

    <!-- Modal background -->
    <div x-show="open" x-cloak class="fixed inset-0 bg-black/60 flex items-center justify-center p-4">
        <!-- Modal content -->
        <div @click.away="open = false" class="ui-card ui-glow w-full max-w-md p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="ui-h2">Labels</div>
                    <div class="ui-caption mt-1">Add a new label or toggle existing ones.</div>
                </div>
                <button type="button" @click="open = false" class="ui-btn ui-icon-btn" title="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="form_add_{{$task->id}}" method="POST" action="/add_label/{{$task->id}}" class="mt-4 flex items-end gap-2">
                @csrf
                <div class="ui-field flex-1">
                    <label class="ui-label" for="name">Name</label>
                    <input type="text" id="name" name="name" class="ui-input" placeholder="e.g. Urgent" autocomplete="off">
                </div>
                <div class="ui-field">
                    <label class="ui-label" for="color">Color</label>
                    <input id="color" type="color" name="color" class="ui-color">
                </div>
                <button type="submit" form="form_add_{{$task->id}}" class="ui-btn ui-btn-neon px-4 py-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Add</span>
                </button>
            </form>

            <div class="mt-5 ui-label">Toggle existing</div>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach ($labels as $label)
                {{-- @if ($task->labels->contains($label))
                    <form method="POST" action="/update_task/{{ $task->id }}/label/{{ $label->id }}" class="mb-1 inline-block">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="bg-[{{$label->color}}]/75 text-white px-2 py-1 rounded truncate">{{ $label->name }}</button>
                    </form>
                @endif --}}

                @if ($task->labels->contains($label))
                    <form method="POST" action="/update_task/{{ $task->id }}/label/{{ $label->id }}" class="inline-block">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="ui-tag ui-tag-btn is-selected">
                            <i class="fa-solid fa-check text-sky-300"></i>
                            <span class="truncate max-w-[160px]">{{ $label->name }}</span>
                        </button>
                    </form>
                @else
                    <form method="POST" action="/update_task/{{ $task->id }}/label/{{ $label->id }}" class="inline-block">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="ui-tag ui-tag-btn" style="--tag: {{ $label->color }};">
                            <span class="truncate max-w-[160px]">{{ $label->name }}</span>
                        </button>
                    </form>
                @endif

                {{-- <form method="POST" action="/update_task/{{ $task->id }}/label/{{ $label->id }}" class="mb-1 inline-block overflow-wrap:anywhere">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="bg-[{{$label->color}}]/75 text-white px-2 py-1 rounded truncate">{{ $label->name }}</button>
                </form> --}}
            @endforeach
            </div>
        </div>
    </div>
</div>
