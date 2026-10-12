<div class="fi-ta-header todays-dispatches-header">
    <h2 class="fi-ta-header-heading todays-dispatches-header-title">Today’s Dispatches</h2>

    <div class="todays-dispatches-header-filters">
        <div class="todays-dispatches-driver-filter">
            <x-filament::input.wrapper :prefix="__('Driver')">
                <x-filament::input.select wire:model.live="tableFilters.driver_id.value">
                    <option value="">{{ __('All Drivers') }}</option>

                    @foreach ($drivers as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>

        <div class="todays-dispatches-per-page">
            <x-filament::input.wrapper :prefix="__('Per page')">
                <x-filament::input.select
                    class="todays-dispatches-per-page-select"
                    wire:model.live="tableRecordsPerPage"
                >
                    @foreach ($recordsPerPageOptions as $recordsPerPageOption)
                        <option value="{{ $recordsPerPageOption }}">{{ $recordsPerPageOption }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
    </div>
</div>

<style>
    .todays-dispatches-header {
        display: flex !important;
        flex-direction: row !important;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        width: 100%;
    }

    .todays-dispatches-header-title {
        flex: 1 1 auto;
        text-align: left;
        margin: 0;
    }

    .todays-dispatches-header-filters {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-left: auto;
    }

    .todays-dispatches-driver-filter {
        flex: 0 0 13rem;
    }

    .todays-dispatches-per-page {
        flex: 0 0 10rem;
    }

    .todays-dispatches-per-page-select {
        padding-inline: 0.375rem !important;
    }

    @media (max-width: 639px) {
        .todays-dispatches-driver-filter {
            flex-basis: 10rem;
        }

        .todays-dispatches-per-page {
            flex-basis: 9rem;
        }
    }

    .dispatches-table-per-page .fi-pagination-records-per-page-select-ctn {
        display: none;
    }
</style>
