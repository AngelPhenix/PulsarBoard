<x-layout>
    <div class="mx-auto max-w-md">
        <div class="ui-card p-6">
            <div class="ui-caption">Welcome back</div>
            <h1 class="ui-h1 mt-1">Login</h1>

            <form class="mt-6 space-y-4" method="post" action="/login">
                @csrf

                <div class="ui-field">
                    <label class="ui-label" for="username">Username</label>
                    <input class="ui-input" type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Your username" autocomplete="username" />
                    <x-form-error fieldname='username' />
                </div>

                <div class="ui-field">
                    <label class="ui-label" for="password">Password</label>
                    <input class="ui-input" type="password" id="password" name="password" autocomplete="current-password" />
                    <a href="{{ route('password.request') }}" class="text-xs text-indigo-400 hover:underline">
                        Forgot your password?
                    </a>
                </div>

                <button class="ui-btn ui-btn-neon w-full" type="submit">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Login</span>
                </button>
            </form>
        </div>
    </div>
</x-layout>