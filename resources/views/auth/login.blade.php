@extends('layouts.app')

@section('content')
@push('styles')

<link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;700&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Urbanist', sans-serif; }
</style>
@endpush

<div class="min-h-screen flex flex-col md:flex-row">
    <!-- Left Side: Branding & Images Marquee -->
    <div class="md:w-2/3 w-full bg-[#28C328] flex flex-col">
        <div class="flex items-center" style="height: 550px;">
            <div class="relative w-full h-full overflow-hidden pt-24" x-data="marqueeSlider()" x-init="start()">
                <div class="flex h-full" :style="`transform: translateX(-${offset}px); transition: transform ${animating ? '1s linear' : '0s'};`" @transitionend="onTransitionEnd">
                    <template x-for="(img, idx) in images" :key="idx">
                        <img :src="img" class="h-full object-cover flex-shrink-0 mx-2 rounded-lg" :style="`width: ${imgWidth}px`" alt="Slider Image">
                    </template>
                </div>
            </div>
        </div>
        <div class="px-12 mt-8">
            <div class="flex flex-col md:flex-row md:items-start md:gap-8">
                <div class="md:w-1/2 w-full text-white text-4xl font-extrabold uppercase leading-tight pl-12">GOLDEN AROMA<br>FOOD INDONESIA</div>
                <div class="md:w-1/2 w-full border-l-2 border-white pl-32 mt-6 md:mt-0">
                    <span class="text-white text-lg font-semibold"><span class="text-[#28C328] font-bold bg-white px-1 rounded">Mitra Terpercaya</span> untuk<br>Solusi Bumbu dan Rasa<br>Berkualitas Industri</span>
                </div>
            </div>
        </div>
    </div>
    <!-- Right Side: Login Form -->
    <div class="md:w-1/3 w-full flex flex-col justify-center px-8" style="min-height: 380px;">
        <div class="w-full max-w-md mx-auto bg-white rounded-2xl shadow-lg p-8">
            <div class="text-center mb-6">
                <div class="text-8xl font-extrabold text-gray-900 mb-1">GAFI</div>
                <div class="text-xs font-medium text-gray-500">GOLDEN AROMA FOOD INDONESIA</div>
            </div>
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                
                <!-- Pesan Error -->
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Pesan Error dari Session -->
                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Contoh: gafi@gmail.com" class="mt-1 block w-full rounded-lg border border-gray-300 focus:border-[#28C328] focus:ring-[#28C328] shadow-sm @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" id="password" name="password" required placeholder="Contoh: Sandi123" class="mt-1 block w-full rounded-lg border border-gray-300 focus:border-[#28C328] focus:ring-[#28C328] shadow-sm pr-10 @error('password') border-red-500 @enderror">
                        <button type="button" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400" @click="show = !show" tabindex="-1">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95m3.161-2.522A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.956 9.956 0 01-4.043 5.306M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center justify-between">
                    <a href="{{ route('password.request') }}" class="text-sm text-[#28C328] hover:underline">Lupa Kata Sandi?</a>
                </div>
                <button type="submit" class="w-full py-2 px-4 bg-[#28C328] text-white font-bold rounded-lg hover:bg-green-600 transition">Masuk</button>
            </form>
            <div class="mt-6 text-center text-sm text-gray-600">
                Belum punya akun gafi? <a href="{{ route('register') }}" class="text-[#28C328] font-semibold hover:underline">Daftar</a>
            </div>
        </div>
        <div class="mt-8 text-center text-gray-400 text-xs">2025 Golden Aroma Food Indonesia GAFI</div>
    </div>
</div>

@push('scripts')
<script>
// Handle CSRF token expired untuk AJAX requests
document.addEventListener('DOMContentLoaded', function() {
    // Setup AJAX untuk menangani CSRF token expired
    if (typeof $ !== 'undefined') {
        $(document).ajaxError(function(event, xhr, settings) {
            if (xhr.status === 419) {
                // CSRF token expired
                alert('Sesi Anda telah berakhir. Silakan login kembali.');
                window.location.href = '{{ route("login") }}';
            }
        });
    }
    
    // Setup fetch untuk menangani CSRF token expired
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        return originalFetch.apply(this, args).then(response => {
            if (response.status === 419) {
                alert('Sesi Anda telah berakhir. Silakan login kembali.');
                window.location.href = '{{ route("login") }}';
                return Promise.reject(new Error('CSRF token expired'));
            }
            return response;
        });
    };
});
</script>
@endpush
<script>
window.marqueeSlider = function() {
    return {
        baseImages: [
            @json(asset('images/spices.png')),
            @json(asset('images/bowl.png')),
            @json(asset('images/chef.png')),
        ],
        images: [],
        offset: 0,
        imgWidth: 450,
        speed: 1.2, // px per frame
        animating: true,
        raf: null,
        start() {
            this.images = [...this.baseImages, ...this.baseImages];
            this.offset = 0;
            this.animating = true;
            this.animate();
        },
        animate() {
            this.offset += this.speed;
            if (this.offset >= this.imgWidth * this.baseImages.length) {
                this.offset = 0;
            }
            this.raf = requestAnimationFrame(() => this.animate());
        },
        stop() {
            cancelAnimationFrame(this.raf);
        }
    }
}
</script>
@endsection 