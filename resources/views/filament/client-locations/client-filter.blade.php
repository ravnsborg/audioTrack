<div class="w-full sm:w-64">
    <x-filament::input.wrapper :prefix="__('Client')">
        <x-filament::input.select wire:model.live="tableFilters.client_id.value">
            <option value="">{{ __('All Clients') }}</option>

            @foreach ($clients as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </x-filament::input.select>
    </x-filament::input.wrapper>
</div>
