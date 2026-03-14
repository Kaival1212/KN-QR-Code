<?php

use App\Models\QrCode;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('My QR Codes')] class extends Component {
    use WithPagination;

    public string $search = '';

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;
    public bool $showPreviewModal = false;

    public string $name = '';
    public string $destinationUrl = '';
    public string $fallbackUrl = '';
    public bool $isActive = true;

    public ?int $editingQrCodeId = null;
    public ?int $deletingQrCodeId = null;
    public ?string $previewSvg = null;
    public ?string $previewName = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function qrCodes(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = auth()->user()->isAdmin()
            ? QrCode::with('user')->latest()
            : auth()->user()->qrCodes()->latest();

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        return $query->paginate(10);
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal(int $qrCodeId): void
    {
        $qrCode = $this->findAuthorizedQrCode($qrCodeId);

        $this->editingQrCodeId = $qrCode->id;
        $this->name = $qrCode->name;
        $this->destinationUrl = $qrCode->destination_url;
        $this->fallbackUrl = $qrCode->fallback_url ?? '';
        $this->isActive = $qrCode->is_active;
        $this->showEditModal = true;
    }

    public function openDeleteModal(int $qrCodeId): void
    {
        $this->findAuthorizedQrCode($qrCodeId);
        $this->deletingQrCodeId = $qrCodeId;
        $this->showDeleteModal = true;
    }

    public function openPreviewModal(int $qrCodeId): void
    {
        $qrCode = $this->findAuthorizedQrCode($qrCodeId);
        $this->previewSvg = $this->generateSvg($qrCode);
        $this->previewName = $qrCode->name;
        $this->showPreviewModal = true;
    }

    public function createQrCode(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'destinationUrl' => ['required', 'url', 'max:2048'],
            'fallbackUrl' => ['nullable', 'url', 'max:2048'],
            'isActive' => ['boolean'],
        ]);

        auth()->user()->qrCodes()->create([
            'name' => $validated['name'],
            'slug' => QrCode::generateUniqueSlug(),
            'destination_url' => $validated['destinationUrl'],
            'fallback_url' => $validated['fallbackUrl'] ?: null,
            'is_active' => $validated['isActive'],
        ]);

        $this->showCreateModal = false;
        $this->resetForm();
        unset($this->qrCodes);
    }

    public function updateQrCode(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'destinationUrl' => ['required', 'url', 'max:2048'],
            'fallbackUrl' => ['nullable', 'url', 'max:2048'],
            'isActive' => ['boolean'],
        ]);

        $qrCode = $this->findAuthorizedQrCode($this->editingQrCodeId);

        $qrCode->update([
            'name' => $validated['name'],
            'destination_url' => $validated['destinationUrl'],
            'fallback_url' => $validated['fallbackUrl'] ?: null,
            'is_active' => $validated['isActive'],
        ]);

        $this->showEditModal = false;
        $this->resetForm();
        unset($this->qrCodes);
    }

    public function deleteQrCode(): void
    {
        $qrCode = $this->findAuthorizedQrCode($this->deletingQrCodeId);
        $qrCode->delete();

        $this->showDeleteModal = false;
        $this->deletingQrCodeId = null;
        unset($this->qrCodes);
    }

    private function findAuthorizedQrCode(int $id): QrCode
    {
        $qrCode = QrCode::findOrFail($id);
        abort_unless(auth()->user()->isAdmin() || $qrCode->user_id === auth()->id(), 403);

        return $qrCode;
    }

    private function generateSvg(QrCode $qrCode): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd
        );

        return (new Writer($renderer))->writeString($qrCode->getRedirectUrl());
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->destinationUrl = '';
        $this->fallbackUrl = '';
        $this->isActive = true;
        $this->editingQrCodeId = null;
        $this->resetValidation();
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('My QR Codes') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Manage and download your QR codes') }}</flux:text>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="openCreateModal">
            {{ __('New QR Code') }}
        </flux:button>
    </div>

    {{-- Search --}}
    <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="{{ __('Search QR codes...') }}"
        icon="magnifying-glass"
        clearable
    />

    {{-- Table --}}
    <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <table class="w-full text-sm">
            <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">{{ __('Name') }}</th>
                    @if(auth()->user()->isAdmin())
                        <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">{{ __('Client') }}</th>
                    @endif
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">{{ __('Destination URL') }}</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">{{ __('Fallback URL') }}</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">{{ __('Scans') }}</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-right font-medium text-neutral-600 dark:text-neutral-300">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                @forelse($this->qrCodes as $qrCode)
                    <tr class="bg-white dark:bg-neutral-900" wire:key="qr-{{ $qrCode->id }}">
                        <td class="px-4 py-3 font-medium">{{ $qrCode->name }}</td>
                        @if(auth()->user()->isAdmin())
                            <td class="px-4 py-3 text-neutral-500">{{ $qrCode->user->name }}</td>
                        @endif
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
                            <div class="flex items-center justify-end gap-2">
                                <flux:tooltip content="{{ __('Preview') }}">
                                    <flux:button size="sm" icon="eye" variant="ghost" wire:click="openPreviewModal({{ $qrCode->id }})" />
                                </flux:tooltip>
                                <flux:dropdown>
                                    <flux:tooltip content="{{ __('Download') }}">
                                        <flux:button size="sm" icon="arrow-down-tray" variant="ghost" />
                                    </flux:tooltip>
                                    <flux:menu>
                                        <flux:menu.item icon="document" tag="a" href="{{ route('qr-codes.download', [$qrCode, 'svg']) }}">
                                            {{ __('Download SVG') }}
                                        </flux:menu.item>
                                        <flux:menu.item icon="photo" tag="a" href="{{ route('qr-codes.download', [$qrCode, 'png']) }}">
                                            {{ __('Download PNG') }}
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                                <flux:tooltip content="{{ __('Edit') }}">
                                    <flux:button size="sm" icon="pencil" variant="ghost" wire:click="openEditModal({{ $qrCode->id }})" />
                                </flux:tooltip>
                                <flux:tooltip content="{{ __('Delete') }}">
                                    <flux:button size="sm" icon="trash" variant="ghost" wire:click="openDeleteModal({{ $qrCode->id }})" class="text-red-500 hover:text-red-600" />
                                </flux:tooltip>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 7 : 6 }}" class="px-4 py-16 text-center">
                            <flux:icon.qr-code class="mx-auto mb-3 size-12 text-neutral-300 dark:text-neutral-600" />
                            <flux:text class="text-neutral-400">{{ __('No QR codes yet. Create your first one!') }}</flux:text>
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

    {{-- Create Modal --}}
    <flux:modal wire:model="showCreateModal" class="w-full max-w-lg">
        <flux:heading size="lg">{{ __('Create QR Code') }}</flux:heading>
        <flux:text class="mt-1">{{ __('The QR code points to your redirect URL — change the destination anytime without reprinting.') }}</flux:text>

        <form wire:submit="createQrCode" class="mt-6 space-y-4">
            <flux:field>
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:input wire:model="name" placeholder="{{ __('e.g. Main Website QR') }}" autofocus />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Destination URL') }}</flux:label>
                <flux:input wire:model="destinationUrl" type="url" placeholder="https://yourclient.com" />
                <flux:description>{{ __('Where the QR code takes users when scanned.') }}</flux:description>
                <flux:error name="destinationUrl" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Fallback URL') }} <span class="text-xs text-neutral-400">({{ __('optional') }})</span></flux:label>
                <flux:input wire:model="fallbackUrl" type="url" placeholder="https://fallback.com" />
                <flux:description>{{ __('Used when QR code is inactive. Leave blank to show 404.') }}</flux:description>
                <flux:error name="fallbackUrl" />
            </flux:field>

            <flux:field>
                <flux:switch wire:model="isActive" label="{{ __('Active') }}" description="{{ __('Inactive QR codes redirect to the fallback URL.') }}" />
            </flux:field>

            <div class="flex justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showCreateModal', false)">{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">{{ __('Create QR Code') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Edit Modal --}}
    <flux:modal wire:model="showEditModal" class="w-full max-w-lg">
        <flux:heading size="lg">{{ __('Edit QR Code') }}</flux:heading>
        <flux:text class="mt-1">{{ __('Update the destination or fallback URL. The QR code itself never changes.') }}</flux:text>

        <form wire:submit="updateQrCode" class="mt-6 space-y-4">
            <flux:field>
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:input wire:model="name" placeholder="{{ __('e.g. Main Website QR') }}" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Destination URL') }}</flux:label>
                <flux:input wire:model="destinationUrl" type="url" placeholder="https://yourclient.com" />
                <flux:description>{{ __('Where the QR code takes users when scanned.') }}</flux:description>
                <flux:error name="destinationUrl" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Fallback URL') }} <span class="text-xs text-neutral-400">({{ __('optional') }})</span></flux:label>
                <flux:input wire:model="fallbackUrl" type="url" placeholder="https://fallback.com" />
                <flux:description>{{ __('Used when QR code is inactive. Leave blank to show 404.') }}</flux:description>
                <flux:error name="fallbackUrl" />
            </flux:field>

            <flux:field>
                <flux:switch wire:model="isActive" label="{{ __('Active') }}" description="{{ __('Inactive QR codes redirect to the fallback URL.') }}" />
            </flux:field>

            <div class="flex justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showEditModal', false)">{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">{{ __('Save Changes') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Preview Modal --}}
    <flux:modal wire:model="showPreviewModal" class="w-full max-w-sm">
        <flux:heading size="lg">{{ $previewName }}</flux:heading>
        <flux:text class="mt-1">{{ __('Scan to test the QR code.') }}</flux:text>
        <div class="mt-4 flex justify-center rounded-xl bg-white p-6">
            @if($previewSvg)
                {!! $previewSvg !!}
            @endif
        </div>
        <div class="mt-4 flex justify-end">
            <flux:button variant="ghost" wire:click="$set('showPreviewModal', false)">{{ __('Close') }}</flux:button>
        </div>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal wire:model="showDeleteModal" class="w-full max-w-sm">
        <flux:heading size="lg">{{ __('Delete QR Code?') }}</flux:heading>
        <flux:text class="mt-2">{{ __('This cannot be undone. The QR code will stop working immediately.') }}</flux:text>
        <div class="mt-6 flex justify-end gap-3">
            <flux:button variant="ghost" wire:click="$set('showDeleteModal', false)">{{ __('Cancel') }}</flux:button>
            <flux:button variant="danger" wire:click="deleteQrCode" wire:loading.attr="disabled">{{ __('Delete') }}</flux:button>
        </div>
    </flux:modal>

</div>
