<x-layouts.app :title="__('Links Management')">
    <livewire:links-list />
    <livewire:analytics-popup :linkId="$linkId ?? null" />
</x-layouts.app>
