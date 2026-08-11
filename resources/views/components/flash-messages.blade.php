@php
    $flashConfig = [
        'success' => [
            'icon' => 'fa-check-circle',
            'class' => 'alert-success',
            'border' => 'border-start-4',
            'bg' => 'bg-success-subtle',
        ],
        'error'   => [
            'icon' => 'fa-circle-exclamation',
            'class' => 'alert-danger',
            'border' => 'border-start-4',
            'bg' => 'bg-danger-subtle',
        ],
        'info'    => [
            'icon' => 'fa-circle-info',
            'class' => 'alert-info',
            'border' => 'border-start-4',
            'bg' => 'bg-info-subtle',
        ],
        'warning' => [
            'icon' => 'fa-triangle-exclamation',
            'class' => 'alert-warning',
            'border' => 'border-start-4',
            'bg' => 'bg-warning-subtle',
        ],
    ];
@endphp

<div class="flash-messages" aria-live="polite">
    @foreach ($flashConfig as $key => $cfg)
        @if(session($key))
            <div class="alert {{ $cfg['class'] }} {{ $cfg['border'] }} {{ $cfg['bg'] }} alert-dismissible fade show d-flex align-items-start gap-3 auto-dismiss border-0 shadow-sm"
                 role="alert"
                 data-autodismiss="5000"
                 style="border-left: 4px solid;">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-25"
                     style="width: 36px; height: 36px; flex-shrink: 0;">
                    <i class="fas {{ $cfg['icon'] }}"></i>
                </div>
                <div class="flex-grow-1 lh-sm">{{ session($key) }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <span class="alert-progress" aria-hidden="true"></span>
            </div>
        @endif
    @endforeach

    @if ($errors->any())
        <div class="alert alert-danger border-start-4 bg-danger-subtle alert-dismissible fade show d-flex align-items-start gap-3 border-0 shadow-sm"
             role="alert"
             style="border-left: 4px solid #dc2626;">
            <div class="d-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10"
                 style="width: 36px; height: 36px; flex-shrink: 0;">
                <i class="fas fa-circle-exclamation text-danger"></i>
            </div>
            <div class="flex-grow-1 lh-sm">
                <h6 class="alert-heading fw-bold mb-1">Please correct the following errors:</h6>
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.alert.auto-dismiss').forEach(function (alert) {
            const ms = parseInt(alert.dataset.autodismiss || '5000', 10);
            setTimeout(function () {
                const btn = alert.querySelector('.btn-close');
                if (btn && typeof bootstrap !== 'undefined') {
                    bootstrap.Alert.getOrCreateInstance(btn).close();
                } else if (btn) {
                    btn.click();
                }
            }, ms);
        });
    });
</script>
