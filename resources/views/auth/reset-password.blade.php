<x-layout>
    <div class="mx-auto max-w-md space-y-6 pt-12">
        <div class="text-center space-y-2">
            <h1 class="ui-h1">Reset Password</h1>
            <p class="ui-caption">Enter your new password below.</p>
        </div>

        <div class="ui-card p-6">
            <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="ui-field">
                    <label class="ui-label" for="email">Email</label>
                    <input class="ui-input" type="email" name="email" id="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" />
                    <x-form-error fieldname='email'/>
                </div>

                <div class="ui-field">
                    <label class="ui-label" for="password">New Password</label>
                    <input class="ui-input" type="password" name="password" id="password" required autocomplete="new-password" />
                    <x-form-error fieldname='password'/>
                </div>

                <div class="ui-field">
                    <label class="ui-label" for="password_confirmation">Confirm Password</label>
                    <input class="ui-input" type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" />
                    <x-form-error fieldname='password_confirmation'/>
                </div>

                <div class="pt-2">
                    <button class="ui-btn ui-btn-neon w-full" type="submit">
                        <span>Reset Password</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>