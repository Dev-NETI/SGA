<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-r from-sgaBlue to-sgaDarkerBlue">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-stone-200 shadow-md overflow-hidden sm:rounded-lg">
        {{ $slot }}
    </div>
</div>
