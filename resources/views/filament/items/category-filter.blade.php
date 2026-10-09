<div class="w-full sm:w-64">
    <x-filament::input.wrapper :prefix="__('Category')">
        <x-filament::input.select wire:model.live="tableFilters.category_id.value">
            <option value="">{{ __('All Categories') }}</option>

            @foreach ($categories as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </x-filament::input.select>
    </x-filament::input.wrapper>
</div>
