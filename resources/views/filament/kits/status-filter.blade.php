<div class="w-full sm:w-64">
    <x-filament::input.wrapper :prefix="__('Status')">
        <x-filament::input.select wire:model.live="tableFilters.kit_status_id.value">
            <option value="">{{ __('All Statuses') }}</option>

            @foreach ($statuses as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </x-filament::input.select>
    </x-filament::input.wrapper>
</div>
