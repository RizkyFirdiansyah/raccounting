<div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        {{-- Brand / App Logo & Title --}}
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-600 text-white shadow-md mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-800">
                Buat Akun Baru
            </h2>
            <p class="mt-1 text-xs text-slate-500">
                Mulai kelola keuangan dan sinking fund Anda secara cerdas.
            </p>
        </div>

        {{-- Form Card --}}
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-6 shadow-xs border border-slate-100 rounded-3xl sm:px-10">
                <form wire:submit="register" class="space-y-4">
                    
                    {{-- Input Nama Lengkap --}}
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">
                            Nama Lengkap
                        </label>
                        <input 
                            wire:model="name" 
                            type="text" 
                            id="name" 
                            required 
                            autofocus 
                            placeholder="Ahmad Rizky"
                            class="w-full text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder:text-slate-400"
                        />
                        @error('name') 
                            <span class="text-xs text-rose-500 mt-1.5 block font-medium">{{ $message }}</span> 
                        @enderror
                    </div>

                    {{-- Input Email --}}
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">
                            Alamat Email
                        </label>
                        <input 
                            wire:model="email" 
                            type="email" 
                            id="email" 
                            required 
                            placeholder="nama@email.com"
                            class="w-full text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder:text-slate-400"
                        />
                        @error('email') 
                            <span class="text-xs text-rose-500 mt-1.5 block font-medium">{{ $message }}</span> 
                        @enderror
                    </div>

                    {{-- Input Kata Sandi --}}
                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">
                            Kata Sandi
                        </label>
                        <input 
                            wire:model="password" 
                            type="password" 
                            id="password" 
                            required 
                            placeholder="Minimal 8 karakter"
                            class="w-full text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder:text-slate-400"
                        />
                        @error('password') 
                            <span class="text-xs text-rose-500 mt-1.5 block font-medium">{{ $message }}</span> 
                        @enderror
                    </div>

                    {{-- Konfirmasi Kata Sandi --}}
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">
                            Konfirmasi Kata Sandi
                        </label>
                        <input 
                            wire:model="password_confirmation" 
                            type="password" 
                            id="password_confirmation" 
                            required 
                            placeholder="Ulangi kata sandi"
                            class="w-full text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder:text-slate-400"
                        />
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="w-full py-3.5 px-4 text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 rounded-xl transition-all shadow-md active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <svg wire:loading class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            
                            <span>Daftar Sekarang</span>
                        </button>
                    </div>

                </form>

                {{-- Link ke Halaman Login --}}
                <div class="mt-6 text-center border-t border-slate-100 pt-5">
                    <p class="text-xs text-slate-500">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" wire:navigate class="font-semibold text-emerald-600 hover:text-emerald-700">
                            Masuk di sini
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>