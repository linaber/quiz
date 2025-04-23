{{--<x-filament::widget>--}}
{{--    <x-filament::card>--}}
{{--        @if($user = $this->getViewData()['user'])--}}
{{--            <div class="flex items-center gap-x-4">--}}


{{--                <div class="flex-1">--}}
{{--                    <h2 class="text-lg font-medium text-gray-900">--}}
{{--                        {{ $user->name }}--}}
{{--                    </h2>--}}

{{--                    <p class="text-sm text-gray-500 py-2">--}}
{{--                        {{ $user->email }}--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--        --}}

{{--            <x-filament::actions :actions="$this->getActions()"  class="mt-4" />--}}
{{--        @endif--}}
{{--    </x-filament::card>--}}
{{--</x-filament::widget>--}}

{{--<x-filament::widget>--}}
{{--    <x-filament::card>--}}
{{--        <div class="flex items-center space-x-4 p-6">--}}


{{--            <div>--}}
{{--                <h2 class="text-lg font-medium text-gray-900">--}}
{{--                    {{ $user->name }}--}}
{{--                </h2>--}}

{{--                <p class="text-sm text-gray-500 py-2">--}}
{{--                    {{ $user->email }}--}}
{{--                </p>--}}
{{--            </div>--}}

{{--            <div class="ml-auto">--}}
{{--                <x-filament::actions :actions="$this->getActions()"  class="mt-4" />--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </x-filament::card>--}}
{{--</x-filament::widget>--}}


{{--<x-filament-widgets::widget class="fi-wi-stats-overview">--}}
{{--    <div class="fi-wi-stats-overview-stats-ctn gap-6">--}}
{{--        <x-filament::card class="col-span-6"> <!-- Явно указываем ширину -->--}}
{{--            <div class="flex items-center space-x-4 p-6">--}}


{{--                <div>--}}
{{--                    <h2 class="text-lg font-medium text-gray-900">--}}
{{--                        {{ $user->name }}--}}
{{--                    </h2>--}}

{{--                    <p class="text-sm text-gray-500 py-2">--}}
{{--                        {{ $user->email }}--}}
{{--                    </p>--}}
{{--                </div>--}}

{{--                <div class="ml-auto">--}}
{{--                    <x-filament::actions :actions="$this->getActions()" class="mt-4" />--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </x-filament::card>--}}
{{--    </div>--}}
{{--</x-filament-widgets::widget>--}}
{{--<x-filament::widget class="max-w-full">--}}
    <x-filament::card class="p-6">
        <div class="flex items-center space-x-4">
            <div>
{{--                <h2 class="text-lg font-medium text-gray-900">--}}
{{--                    {{ $user->name }}--}}
{{--                </h2>--}}

                <p class="text-sm text-gray-500 py-2">
                    {{ $user->email }}
                </p>
            </div>

            <div class="ml-auto">
                <x-filament::actions :actions="$this->getActions()" class="mt-4" />
            </div>
        </div>
    </x-filament::card>
{{--</x-filament::widget>--}}
