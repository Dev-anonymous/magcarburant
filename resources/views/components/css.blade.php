<style>
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 16px;
    }

    ::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
    }

    * {
        -ms-overflow-style: 8px;
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.05);
    }
</style>
<link type="text/css" href="{{ asset('assets/css/app.css') }}" rel="stylesheet" />
<link type="text/css" href="{{ asset('assets/css/app.rtl.css') }}" rel="stylesheet" />
<link type="text/css" href="{{ asset('assets/vendor/simplebar.css') }}" rel="stylesheet" />
<link type="text/css" href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" />
<script>
    let lastError = null;

    function sendError(errorData) {
        const payload = safeStringify(errorData);

        if (payload === lastError) return;
        lastError = payload;

        fetch('{{ route('log-error') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: payload
        }).catch(() => {
            // éviter boucle
        });
    }

    function safeStringify(obj) {
        const seen = new WeakSet();
        return JSON.stringify(obj, function(key, value) {
            if (typeof value === "object" && value !== null) {
                if (seen.has(value)) {
                    return '[Circular]';
                }
                seen.add(value);
            }
            return value;
        });
    }

    window.onerror = function(message, source, lineno, colno, error) {
        if (typeof source === 'string' && source.includes('/log-error')) return;
        if (typeof message === 'string' && message.includes('Script error')) return;
        const errorData = {
            type: 'error',
            message,
            source,
            line: lineno,
            column: colno,
            stack: error?.stack || null,
            url: window.location.href,
            time: new Date().toISOString()
        };
        sendError(errorData);
    };

    window.addEventListener('unhandledrejection', function(event) {
        const errorData = {
            type: 'unhandledrejection',
            message: event.reason?.message || String(event.reason),
            stack: event.reason?.stack || null,
            url: window.location.href,
            userAgent: navigator.userAgent,
            time: new Date().toISOString()
        };
        sendError(errorData);
    });
</script>
