@if ($paginator->hasPages())
    <div class="mt-16 flex justify-center gap-2">

        {{-- Prev --}}
        @if ($paginator->onFirstPage())
            <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-low opacity-50 cursor-not-allowed">
                &lt;
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-low hover:bg-gray-200 transition">
                &lt;
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)

            {{-- "..." --}}
            @if (is_string($element))
                <span class="px-2 flex items-center">{{ $element }}</span>
            @endif

            {{-- Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)

                    @if ($page == $paginator->currentPage())
                        <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-primary text-white font-semibold">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-low hover:bg-gray-200 transition">
                            {{ $page }}
                        </a>
                    @endif

                @endforeach
            @endif

        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-low hover:bg-gray-200 transition">
                &gt;
            </a>
        @else
            <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-low opacity-50 cursor-not-allowed">
                &gt;
            </span>
        @endif

    </div>
@endif