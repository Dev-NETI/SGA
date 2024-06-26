<tr class="border-b border-gray-200 dark:border-gray-700">
    <x-td>{{ $recipient->name }}</x-td>
    <x-td>{{ $recipient->principal->name }}</x-td>
    <x-td>{{ $recipient->department }}</x-td>
    <x-td>{{ $recipient->position }}</x-td>
    <x-td>{{ $recipient->modified_by }}</x-td>
    <x-td>{{ $recipient->updated_at }}</x-td>
    <x-td>
        
        <x-action-dropdown :id="$recipient->id">
            <x-action-dropdown-item label="Edit"
                href="{{ route('recipient.edit', ['hash_id' => $recipient->hash]) }}" />
            <x-action-dropdown-item label="Delete" wire:confirm="Are you sure you want to delete recipient?"
                wire:click="destroy({{ $recipient->id }})" />
        </x-action-dropdown>

    </x-td>
</tr>
