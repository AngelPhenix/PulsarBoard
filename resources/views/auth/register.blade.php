<x-layout>
    <div class="mx-auto max-w-md">
        <div class="ui-card p-6">
            <div class="ui-caption">Create your account</div>
            <h1 class="ui-h1 mt-1">Register</h1>

            <form class="mt-6 space-y-4" method="post" action="/register" enctype="multipart/form-data">
                @csrf

                <div class="ui-field">
                    <label class="ui-label" for="username">Username</label>
                    <input class="ui-input" type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Your username" autocomplete="username"/>
                    <x-form-error fieldname='username' />
                </div>

                <div class="ui-field">
                    <label class="ui-label" for="email">Email</label>
                    <input class="ui-input" type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@domain.com" autocomplete="email"/>
                    <x-form-error fieldname='email' />
                </div>

                <div class="ui-field">
                    <label class="ui-label" for="password">Password</label>
                    <input class="ui-input" type="password" id="password" name="password" autocomplete="new-password" />
                    <x-form-error fieldname='password' />
                </div>

                <button class="ui-btn ui-btn-neon w-full" type="submit">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Create</span>
                </button>
            </form>
        </div>
    </div>
</x-layout>