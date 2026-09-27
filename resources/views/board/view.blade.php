<x-layout :boardList='$boardList ?? null'>
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">My Boards</h1>
            <p class="text-sm text-gray-400 mt-1">Manage your projects and track your task progress.</p>
        </div>
        
        <!-- New Board Button -->
        <a href="/board_create" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-indigo-600/20 transition flex items-center gap-2">
            <i class="fas fa-plus"></i> New Board
        </a>
    </div>

    <!-- Cards Grid: 3 per row on large screens -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($boardList as $board)
            <div class="bg-[#121620] rounded-2xl shadow-lg border border-gray-800/80 flex flex-col justify-between overflow-hidden group hover:border-indigo-500/50 transition-all duration-200">
                
                <!-- Card Body -->
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold">
                            <i class="fas fa-columns"></i>
                        </div>
                        <a href="{{ route('settings', $board->id) }}" class="text-gray-500 hover:text-gray-300 p-1 transition" title="Board settings">
                            <i class="fas fa-cog"></i>
                        </a>
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
        @empty
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
        @endforelse
    </div>
</x-layout>