@if ($paginator->hasPages())
    <div class="mt-5 pt-4 border-t border-[#F3F4F6] flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[#6B7280]">
        <p class="tabular-nums">
            Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
        </p>

        <nav class="inline-flex items-center gap-1.5" aria-label="Navigasi halaman">
            @if ($paginator->onFirstPage())
                <span class="rounded-full border border-[#E5E7EB] px-3 py-1.5 text-[#9CA3AF] cursor-not-allowed">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="rounded-full border border-[#D1D5DB] px-3 py-1.5 text-[#111827] hover:border-[#0F0F10] transition-colors">
                    Sebelumnya
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 py-1 text-[#9CA3AF]">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-8 h-8 rounded-full bg-[#0F0F10] text-white font-semibold inline-flex items-center justify-center tabular-nums">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="w-8 h-8 rounded-full border border-[#E5E7EB] text-[#111827] hover:border-[#0F0F10] inline-flex items-center justify-center tabular-nums transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="rounded-full border border-[#D1D5DB] px-3 py-1.5 text-[#111827] hover:border-[#0F0F10] transition-colors">
                    Berikutnya
                </a>
            @else
                <span class="rounded-full border border-[#E5E7EB] px-3 py-1.5 text-[#9CA3AF] cursor-not-allowed">
                    Berikutnya
                </span>
            @endif
        </nav>
    </div>
@endif
