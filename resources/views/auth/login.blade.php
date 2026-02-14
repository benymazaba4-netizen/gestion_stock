<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Mot de passe')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Se souvenir de moi') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4 gap-4">
            {{-- Cette condition vérifie si la route 'register' existe avant d'afficher le bouton --}}
            @if (Route::has('register'))
                <a class="underline text-sm text-indigo-600 hover:text-indigo-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}">
                    {{ __('Créer un compte') }}
                </a>
            @endif

            <x-primary-button>
                {{ __('Se connecter') }}
            </x-primary-button>
        </div>

        @if (Route::has('password.request'))
            <div class="text-center mt-4">
                <a class="underline text-xs text-gray-500 hover:text-gray-700" href="{{ route('password.request') }}">
                    {{ __('Mot de passe oublié ?') }}
                </a>
            </div>
        @endif
    </form>
</x-guest-layout>