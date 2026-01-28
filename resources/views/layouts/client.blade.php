<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Client Dashboard</title>
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">
    @php
    $isProduction = app()->environment('production');
    $manifestPath = $isProduction ? '../public_html/build/manifest.json' : public_path('build/manifest.json');
 @endphp
 
  @if ($isProduction && file_exists($manifestPath))
   @php
    $manifest = json_decode(file_get_contents($manifestPath), true);
   @endphp
    <link rel="stylesheet" href="{{ config('app.url') }}/build/{{ $manifest['resources/css/app.css']['file'] }}">
    <script type="module" src="{{ config('app.url') }}/build/{{ $manifest['resources/js/app.js']['file'] }}"></script>
  @else
    @viteReactRefresh
    @vite(['resources/js/app.js', 'resources/css/app.css'])
  @endif
    <!-- Global Session Handler -->
    <script>
    // Function untuk check dan handle session expired
    function handleSessionExpired() {
        // Redirect ke login
        window.location.href = '/login';
    }

    // Function untuk refresh CSRF token
    async function refreshCSRFToken() {
        try {
            const response = await fetch('/csrf-token', {
                method: 'GET',
                credentials: 'same-origin'
            });
            
            if (response.ok) {
                const data = await response.json();
                // Update meta tag
                document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.token);
                return data.token;
            }
        } catch (error) {
            console.log('Failed to refresh CSRF token');
        }
        return null;
    }

    // Global fetch interceptor untuk handle 401/403/419 responses
    const originalFetch = window.fetch;
    window.fetch = async function(...args) {
        const response = await originalFetch.apply(this, args);
        
        // Check jika response adalah 401 (Unauthorized), 403 (Forbidden), atau 419 (CSRF Token Mismatch)
        if (response.status === 401 || response.status === 403) {
            handleSessionExpired();
            return response;
        }
        
        // Handle CSRF token mismatch (419)
        if (response.status === 419) {
            // Coba refresh CSRF token
            const newToken = await refreshCSRFToken();
            if (newToken) {
                // Retry request dengan token baru jika request menggunakan POST/PUT/DELETE
                const [url, options] = args;
                if (options && options.method && options.method !== 'GET') {
                    if (options.headers) {
                        options.headers['X-CSRF-TOKEN'] = newToken;
                    }
                    // Retry request
                    return originalFetch.apply(this, args);
                }
            } else {
                // Jika gagal refresh token, redirect ke login
                handleSessionExpired();
            }
        }
        
        return response;
    };

    // Check session status saat halaman load
    document.addEventListener('DOMContentLoaded', function() {
        // Check apakah user masih login dengan request sederhana
        fetch('/check-session', {
            method: 'GET',
            credentials: 'same-origin'
        }).then(response => {
            if (response.status === 401 || response.status === 403) {
                handleSessionExpired();
            }
        }).catch(() => {
            // Jika network error, tidak perlu redirect
        });
    });

    // Auto refresh CSRF token setiap 30 menit
    setInterval(async function() {
        await refreshCSRFToken();
    }, 30 * 60 * 1000); // 30 menit
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Library untuk Export Excel dan PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-[#F3FFF3] font-sans">
    <div class="flex min-h-screen">
        @include('components.client.sidebar')
        <div class="flex-1 flex flex-col">
            @include('components.client.topbar')
            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>

     <!-- Help System -->
     @include('components.help-system')
     
</body>
</html> 