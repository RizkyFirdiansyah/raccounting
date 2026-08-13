<x-slot:title>
    Sinking Fund
</x-slot:title>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 space-y-6">
    @if (session()->has('message'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 4000)"
            class="flex items-center p-4 text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl shadow-sm transition-all"
        >
            <svg class="w-5 h-5 me-2 fill-current" viewBox="0 0 20 20">
                <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm4.707 7.707l-5.5 5.5a1 1 0 01-1.414 0l-2.5-2.5a1 1 0 011.414-1.414L9 11.086l4.793-4.793a1 1 0 011.414 1.414z"/>
            </svg>
            <span class="text-sm font-medium">{{ session('message') }}</span>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Sinking Funds & Goals</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola pos utama, item target, dan riwayat transaksi per item.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <button wire:click="openCategoryModal()" type="button" class="px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all shadow-2xs">+ Pos Utama</button>
            <button wire:click="openItemModal()" type="button" class="px-4 py-2.5 text-xs sm:text-sm font-bold text-white bg-linear-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 rounded-xl transition-all shadow-md active:scale-95">+ Sinking Fund / Goal</button>
            <a href="{{ route('dashboard') }}" class="px-4 py-2.5 text-xs sm:text-sm font-bold text-white bg-linear-to-r from-slate-500 to-slate-600 hover:from-slate-600 hover:to-slate-700 rounded-xl transition-all shadow-2xs active:scale-95 inline-flex items-center justify-center">← Kembali</a>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
        <div class="relative flex-1 max-w-md">
            <div class="absolute inset-y-0 inset-s-0 flex items-center ps-3.5 pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama sinking fund / goal..." class="w-full text-sm text-slate-800 bg-white border border-slate-200 rounded-xl ps-10 pe-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all shadow-2xs" />
        </div>

        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
            <button wire:click="$set('statusFilter', 'all')" type="button" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ $statusFilter === 'all' ? 'bg-slate-800 text-white shadow-2xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">Semua</button>
            <button wire:click="$set('statusFilter', 'proses')" type="button" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ $statusFilter === 'proses' ? 'bg-amber-500 text-white shadow-2xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">Proses</button>
            <button wire:click="$set('statusFilter', 'terpenuhi')" type="button" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ $statusFilter === 'terpenuhi' ? 'bg-emerald-500 text-white shadow-2xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">Terpenuhi</button>
            <button wire:click="$set('statusFilter', 'belum')" type="button" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ $statusFilter === 'belum' ? 'bg-slate-500 text-white shadow-2xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">Belum</button>
        </div>
    </div>

    <div class="flex items-center gap-2 bg-white p-1.5 rounded-2xl border border-slate-100 shadow-xs w-fit">
        <button type="button" wire:click="setActiveTab('overview')" class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition-colors {{ $activeTab === 'overview' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}">Ringkasan Pos</button>
        <button type="button" wire:click="setActiveTab('detail')" class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition-colors {{ $activeTab === 'detail' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}">Detail Item & Target Pos</button>
    </div>

    @if($activeTab === 'overview')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @forelse($categorySummaries as $summary)
                @php
                    $progress = $summary['target_total'] > 0 ? min(100, round(($summary['current_total'] / $summary['target_total']) * 100, 1)) : 0;
                @endphp
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">{{ $summary['name'] }}</h2>
                            @if($summary['description'])
                                <p class="text-xs text-slate-500 mt-0.5">{{ $summary['description'] }}</p>
                            @endif
                        </div>
                        <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">{{ $summary['item_count'] }} item</span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <div class="rounded-xl bg-emerald-50 p-3"><p class="text-emerald-700 font-semibold">Target Total</p><p class="font-bold text-slate-800 mt-1">Rp {{ number_format($summary['target_total'], 0, ',', '.') }}</p></div>
                        <div class="rounded-xl bg-blue-50 p-3"><p class="text-blue-700 font-semibold">Terkumpul / Terbayar</p><p class="font-bold text-slate-800 mt-1">Rp {{ number_format($summary['current_total'], 0, ',', '.') }}</p></div>
                        <div class="rounded-xl bg-amber-50 p-3"><p class="text-amber-700 font-semibold">Sisa Target</p><p class="font-bold text-slate-800 mt-1">Rp {{ number_format($summary['remaining_total'], 0, ',', '.') }}</p></div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-xs"><span class="text-slate-500">Progress Pos</span><span class="font-bold text-slate-800">{{ $progress }}%</span></div>
                        <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden"><div class="h-2.5 rounded-full bg-emerald-500" style="width: {{ $progress }}%"></div></div>
                    </div>

                    <div class="space-y-2">
                        <p class="text-xs font-semibold uppercase text-slate-400">Daftar Item</p>
                        <div class="space-y-2">
                            @forelse($summary['items'] as $item)
                                @php $itemRemaining = max(0, $item['target_amount'] - $item['current_amount']); @endphp
                                <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2.5">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $item['name'] }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $item['priority']->label() }} · {{ $item['status']->label() }}</p>
                                    </div>
                                    <div class="text-right text-[11px] text-slate-500">
                                        <p>Rp {{ number_format($item['current_amount'], 0, ',', '.') }}</p>
                                        <p>Sisa Rp {{ number_format($itemRemaining, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-400">Belum ada item di pos ini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 text-center rounded-2xl border border-slate-100 lg:col-span-2"><p class="text-slate-500">Belum ada Pos Utama atau item sinking fund terdaftar.</p></div>
            @endforelse
        </div>
    @else
        <div class="space-y-8">
            @forelse($categories as $category)
                @if($category->items->isNotEmpty() || $search === '')
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-3">
                                <span class="w-3.5 h-3.5 rounded-full inline-block" style="background-color: {{ $category->color ?? '#3B82F6' }}"></span>
                                <div>
                                    <h2 class="text-lg font-bold text-slate-800">{{ $category->name }}</h2>
                                    @if($category->description)
                                        <p class="text-xs text-slate-500">{{ $category->description }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button wire:click="openItemModal(null, {{ $category->id }})" type="button" class="text-xs font-medium text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition-colors">+ Item</button>
                                <button wire:click="openCategoryModal({{ $category->id }})" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100" title="Edit Pos Utama">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                            </div>
                        </div>

                        @if($category->items->isEmpty())
                            <div class="text-center py-6 text-slate-400 text-sm italic">Belum ada item/sinking fund di pos ini. Klik <button wire:click="openItemModal(null, {{ $category->id }})" class="text-emerald-600 font-semibold underline">+ Item</button> untuk menambahkan.</div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($category->items as $item)
                                    @php
                                        $target = (float) $item->target_amount;
                                        $current = (float) $item->current_amount;
                                        $percentage = $target > 0 ? min(100, round(($current / $target) * 100, 1)) : 0;
                                        $remaining = max(0, $target - $current);
                                        $priorityClass = match($item->priority) {
                                            App\Enums\ItemPriority::Wajib => 'bg-rose-100 text-rose-700 border-rose-200',
                                            App\Enums\ItemPriority::RutinBulanan => 'bg-blue-100 text-blue-700 border-blue-200',
                                            App\Enums\ItemPriority::KeinginanShortterm => 'bg-purple-100 text-purple-700 border-purple-200',
                                            App\Enums\ItemPriority::Emergency => 'bg-amber-100 text-amber-800 border-amber-200',
                                        };
                                        $statusClass = match($item->status) {
                                            App\Enums\ItemStatus::Terpenuhi => 'bg-emerald-500 text-white',
                                            App\Enums\ItemStatus::Proses => 'bg-amber-500 text-white',
                                            App\Enums\ItemStatus::Belum => 'bg-slate-400 text-white',
                                        };
                                    @endphp
                                    <div class="bg-slate-50/70 hover:bg-slate-50 p-4 rounded-xl border border-slate-200/80 transition-all flex flex-col justify-between space-y-3">
                                        <div>
                                            <div class="flex items-start justify-between gap-2">
                                                <h3 class="font-bold text-slate-800 text-base leading-snug">{{ $item->name }}</h3>
                                                @if($item->account)
                                                    <span class="inline-flex items-center text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 whitespace-nowrap">{{ $item->account->name }}</span>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                                                <span class="text-[11px] font-medium px-2 py-0.5 rounded-md border {{ $priorityClass }}">{{ $item->priority->label() }}</span>
                                                <span class="text-[11px] font-bold uppercase px-2 py-0.5 rounded-md {{ $statusClass }}">{{ $item->status->label() }}</span>
                                            </div>
                                        </div>

                                        <div class="space-y-1.5 pt-1">
                                            <div class="flex justify-between items-baseline text-xs"><span class="text-slate-500 font-medium">Terkumpul / Terbayar</span><span class="font-bold text-slate-800">{{ $percentage }}%</span></div>
                                            <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden"><div class="h-2.5 rounded-full transition-all duration-500 {{ $percentage >= 100 ? 'bg-emerald-500' : ($percentage > 0 ? 'bg-amber-500' : 'bg-slate-300') }}" style="width: {{ $percentage }}%"></div></div>
                                            <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                                                <div><p class="text-slate-400">Terkumpul / Terbayar</p><p class="font-bold text-emerald-600">Rp {{ number_format($current, 0, ',', '.') }}</p></div>
                                                <div class="text-right"><p class="text-slate-400">Sisa Target</p><p class="font-bold text-amber-600">Rp {{ number_format($remaining, 0, ',', '.') }}</p></div>
                                            </div>
                                            <p class="text-[11px] text-slate-500">Target Rp {{ number_format($target, 0, ',', '.') }}</p>
                                        </div>

                                        <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 text-xs">
                                            <span class="text-slate-400">{{ $item->target_date ? $item->target_date->format('d M Y') : 'Tanpa Tenggat' }}</span>
                                            <div class="flex items-center gap-1">
                                                <button wire:click="openItemDetail({{ $item->id }})" type="button" class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-200/60 rounded-lg transition-colors" title="Lihat Log Transaksi"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20h9M3 20h9M4 4h16M4 8h16M4 12h8"/></svg></button>
                                                <button wire:click="openItemModal({{ $item->id }})" type="button" class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-200/60 rounded-lg transition-colors" title="Edit Item"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 113.536 3.536L12 20.243H8.5v-3.5a1 1 0 01.293-.707l9.414-9.414z"/></svg></button>
                                                <button wire:click="confirmDeleteItem({{ $item->id }})" type="button" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Item"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            @empty
                <div class="bg-white p-8 text-center rounded-2xl border border-slate-100"><p class="text-slate-500">Belum ada Pos Utama atau item sinking fund terdaftar.</p></div>
            @endforelse
        </div>
    @endif

    @if($showItemDetailModal && $selectedItem)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" wire:click="closeItemDetail"></div>
            <div class="flex min-h-full items-end justify-center sm:items-center p-0 sm:p-4 text-center">
                <div class="relative w-full max-w-2xl overflow-hidden rounded-t-3xl sm:rounded-2xl bg-white text-left shadow-2xl border border-slate-100">
                    <div class="flex items-center justify-between px-6 pt-4 pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Riwayat Transaksi Item</h3>
                            <p class="text-xs text-slate-500 mt-1">{{ $selectedItem->name }}</p>
                        </div>
                        <button wire:click="closeItemDetail" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-full hover:bg-slate-100"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>

                    <div class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                            <div class="rounded-xl bg-slate-50 p-3"><p class="text-slate-500 text-xs">Target</p><p class="font-bold text-slate-800 mt-1">Rp {{ number_format((float) $selectedItem->target_amount, 0, ',', '.') }}</p></div>
                            <div class="rounded-xl bg-emerald-50 p-3"><p class="text-emerald-700 text-xs font-semibold">Terkumpul / Terbayar</p><p class="font-bold text-slate-800 mt-1">Rp {{ number_format((float) $selectedItem->current_amount, 0, ',', '.') }}</p></div>
                            <div class="rounded-xl bg-amber-50 p-3"><p class="text-amber-700 text-xs font-semibold">Sisa Target</p><p class="font-bold text-slate-800 mt-1">Rp {{ number_format(max(0, (float) $selectedItem->target_amount - (float) $selectedItem->current_amount), 0, ',', '.') }}</p></div>
                            <div class="rounded-xl bg-blue-50 p-3"><p class="text-blue-700 text-xs font-semibold">Status</p><p class="font-bold text-slate-800 mt-1">{{ $selectedItem->status->label() }}</p></div>
                        </div>

                        <div class="space-y-2">
                            <p class="text-xs font-semibold uppercase text-slate-400">Log Transaksi</p>
                            @forelse($selectedItemLogs as $log)
                                @php
                                    $logClass = match($log->type) {
                                        App\Enums\TransactionType::Income => 'bg-emerald-100 text-emerald-700',
                                        App\Enums\TransactionType::Expense => 'bg-rose-100 text-rose-700',
                                        App\Enums\TransactionType::Transfer => 'bg-blue-100 text-blue-700',
                                    };
                                @endphp
                                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 space-y-2">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">{{ $log->type->label() }}</p>
                                            <p class="text-xs text-slate-500">{{ $log->transaction_date?->format('d M Y') }}</p>
                                        </div>
                                        <span class="text-[11px] font-semibold px-2 py-1 rounded-full {{ $logClass }}">Rp {{ number_format((float) $log->amount, 0, ',', '.') }}</span>
                                    </div>

                                    @if($log->description)
                                        <p class="text-sm text-slate-600">{{ $log->description }}</p>
                                    @endif

                                    <div class="flex flex-wrap gap-2 text-[11px] text-slate-500">
                                        @if($log->account)
                                            <span class="px-2 py-1 rounded-full bg-white border border-slate-200">Akun: {{ $log->account->name }}</span>
                                        @endif
                                        @if($log->category)
                                            <span class="px-2 py-1 rounded-full bg-white border border-slate-200">Kategori: {{ $log->category->name }}</span>
                                        @endif
                                        @if($log->targetAccount)
                                            <span class="px-2 py-1 rounded-full bg-white border border-slate-200">Target: {{ $log->targetAccount->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-400">Belum ada log transaksi untuk item ini.</div>
                            @endforelse
                        </div>

                        <div class="flex justify-end"><button type="button" wire:click="closeItemDetail" class="px-4 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl">Tutup</button></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showItemModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" wire:click="closeItemModal"></div>
            <div class="flex min-h-full items-end justify-center sm:items-center p-0 sm:p-4 text-center">
                <div class="relative w-full max-w-lg overflow-hidden rounded-t-3xl sm:rounded-2xl bg-white text-left shadow-2xl transition-all border border-slate-100" x-data="{ targetRaw: @entangle('target_amount'), formatRupiah(val) { if (!val) return ''; let number_string = val.toString().replace(/[^,\d]/g, ''); let split = number_string.split(','); let sisa = split[0].length % 3; let rupiah = split[0].substr(0, sisa); let ribuan = split[0].substr(sisa).match(/\d{3}/gi); if (ribuan) { let separator = sisa ? '.' : ''; rupiah += separator + ribuan.join('.'); } rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah; return rupiah ? 'Rp ' + rupiah : ''; }, onInput(e) { this.targetRaw = this.formatRupiah(e.target.value); } }">
                    <div class="flex items-center justify-between px-6 pt-4 pb-3 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800">{{ $editingItemId ? 'Edit Sinking Fund / Goal' : 'Tambah Sinking Fund / Goal Baru' }}</h3>
                        <button wire:click="closeItemModal" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-full hover:bg-slate-100"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>

                    <form wire:submit.prevent="saveItem" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Nama Item / Goal</label>
                            <input type="text" wire:model.live="item_name" placeholder="misal: Dana Darurat 6 Bulan, Servis Motor" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                            @error('item_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Pos Utama / Kategori</label>
                            <select wire:model.live="category_id" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">-- Pilih Pos Utama --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Target Biaya (Rp)</label>
                            <input type="text" x-bind:value="targetRaw" x-on:input="onInput($event)" placeholder="Rp 0" class="w-full text-lg font-bold bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                            @error('target_amount') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Prioritas</label>
                                <select wire:model.live="priority" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    @foreach(App\Enums\ItemPriority::cases() as $prio)
                                        <option value="{{ $prio->value }}">{{ $prio->label() }}</option>
                                    @endforeach
                                </select>
                                @error('priority') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Tempat Simpan Utama</label>
                                <select wire:model.live="account_id" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <option value="">-- Tanpa Akun Spesifik --</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->type->label() }})</option>
                                    @endforeach
                                </select>
                                @error('account_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Tenggat Target (Opsional)</label>
                            <input type="date" wire:model.live="target_date" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                            @error('target_date') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Catatan / Note</label>
                            <textarea wire:model.live="note" rows="2" placeholder="Catatan tambahan..." class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none"></textarea>
                            @error('note') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-3 flex justify-end gap-2">
                            <button type="button" wire:click="closeItemModal" class="px-4 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl">Batal</button>
                            <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($showCategoryModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" wire:click="closeCategoryModal"></div>
            <div class="flex min-h-full items-end justify-center sm:items-center p-0 sm:p-4 text-center">
                <div class="relative w-full max-w-md overflow-hidden rounded-t-3xl sm:rounded-2xl bg-white text-left shadow-2xl border border-slate-100">
                    <div class="flex items-center justify-between px-6 pt-4 pb-3 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800">{{ $editingCategoryId ? 'Edit Pos Utama' : 'Tambah Pos Utama Baru' }}</h3>
                        <button wire:click="closeCategoryModal" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-full hover:bg-slate-100"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>

                    <form wire:submit.prevent="saveCategory" class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Nama Pos Utama</label>
                            <input type="text" wire:model.live="category_name" placeholder="misal: Keseharian, Tagihan Wajib" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                            @error('category_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Deskripsi</label>
                            <textarea wire:model.live="category_description" rows="3" placeholder="Contoh: kebutuhan rutin bulanan" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none"></textarea>
                            @error('category_description') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Warna</label>
                            <input type="text" wire:model.live="category_color" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                            @error('category_color') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-3 flex justify-end gap-2">
                            <button type="button" wire:click="closeCategoryModal" class="px-4 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl">Batal</button>
                            <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($showDeleteConfirm)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" wire:click="$set('showDeleteConfirm', false)"></div>
            <div class="flex min-h-full items-end justify-center sm:items-center p-0 sm:p-4 text-center">
                <div class="relative w-full max-w-md overflow-hidden rounded-t-3xl sm:rounded-2xl bg-white text-left shadow-2xl border border-slate-100 p-6 space-y-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Hapus Item</h3>
                        <p class="text-sm text-slate-500 mt-1">Tindakan ini akan menghapus item dari daftar.</p>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="$set('showDeleteConfirm', false)" class="px-4 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl">Batal</button>
                        <button type="button" wire:click="deleteItem" class="px-5 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
