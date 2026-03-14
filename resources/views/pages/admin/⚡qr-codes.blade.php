<?php

use App\Models\QrCode;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('All QR Codes')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $filterClient = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterClient(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function qrCodes(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return QrCode::with('user')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->filterClient, fn ($q) => $q->where('user_id', $this->filterClient))
            ->latest()
            ->paginate(20);
    }

    #[Computed]
    public function clientList(): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('role', 'client')->orderBy('name')->get(['id', 'name']);
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('All QR Codes') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Overview of all QR codes across all clients') }}</flux:text>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex gap-3">
        <div class="flex-1">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Search by name...') }}"
                icon="magnifying-glass"
                clearable
            />
        </div>
        <div class="w-56">
            <flux:select wire:model.live="filterClient" placeholder="{{ __('All clients') }}">
                <flux:select.option value="">{{ __('All clients') }}</flux:select.option>
                @foreach($this->clientList as $client)
                    <flux:select.option value="{{ $client->id }}">{{ $client->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <table class="w-full text-sm">
            <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">{{ __('Name') }}</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">{{ __('Client') }}</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">{{ __('Destination URL') }}</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">{{ __('Fallback URL') }}</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">{{ __('Scans') }}</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">{{ __('Slug') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                @forelse($this->qrCodes as $qrCode)
                    <tr class="bg-white dark:bg-neutral-900" wire:key="qr-{{ $qrCode->id }}">
                        <td class="px-4 py-3 font-medium">{{ $qrCode->name }}</td>
                        <td class="px-4 py-3 text-neutral-500">{{ $qrCode->user->name }}</td>
                        <td class="max-w-xs px-4 py-3">
                            <span class="block truncate text-neutral-500" title="{{ $qrCode->destination_url }}">
                                {{ $qrCode->destination_url }}
                            </span>
                        </td>
                        <td class="max-w-xs px-4 py-3">
                            <span class="block truncate text-neutral-500" title="{{ $qrCode->fallback_url }}">
                                {{ $qrCode->fallback_url ?: '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-neutral-500">{{ number_format($qrCode->scans_count) }}</td>
                        <td class="px-4 py-3">
                            @if($qrCode->is_active)
                                <flux:badge color="green" size="sm">{{ __('Active') }}</flux:badge>
                            @else
                                <flux:badge color="red" size="sm">{{ __('Inactive') }}</flux:badge>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <flux:badge variant="outline" size="sm">{{ $qrCode->slug }}</flux:badge>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center">
                            <flux:icon.qr-code class="mx-auto mb-3 size-12 text-neutral-300 dark:text-neutral-600" />
                            <flux:text class="text-neutral-400">{{ __('No QR codes found.') }}</flux:text>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($this->qrCodes->hasPages())
        <div>{{ $this->qrCodes->links() }}</div>
    @endif

</div>
