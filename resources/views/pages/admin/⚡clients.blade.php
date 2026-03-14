<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Manage Clients')] class extends Component {
    use WithPagination;

    public string $search = '';

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $passwordConfirmation = '';

    public ?int $editingClientId = null;
    public ?int $deletingClientId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function clients(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return User::where('role', 'client')
            ->withCount('qrCodes')
            ->when($this->search, fn ($query) => $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->latest()
            ->paginate(15);
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal(int $clientId): void
    {
        $client = User::where('role', 'client')->findOrFail($clientId);

        $this->editingClientId = $client->id;
        $this->name = $client->name;
        $this->email = $client->email;
        $this->password = '';
        $this->passwordConfirmation = '';
        $this->showEditModal = true;
    }

    public function openDeleteModal(int $clientId): void
    {
        User::where('role', 'client')->findOrFail($clientId);
        $this->deletingClientId = $clientId;
        $this->showDeleteModal = true;
    }

    public function createClient(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'same:passwordConfirmation'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'client',
        ]);

        $this->showCreateModal = false;
        $this->resetForm();
        unset($this->clients);
    }

    public function updateClient(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', "unique:users,email,{$this->editingClientId}"],
            'password' => ['nullable', 'string', 'min:8', 'same:passwordConfirmation'],
        ]);

        $client = User::where('role', 'client')->findOrFail($this->editingClientId);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $client->update($updateData);

        $this->showEditModal = false;
        $this->resetForm();
        unset($this->clients);
    }

    public function deleteClient(): void
    {
        $client = User::where('role', 'client')->findOrFail($this->deletingClientId);
        $client->delete();

        $this->showDeleteModal = false;
        $this->deletingClientId = null;
        unset($this->clients);
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->passwordConfirmation = '';
        $this->editingClientId = null;
        $this->resetValidation();
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Clients') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Manage client accounts and their access') }}</flux:text>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="openCreateModal">
            {{ __('Add Client') }}
        </flux:button>
    </div>

    {{-- Search --}}
    <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="{{ __('Search by name or email...') }}"
        icon="magnifying-glass"
        clearable
    />

    {{-- Table --}}
    <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <table class="w-full text-sm">
            <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">{{ __('Name') }}</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">{{ __('Email') }}</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">{{ __('QR Codes') }}</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">{{ __('Joined') }}</th>
                    <th class="px-4 py-3 text-right font-medium text-neutral-600 dark:text-neutral-300">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                @forelse($this->clients as $client)
                    <tr class="bg-white dark:bg-neutral-900" wire:key="client-{{ $client->id }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <flux:avatar :name="$client->name" :initials="$client->initials()" size="sm" />
                                <span class="font-medium">{{ $client->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-neutral-500">{{ $client->email }}</td>
                        <td class="px-4 py-3 text-neutral-500">{{ $client->qr_codes_count }}</td>
                        <td class="px-4 py-3 text-neutral-500">{{ $client->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <flux:tooltip content="{{ __('Edit') }}">
                                    <flux:button size="sm" icon="pencil" variant="ghost" wire:click="openEditModal({{ $client->id }})" />
                                </flux:tooltip>
                                <flux:tooltip content="{{ __('Delete') }}">
                                    <flux:button size="sm" icon="trash" variant="ghost" wire:click="openDeleteModal({{ $client->id }})" class="text-red-500 hover:text-red-600" />
                                </flux:tooltip>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-16 text-center">
                            <flux:icon name="users" class="mx-auto mb-3 size-12 text-neutral-300 dark:text-neutral-600" />
                            <flux:text class="text-neutral-400">{{ __('No clients yet. Add your first client!') }}</flux:text>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($this->clients->hasPages())
        <div>{{ $this->clients->links() }}</div>
    @endif

    {{-- Create Client Modal --}}
    <flux:modal wire:model="showCreateModal" class="w-full max-w-lg">
        <flux:heading size="lg">{{ __('Add Client') }}</flux:heading>
        <flux:text class="mt-1">{{ __('Create login credentials to share with your client.') }}</flux:text>

        <form wire:submit="createClient" class="mt-6 space-y-4">
            <flux:field>
                <flux:label>{{ __('Full Name') }}</flux:label>
                <flux:input wire:model="name" placeholder="{{ __('Client Name') }}" autofocus />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Email Address') }}</flux:label>
                <flux:input wire:model="email" type="email" placeholder="client@example.com" />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Password') }}</flux:label>
                <flux:input wire:model="password" type="password" placeholder="{{ __('Min. 8 characters') }}" />
                <flux:error name="password" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Confirm Password') }}</flux:label>
                <flux:input wire:model="passwordConfirmation" type="password" placeholder="{{ __('Repeat password') }}" />
            </flux:field>

            <div class="flex justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showCreateModal', false)">{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">{{ __('Create Client') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Edit Client Modal --}}
    <flux:modal wire:model="showEditModal" class="w-full max-w-lg">
        <flux:heading size="lg">{{ __('Edit Client') }}</flux:heading>
        <flux:text class="mt-1">{{ __('Update client details. Leave password blank to keep the current one.') }}</flux:text>

        <form wire:submit="updateClient" class="mt-6 space-y-4">
            <flux:field>
                <flux:label>{{ __('Full Name') }}</flux:label>
                <flux:input wire:model="name" placeholder="{{ __('Client Name') }}" autofocus />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Email Address') }}</flux:label>
                <flux:input wire:model="email" type="email" placeholder="client@example.com" />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('New Password') }} <span class="text-xs text-neutral-400">({{ __('optional') }})</span></flux:label>
                <flux:input wire:model="password" type="password" placeholder="{{ __('Leave blank to keep current') }}" />
                <flux:error name="password" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Confirm New Password') }}</flux:label>
                <flux:input wire:model="passwordConfirmation" type="password" placeholder="{{ __('Repeat new password') }}" />
            </flux:field>

            <div class="flex justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showEditModal', false)">{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">{{ __('Save Changes') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal wire:model="showDeleteModal" class="w-full max-w-sm">
        <flux:heading size="lg">{{ __('Delete Client?') }}</flux:heading>
        <flux:text class="mt-2">{{ __('This will permanently delete the client and all their QR codes. This cannot be undone.') }}</flux:text>
        <div class="mt-6 flex justify-end gap-3">
            <flux:button variant="ghost" wire:click="$set('showDeleteModal', false)">{{ __('Cancel') }}</flux:button>
            <flux:button variant="danger" wire:click="deleteClient" wire:loading.attr="disabled">{{ __('Delete Client') }}</flux:button>
        </div>
    </flux:modal>

</div>
