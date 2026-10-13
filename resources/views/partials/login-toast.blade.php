@php
    $toasts = [];

    foreach (['success', 'warning', 'error'] as $type) {
        if (session($type)) {
            $toasts[] = ['type' => $type, 'msg' => session($type)];
        }
    }

    foreach ($errors->all() as $e) {
        $toasts[] = ['type' => 'error', 'msg' => $e];
    }
@endphp

<div class="toast-stack toast-top" id="toastStack" aria-live="polite"></div>

<script>
    window.__toasts = {{ Js::from($toasts) }};
</script>