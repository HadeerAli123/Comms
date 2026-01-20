{{-- resources/views/vendor/pagination/custom.blade.php --}}

@if ($paginator->hasPages())
    <nav class="d-flex justify-content-center" role="navigation" aria-label="Pagination Navigation">
        <div class="pagination-container">
            <ul class="custom-pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link-arrow">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a href="{{ $paginator->previousPageUrl() }}" class="page-link-arrow" rel="prev">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled">
                            <span class="page-link-dots">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active">
                                    <span class="page-link-active">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a href="{{ $url }}" class="page-link-number">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a href="{{ $paginator->nextPageUrl() }}" class="page-link-arrow" rel="next">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif

<style>
/* Custom Pagination Styles */
.pagination-container {
    background: #ffffff;
    padding: 20px 30px;
    border-radius: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    display: inline-block;
}

.custom-pagination {
    display: flex;
    align-items: center;
    gap: 15px;
    list-style: none;
    margin: 0;
    padding: 0;
}

.custom-pagination .page-item {
    display: inline-block;
}

/* الأرقام العادية */
.page-link-number {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 12px;
    font-size: 20px;
    font-weight: 500;
    color: #dc3545;
    text-decoration: none;
    transition: all 0.3s ease;
    background: transparent;
}

.page-link-number:hover {
    background: #f8f9fa;
    color: #dc3545;
    transform: translateY(-2px);
}

/* الرقم النشط */
.page-link-active {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border-radius: 18px;
    font-size: 32px;
    font-weight: 600;
    color: #ffffff;
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
    cursor: default;
}

/* الأسهم */
.page-link-arrow {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border-radius: 12px;
    font-size: 18px;
    color: #333;
    text-decoration: none;
    transition: all 0.3s ease;
    background: transparent;
}

.page-link-arrow:hover {
    background: #f8f9fa;
    color: #dc3545;
    transform: translateY(-2px);
}

.page-item.disabled .page-link-arrow {
    color: #ccc;
    cursor: not-allowed;
}

.page-item.disabled .page-link-arrow:hover {
    background: transparent;
    transform: none;
}

/* النقاط ... */
.page-link-dots {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    font-size: 24px;
    font-weight: 600;
    color: #999;
    letter-spacing: 2px;
}

/* Responsive */
@media (max-width: 768px) {
    .pagination-container {
        padding: 15px 20px;
        border-radius: 15px;
    }
    
    .custom-pagination {
        gap: 8px;
    }
    
    .page-link-number {
        width: 40px;
        height: 40px;
        font-size: 16px;
        border-radius: 10px;
    }
    
    .page-link-active {
        width: 50px;
        height: 50px;
        font-size: 24px;
        border-radius: 14px;
    }
    
    .page-link-arrow {
        width: 40px;
        height: 40px;
        font-size: 14px;
        border-radius: 10px;
    }
    
    .page-link-dots {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }
}

@media (max-width: 576px) {
    .custom-pagination {
        gap: 5px;
    }
    
    .page-link-number {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }
    
    .page-link-active {
        width: 45px;
        height: 45px;
        font-size: 20px;
    }
    
    .page-link-arrow {
        width: 35px;
        height: 35px;
        font-size: 12px;
    }
}
</style>