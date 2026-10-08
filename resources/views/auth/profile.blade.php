<x-layout :boardList='$boardList ?? null'>
    <div class="mx-auto max-w-2xl space-y-8 pb-12">
        
        <!-- Header -->
        <div class="flex items-start gap-4">
            <a href="javascript:history.back()" class="shrink-0 w-10 h-10 bg-gray-900 hover:bg-gray-600 border border-gray-700 rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition shadow-lg" title="Back">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <div class="ui-caption">Account</div>
                <h1 class="ui-h1">My profile</h1>
            </div>
        </div>

        <!-- Success Messages -->
        @if (session('status') === 'profile-updated')
            <div class="text-sm font-medium text-green-400 bg-green-500/10 border border-green-500/20 p-3 rounded-xl">
                Profile updated successfully.
            </div>
        @endif
        @if (session('status') === 'password-updated')
            <div class="text-sm font-medium text-green-400 bg-green-500/10 border border-green-500/20 p-3 rounded-xl">
                Password updated successfully.
            </div>
        @endif

        <!-- 1. Profile Information Form -->
        <div class="ui-card p-6 space-y-6">
            <div>
                <h2 class="text-lg font-semibold text-white">Profile Information</h2>
                <p class="text-xs text-gray-400 mt-1">Update your account's profile information and email address.</p>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('patch')

                <div class="ui-field">
                    <label class="ui-label" for="username">Username</label>
                    <input class="ui-input" type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required autofocus autocomplete="username" />
                    <x-form-error fieldname='username'/>
                </div>

                <div class="ui-field">
                    <label class="ui-label" for="email">Email</label>
                    <input class="ui-input" type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                    <x-form-error fieldname='email'/>
                </div>

                <div class="flex justify-end pt-2">
                    <button class="ui-btn ui-btn-neon" type="submit">
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. Update Password Form -->
        <div class="ui-card p-6 space-y-6">
            <div>
                <h2 class="text-lg font-semibold text-white">Update Password</h2>
                <p class="text-xs text-gray-400 mt-1">Ensure your account is using a long, random password to stay secure.</p>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('put')

                <div class="ui-field">
                    <label class="ui-label" for="current_password">Current Password</label>
                    <input class="ui-input" type="password" name="current_password" id="current_password" autocomplete="current-password" />
                    <x-form-error fieldname='current_password'/>
                </div>

                <div class="ui-field">
                    <label class="ui-label" for="password">New Password</label>
                    <input class="ui-input" type="password" name="password" id="password" autocomplete="new-password" />
                    <x-form-error fieldname='password'/>
                </div>

                <div class="ui-field">
                    <label class="ui-label" for="password_confirmation">Confirm Password</label>
                    <input class="ui-input" type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" />
                    <x-form-error fieldname='password_confirmation'/>
                </div>

                <div class="flex justify-end pt-2">
                    <button class="ui-btn ui-btn-neon" type="submit">
                        <span>Update Password</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. Delete Account (Danger Zone) -->
        <div class="ui-card p-6 space-y-6 border-red-500/20 bg-red-500/5">
            <div>
                <h2 class="text-lg font-semibold text-red-400">Delete Account</h2>
                <p class="text-xs text-gray-400 mt-1">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
            </div>

            <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4" onsubmit="return confirm('Are you sure you want to delete your account? This action is irreversible.');">
                @csrf
                @method('delete')

                <div class="ui-field">
                    <label class="ui-label" for="delete_password">Enter your password to confirm</label>
                    <input class="ui-input" type="password" name="password" id="delete_password" placeholder="Password required" required />
                    <x-form-error fieldname='password'/>
                </div>

                <div class="flex justify-end pt-2">
                    <button class="ui-btn bg-red-600 hover:bg-red-500 text-white font-medium px-4 py-2 rounded-xl transition" type="submit">
                        <span>Delete Account</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-layout>