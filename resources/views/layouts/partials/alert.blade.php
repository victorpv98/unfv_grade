@php
    $message = session('success') ?? session('error') ?? session('warning') ?? session('info');
    $type = session('success') ? 'success'
        : (session('error') ? 'danger'
        : (session('warning') ? 'warning'
        : (session('info') ? 'info' : null)));
@endphp

@if($message && $type)
    <div class="alert alert-{{ $type }} alert-dismissible fade show mb-4 shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <span class="me-3">
                @switch($type)
                    @case('success')
                        <i class="fa-solid fa-circle-check text-success"></i>
                        @break
                    @case('danger')
                        <i class="fa-solid fa-circle-xmark text-danger"></i>
                        @break
                    @case('warning')
                        <i class="fa-solid fa-triangle-exclamation text-warning"></i>
                        @break
                    @default
                        <i class="fa-solid fa-circle-info text-info"></i>
                @endswitch
            </span>
            <div class="flex-grow-1 text-secondary">
                {{ $message }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    </div>
@endif
