<style>.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    list-style: none;
    padding: 10px;
}

.pagination-link {
    display: inline-block;
    padding: 10px 15px;
    margin: 0 5px;
    text-decoration: none;
    color: #1F1F1F;
    border: 1px solid #ddd !important;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

.pagination-link:hover {
    background-color: #f8f9fa;
}

.pagination-link.current {
    background-color: #1F1F1F;
    color: white;
    cursor: default;
}

.pagination-link.disabled {
    color: #ddd;
    cursor: not-allowed;
    border-color: #ddd;
}

.pagination-link i {
    font-size: 12px;
}

.pagination-link.disabled i {
    color: #ddd;
}
</style>
@props(['paginator'])

@php
    $queryString = http_build_query(request()->except($paginator->getPageName()));
@endphp

@if ($paginator->lastPage() > 1)
    <div class="pagination">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <div class="pagination-link disabled"><i class="fa-solid fa-angle-left"></i></div>
        @else
            <a href="{{ $paginator->previousPageUrl() . (strlen($queryString) ? '&'.$queryString : '') }}" class="pagination-link">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @endif

        {{-- Pages --}}
        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
            @php
                $url = $paginator->url($i) . (strlen($queryString) ? '&'.$queryString : '');
            @endphp

            @if ($i == 1 || $i == $paginator->lastPage() || ($i >= $paginator->currentPage() - 2 && $i <= $paginator->currentPage() + 2))
                {{-- عرض أول صفحة + آخر صفحة + 2 قبل و 2 بعد الصفحة الحالية --}}
                @if ($i == $paginator->currentPage())
                    <div class="pagination-link current">{{ $i }}</div>
                @else
                    <a href="{{ $url }}" class="pagination-link">{{ $i }}</a>
                @endif
            @elseif ($i == 2 || $i == $paginator->lastPage() - 1)
                {{-- عرض "..." مرة واحدة بعد أول صفحة أو قبل آخر صفحة --}}
                <div class="pagination-link disabled">...</div>
            @endif
        @endfor

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() . (strlen($queryString) ? '&'.$queryString : '') }}" class="pagination-link">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @else
            <div class="pagination-link disabled"><i class="fa-solid fa-angle-right"></i></div>
        @endif
    </div>
@endif
