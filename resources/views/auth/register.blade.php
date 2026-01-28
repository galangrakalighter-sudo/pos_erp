@extends('layouts.app')

@section('content')
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;700&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Urbanist', sans-serif; }
</style>
@endpush

<div class="min-h-screen flex flex-col md:flex-row" x-data="clientValidation()">
    <!-- Left: Branding & Marquee (reuse from login) -->
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

    <!-- Right: Client ID Validation & Register Form -->
    <div class="md:w-1/3 w-full flex flex-col justify-center px-8" style="min-height: 380px;">
        <div class="w-full max-w-md mx-auto bg-white rounded-2xl shadow-lg p-8">
            <div class="text-center mb-6">
                <div class="text-8xl font-extrabold text-gray-900 mb-1">GAFI</div>
                <div class="text-xs font-medium text-gray-500">GOLDEN AROMA FOOD INDONESIA</div>
            </div>
            
            <!-- Step 1: Client ID Validation -->
            <div x-show="!clientValidated" x-transition>
                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Verifikasi Client ID</h2>
                    <p class="text-gray-600 text-sm">Masukkan Client ID yang telah diberikan untuk melanjutkan registrasi</p>
                </div>
                
                <div x-data="{ loading: false, errorMessage: '' }" class="space-y-4">
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700">Client ID</label>
                        <input 
                            id="client_id" 
                            type="text" 
                            x-model="clientId" 
                            placeholder="Masukkan Client ID" 
                            class="mt-1 block w-full rounded-lg border border-gray-300 focus:border-[#28C328] focus:ring-[#28C328] shadow-sm"
                            :disabled="loading"
                        >
                    </div>
                    
                    <div x-show="errorMessage" class="text-sm text-red-600 text-center" x-text="errorMessage"></div>
                    
                    <button 
                        @click="validateClientId()" 
                        :disabled="!clientId.trim() || loading"
                        class="w-full py-2 px-4 bg-[#28C328] text-white font-bold rounded-lg hover:bg-green-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span x-show="!loading">Verifikasi Client ID</span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memverifikasi...
                        </span>
                    </button>
                </div>
            </div>
            
            <!-- Step 2: Registration Form (only shown after client validation) -->
            <div x-show="clientValidated" x-transition>
                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Registrasi Akun</h2>
                    <p class="text-gray-600 text-sm">Client ID: <span class="font-semibold text-[#28C328]" x-text="validatedClient.client_id"></span></p>
                </div>
                
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                    <!-- Hidden input untuk client_id yang sudah divalidasi -->
                    <input type="hidden" name="client_id" x-bind:value="validatedClient.client_id">
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input id="name" type="text" name="name" x-model="userName" required autofocus autocomplete="name" placeholder="Masukkan nama lengkap Anda" class="mt-1 block w-full rounded-lg border border-gray-300 focus:border-[#28C328] focus:ring-[#28C328] shadow-sm">
                        <p class="text-xs text-gray-500 mt-1">Nama akan disinkronkan dengan data client</p>
                        @error('name')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Contoh: gafi@gmail.com" class="mt-1 block w-full rounded-lg border border-gray-300 focus:border-[#28C328] focus:ring-[#28C328] shadow-sm">
                        @error('email')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div x-data="{ show: false }">
                        <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" class="mt-1 block w-full rounded-lg border border-gray-300 focus:border-[#28C328] focus:ring-[#28C328] shadow-sm pr-10">
                            <button type="button" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400" @click="show = !show" tabindex="-1">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 014.043-5.306M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                        @error('password')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Kata Sandi</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi" class="mt-1 block w-full rounded-lg border border-gray-300 focus:border-[#28C328] focus:ring-[#28C328] shadow-sm">
                        @error('password_confirmation')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" @click="resetValidation()" class="flex-1 py-2 px-4 bg-gray-200 text-gray-700 font-bold rounded-lg hover:bg-gray-300 transition">
                            Kembali
                        </button>
                        <button type="submit" class="flex-1 py-2 px-4 bg-[#28C328] text-white font-bold rounded-lg hover:bg-green-600 transition">
                            Daftar
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="mt-6 text-center text-sm text-gray-600">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-[#28C328] font-semibold hover:underline">Masuk</a>
            </div>
        </div>
        <div class="mt-8 text-center text-gray-400 text-xs">2025 Golden Aroma Food Indonesia GAFI</div>
    </div>
</div>

@push('scripts')
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
        speed: 1.2,
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
        stop() { cancelAnimationFrame(this.raf); }
    }
}

// Client ID validation logic
window.clientValidation = function() {
    return {
        clientId: '',
        clientValidated: false,
        validatedClient: null,
        userName: '', // Added userName property
        
        async validateClientId() {
            if (!this.clientId.trim()) return;
            
            this.loading = true;
            this.errorMessage = '';
            
            try {
                const response = await fetch('/validate-client-id', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        client_id: this.clientId.trim()
                    })
                });
                
                const data = await response.json();
                
                if (data.valid) {
                    this.validatedClient = data.client;
                    this.clientValidated = true;
                    this.userName = this.validatedClient.name || ''; // Set userName from validated client
                } else {
                    this.errorMessage = data.message || 'Client ID tidak valid';
                }
            } catch (error) {
                this.errorMessage = 'Terjadi kesalahan saat memverifikasi Client ID';
            } finally {
                this.loading = false;
            }
        },
        
        resetValidation() {
            this.clientId = '';
            this.clientValidated = false;
            this.validatedClient = null;
            this.userName = ''; // Reset userName
            this.errorMessage = '';
        }
    }
}
</script>
@endsection
