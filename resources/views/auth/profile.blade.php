<x-layout :boardList='$boardList ?? null'>
    <div class="flex items-start gap-4">
            <a href="javascript:history.back()" class="shrink-0 w-10 h-10 bg-gray-900 hover:bg-gray-600 border border-gray-700 rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition shadow-lg" title="Back to boards">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>

            <div>
                <div class="ui-caption">Account</div>
                <h1 class="ui-h1">My profile</h1>
            </div>
        </div>
</x-layout>