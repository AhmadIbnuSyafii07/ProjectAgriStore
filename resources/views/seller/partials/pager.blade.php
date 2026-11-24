@if ($paginator->hasPages())
    <div class="pager">
        @if ($paginator->onFirstPage())
            <span class="btn btn-sm btn-outline is-disabled">Sebelumnya</span>
        @else
            <a class="btn btn-sm btn-outline" href="{{ $paginator->previousPageUrl() }}">Sebelumnya</a>
        @endif

        <span>Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a class="btn btn-sm btn-outline" href="{{ $paginator->nextPageUrl() }}">Berikutnya</a>
        @else
            <span class="btn btn-sm btn-outline is-disabled">Berikutnya</span>
        @endif
    </div>
@endif
