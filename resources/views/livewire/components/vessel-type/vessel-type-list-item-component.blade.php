<tr class="border-b border-gray-200 dark:border-gray-700">
    <livewire:components.reusable.td label="{{ $vessel->name }}" />
    <td class="px-6 py-4">

        <button id="dropdownDefaultButton" data-dropdown-toggle="dropdown{{ $vessel->id }}"
            class="px-3 py-2 text-xs font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 
            focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            type="button">
            . . .
        </button>

        <!-- Dropdown menu -->
        <div id="dropdown{{ $vessel->id }}"
            class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                <li>
                    <a href="{{ route('vessel-type.edit', ['hash_id' => $vessel->hash]) }}"
                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                        wire:navigate>Edit</a>
                </li>
                <li>
                    <a wire:confirm="Are you sure you want to delete vessel type?"
                        wire:click="destroy({{ $vessel->id }})"
                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Delete</a>
                </li>
            </ul>
        </div>

    </td>
</tr>