<div class="w-full sm:w-64">
    <x-filament::input.wrapper :prefix="__('Status')">
        <x-filament::input.select wire:model.live="tableFilters.status.value">
            <option value="">{{ __('All Statuses') }}</option>

            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-filament::input.select>
    </x-filament::input.wrapper>
</div>
