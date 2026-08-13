<div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-width-md">
        {{-- Brand / App Logo & Title --}}
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-600 text-white shadow-md mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-800">
                Masuk ke Cashflow Tracker
            </h2>
            <p class="mt-1 text-xs text-slate-500">
                Kelola pemasukan, pengeluaran, dan sinking fund Anda secara aman.
            </p>
        </div>

        {{-- Form Card --}}
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-6 shadow-xs border border-slate-100 rounded-3xl sm:px-10">
                <form wire:submit="login" class="space-y-5">
                    
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
                            autofocus 
                            placeholder="nama@email.com"
                            class="w-full text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder:text-slate-400"
                        />
                        @error('email') 
                            <span class="text-xs text-rose-500 mt-1.5 block font-medium">{{ $message }}</span> 
                        @enderror
                    </div>

                    {{-- Input Password --}}
                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">
                            Kata Sandi
                        </label>
                        <input 
                            wire:model="password" 
                            type="password" 
                            id="password" 
                            required 
                            placeholder="••••••••"
                            class="w-full text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder:text-slate-400"
                        />
                        @error('password') 
                            <span class="text-xs text-rose-500 mt-1.5 block font-medium">{{ $message }}</span> 
                        @enderror
                    </div>

                    {{-- Remember Me Checkbox --}}
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input 
                                wire:model="remember" 
                                type="checkbox" 
                                class="w-4 h-4 text-emerald-600 bg-slate-50 border-slate-300 rounded-md focus:ring-emerald-500/20 focus:ring-2 transition-all cursor-pointer"
                            />
                            <span class="text-xs font-medium text-slate-600">Ingat Saya</span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <div>
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="w-full py-3.5 px-4 text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 rounded-xl transition-all shadow-md active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            {{-- Loading Indicator --}}
                            <svg wire:loading class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            
                            <span>Masuk Aplikasi</span>
                        </button>
                    </div>

                    <div class="mt-6 text-center border-t border-slate-100 pt-5">
                      <p class="text-xs text-slate-500">
                          Belum memiliki akun? 
                          <a href="{{ route('register') }}" wire:navigate class="font-semibold text-emerald-600 hover:text-emerald-700">
                              Daftar Akun Baru
                          </a>
                      </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>