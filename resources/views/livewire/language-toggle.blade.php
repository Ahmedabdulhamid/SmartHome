<x-filament::dropdown placement="bottom-start">
    <x-slot name="trigger">
        <x-filament::button color="primary" icon="heroicon-o-language" class="flex items-center gap-2">
            <span class="text-sm font-medium">
                {{ $availableLanguages[$currentLanguage]['name'] ?? $currentLanguage }}
            </span>
        </x-filament::button>
    </x-slot>

    <ul class="filament-dropdown-list mt-2 w-40 rounded-xl bg-white shadow-lg ring-1 ring-black/5 divide-y divide-gray-100">
        @foreach ($availableLanguages as $code => $lang)
            <li>
                <a href="#"
                   wire:click="switchLanguage('{{ $code }}')"
                   class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-primary-100 hover:text-primary-600 rounded-lg transition">
                    {{ $lang['name'] }}
                </a>
            </li>
        @endforeach
    </ul>
</x-filament::dropdown>
