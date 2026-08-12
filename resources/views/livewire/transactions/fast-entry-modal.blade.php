<div>
    {{-- Floating Action Button (FAB) --}}
    <button 
        wire:click="openModal"
        type="button" 
        class="fixed bottom-6 right-6 z-40 flex items-center justify-center w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-full shadow-lg hover:shadow-xl transform active:scale-95 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-emerald-300"
        aria-label="Tambah Transaksi Cepat"
        id="fab-fast-entry"
    >
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
    </button>

    {{-- Flash Toast Notification --}}
    @if (session()->has('message'))
        <div 
            x-data="{ show: true }" 
            x-show="show" 
            x-init="setTimeout(() => show = false, 4000)"
            class="fixed top-5 right-5 z-50 flex items-center p-4 text-emerald-800 bg-emerald-100 border border-emerald-300 rounded-xl shadow-md transition-all"
            role="alert"
        >
            <svg class="w-5 h-5 me-2 fill-current" viewBox="0 0 20 20">
                <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm4.707 7.707l-5.5 5.5a1 1 0 01-1.414 0l-2.5-2.5a1 1 0 011.414-1.414L9 11.086l4.793-4.793a1 1 0 011.414 1.414z"/>
            </svg>
            <span class="text-sm font-medium">{{ session('message') }}</span>
        </div>
    @endif

    {{-- Modal Overlay & Slide-Up Drawer --}}
    @if($isOpen)
        <div 
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" 
            role="dialog" 
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div 
                wire:click="closeModal"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                aria-hidden="true"
            ></div>

            {{-- Modal Dialog Container --}}
            <div class="flex min-h-full items-end justify-center sm:items-center p-0 sm:p-4 text-center">
                <div 
                    class="relative w-full max-w-lg transform overflow-hidden rounded-t-3xl sm:rounded-2xl bg-white text-left shadow-2xl transition-all border border-slate-100"
                    x-data="{
                        amountRaw: @entangle('amount'),
                        formatRupiah(val) {
                            if (!val) return '';
                            let number_string = val.toString().replace(/[^,\d]/g, '');
                            let split = number_string.split(',');
                            let sisa = split[0].length % 3;
                            let rupiah = split[0].substr(0, sisa);
                            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                            if (ribuan) {
                                let separator = sisa ? '.' : '';
                                rupiah += separator + ribuan.join('.');
                            }
                            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
                            return rupiah ? 'Rp ' + rupiah : '';
                        },
                        onInput(e) {
                            let formatted = this.formatRupiah(e.target.value);
                            this.amountRaw = formatted;
                        }
                    }"
                >
                    {{-- Mobile Pull Handle --}}
                    <div class="sm:hidden w-full flex justify-center pt-3 pb-1">
                        <div class="w-12 h-1.5 bg-slate-300 rounded-full"></div>
                    </div>

                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 pt-4 pb-3 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800" id="modal-title">
                            Pencatatan Transaksi Cepat
                        </h3>
                        <button 
                            wire:click="closeModal" 
                            type="button" 
                            class="text-slate-400 hover:text-slate-600 p-2 rounded-full hover:bg-slate-100 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Form Body --}}
                    <form wire:submit.prevent="save" class="p-6 space-y-5 max-h-[80vh] overflow-y-auto">
                        {{-- Type Selector (Tabs) --}}
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">
                                Jenis Transaksi
                            </label>
                            <div class="grid grid-cols-3 gap-2 p-1 bg-slate-100 rounded-xl">
                                <button 
                                    type="button" 
                                    wire:click="$set('type', 'expense')"
                                    class="py-2.5 text-xs sm:text-sm font-semibold rounded-lg transition-all {{ $type === 'expense' ? 'bg-rose-500 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}"
                                >
                                    Pengeluaran
                                </button>
                                <button 
                                    type="button" 
                                    wire:click="$set('type', 'income')"
                                    class="py-2.5 text-xs sm:text-sm font-semibold rounded-lg transition-all {{ $type === 'income' ? 'bg-emerald-500 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}"
                                >
                                    Pemasukan
                                </button>
                                <button 
                                    type="button" 
                                    wire:click="$set('type', 'transfer')"
                                    class="py-2.5 text-xs sm:text-sm font-semibold rounded-lg transition-all {{ $type === 'transfer' ? 'bg-blue-500 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}"
                                >
                                    Transfer
                                </button>
                            </div>
                            @error('type') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Amount Input --}}
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
                                Nominal / Jumlah
                            </label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    x-bind:value="amountRaw"
                                    x-on:input="onInput($event)"
                                    placeholder="Rp 0"
                                    class="w-full text-2xl font-bold tracking-tight text-slate-900 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                                />
                            </div>
                            @error('amount') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Transaction Date --}}
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
                                Tanggal Transaksi
                            </label>
                            <input 
                                type="date" 
                                wire:model.live="transaction_date"
                                class="w-full text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                            />
                            @error('transaction_date') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Category & Cascading Item --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
                                    Kategori
                                </label>
                                <select 
                                    wire:model.live="category_id"
                                    class="w-full text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                                >
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
                                    Item / Sinking Fund
                                </label>
                                <select 
                                    wire:model.live="item_id"
                                    class="w-full text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all disabled:opacity-50 disabled:bg-slate-100"
                                    @if(!$category_id) disabled @endif
                                >
                                    <option value="">-- {{ $category_id ? 'Pilih Item' : 'Pilih Kategori Dulu' }} --</option>
                                    @foreach($availableItems as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('item_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Payment Account / Source Account --}}
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
                                {{ $type === 'transfer' ? 'Dompet Asal' : 'Akun Pembayaran / Dompet' }}
                            </label>
                            <select 
                                wire:model.live="account_id"
                                class="w-full text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                            >
                                <option value="">-- Pilih Akun / Dompet --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->type->label() }})</option>
                                @endforeach
                            </select>
                            @error('account_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Target Account (Transfer Only) --}}
                        @if($type === 'transfer')
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
                                    Dompet Tujuan
                                </label>
                                <select 
                                    wire:model.live="target_account_id"
                                    class="w-full text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                                >
                                    <option value="">-- Pilih Dompet Tujuan --</option>
                                    @foreach($accounts as $acc)
                                        @if($acc->id !== $account_id)
                                            <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->type->label() }})</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('target_account_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        {{-- Notes --}}
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
                                Catatan / Keterangan
                            </label>
                            <textarea 
                                wire:model.live="notes"
                                rows="2"
                                placeholder="Tambah catatan transaksi (opsional)..."
                                class="w-full text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all resize-none"
                            ></textarea>
                            @error('notes') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Action Buttons --}}
                        <div class="pt-3 flex items-center justify-end gap-3">
                            <button 
                                type="button" 
                                wire:click="closeModal"
                                class="w-1/2 sm:w-auto px-5 py-3 text-xs sm:text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors text-center"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit" 
                                class="w-1/2 sm:w-auto px-6 py-3 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 rounded-xl shadow-md hover:shadow-lg active:scale-95 transition-all text-center"
                            >
                                Simpan Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
