@props(['hash', 'data', 'name', 'title'])
<div class="relative z-0 w-full mb-5 group">

    <select name="{{ $title }}" id="{{ $title }}" {{ $attributes }}
        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-sgaDarkBlue 
                appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 
                focus:sgaDarkerBlue peer"
        placeholder=" ">
        @if ($hash == null)
            <option value="">Select</option>
        @else
        @endif
        @if ($data != null)
            @foreach ($data as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        @endif
    </select>

    <label for="{{ $title }}"
        class="peer-focus:font-medium absolute text-sm text-sgaFontBlue dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 
        origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-sgaDarkerBlue  
        peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 
        peer-focus:-translate-y-6">
        {{ $title }}
    </label>

    @error($name)
        <livewire:components.reusable.error-span-red message="{{ $message }}" />
    @enderror

</div>
