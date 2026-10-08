<x-layout>
    <div class="mx-auto max-w-md space-y-6 pt-12">
        <div class="text-center space-y-2">
            <h1 class="ui-h1">Forgot your password?</h1>
            <p class="ui-caption">No problem. Just let us know your email address and we will email you a password reset link.</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-4 text-sm font-medium text-green-400 bg-green-500/10 border border-green-500/20 p-3 rounded-xl">
                {{ session('status') }}
            </div>
        @endif

        <div class="ui-card p-6">
            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <div class="ui-field">
                    <label class="ui-label" for="email">Email</label>
                    <input class="ui-input" type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                    <x-form-error fieldname='email'/>
                </div>

                <div class="pt-2">
                    <button class="ui-btn ui-btn-neon w-full" type="submit">
                        <span>Email Password Reset Link</span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="text-center">
            <a href="{{ route('auth.login') }}" class="text-xs text-gray-400 hover:text-white transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to login
            </a>
        </div>
    </div>
</x-layout>