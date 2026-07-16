@php
    $flashConfig = [
        'success' => ['icon' => 'fa-check-circle', 'class' => 'alert-success'],
        'error'   => ['icon' => 'fa-times-circle', 'class' => 'alert-danger'],
        'info'    => ['icon' => 'fa-info-circle', 'class' => 'alert-info'],
        'warning' => ['icon' => 'fa-exclamation-triangle', 'class' => 'alert-warning'],
    ];
@endphp

<div class="flash-messages" aria-live="polite">
    @foreach ($flashConfig as $key => $cfg)
        @if(session($key))
            <div class="alert {{ $cfg['class'] }} alert-dismissible fade show d-flex align-items-start gap-2 auto-dismiss" role="alert" data-autodismiss="5000">
                <i class="fas {{ $cfg['icon'] }} mt-1"></i>
                <div class="flex-grow-1">{{ session($key) }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <span class="alert-progress" aria-hidden="true"></span>
            </div>
        @endif
    @endforeach

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start gap-2" role="alert">
            <i class="fas fa-circle-exclamation mt-1"></i>
            <div class="flex-grow-1">
                <h6 class="alert-heading fw-bold mb-1">Please correct the following errors:</h6>
                <ul class="mb-0 ps-3">
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
