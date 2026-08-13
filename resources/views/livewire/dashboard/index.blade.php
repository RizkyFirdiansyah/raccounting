<x-slot:title>
    Dashboard Keuangan
</x-slot:title>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 pb-24 space-y-6">

    {{-- =========================================================
         HEADER: Filter Bulan & Tahun
    ========================================================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-100 shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Dashboard Keuangan</h1>
            <p class="text-xs text-slate-500 mt-0.5">Ringkasan kondisi keuangan & pencapaian alokasi.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            {{-- Tombol Navigasi Kelola Sinking Fund --}}
            <a 
                href="{{ route('sinking-funds.index') }}" 
                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100/80 active:bg-emerald-200 border border-emerald-200/60 rounded-xl transition-all shadow-2xs active:scale-95 shrink-0"
            >
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Kelola Sinking Fund
            </a>

            {{-- Filter Bulan & Tahun --}}
            <div class="flex items-center gap-2">
                <div class="relative">
                    <select
                        wire:model.live="month"
                        class="text-xs sm:text-sm font-medium text-slate-700 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 pr-7 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 appearance-none cursor-pointer"
                        id="dashboard-month-filter"
                    >
                        @foreach($monthNames as $num => $name)
                            <option value="{{ $num }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <div class="relative">
                    <select
                        wire:model.live="year"
                        class="text-xs sm:text-sm font-medium text-slate-700 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 pr-7 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 appearance-none cursor-pointer"
                        id="dashboard-year-filter"
                    >
                        @foreach($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================
         KPI SUMMARY CARDS
    ========================================================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">

        {{-- Total Income --}}
        <div class="bg-linear-to-br from-emerald-500 to-teal-600 p-4 rounded-2xl text-white shadow-md">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold opacity-90 leading-tight">Total Pemasukan</span>
            </div>
            <p class="text-lg sm:text-xl font-black leading-none">
                Rp {{ number_format($summary['total_income'], 0, ',', '.') }}
            </p>
            <p class="text-[10px] opacity-75 mt-1">{{ $monthNames[$month] }} {{ $year }}</p>
        </div>

        {{-- Total Expense --}}
        <div class="bg-linear-to-br from-rose-500 to-pink-600 p-4 rounded-2xl text-white shadow-md">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold opacity-90 leading-tight">Total Pengeluaran</span>
            </div>
            <p class="text-lg sm:text-xl font-black leading-none">
                Rp {{ number_format($summary['total_expense'], 0, ',', '.') }}
            </p>
            <p class="text-[10px] opacity-75 mt-1">{{ $monthNames[$month] }} {{ $year }}</p>
        </div>

        {{-- Net Cashflow --}}
        @php $isPositive = $summary['net_cashflow'] >= 0; @endphp
        <div class="bg-linear-to-br {{ $isPositive ? 'from-blue-500 to-indigo-600' : 'from-amber-500 to-orange-600' }} p-4 rounded-2xl text-white shadow-md">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold opacity-90 leading-tight">Net Cashflow</span>
            </div>
            <p class="text-lg sm:text-xl font-black leading-none">
                {{ $isPositive ? '+' : '' }}Rp {{ number_format($summary['net_cashflow'], 0, ',', '.') }}
            </p>
            <p class="text-[10px] opacity-75 mt-1">Selisih Masuk - Keluar</p>
        </div>

        {{-- Total Savings --}}
        <div class="bg-linear-to-br from-violet-500 to-purple-600 p-4 rounded-2xl text-white shadow-md">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold opacity-90 leading-tight">Akumulasi Tabungan</span>
            </div>
            <p class="text-lg sm:text-xl font-black leading-none">
                Rp {{ number_format($summary['total_savings'], 0, ',', '.') }}
            </p>
            <p class="text-[10px] opacity-75 mt-1">Savings & Buffer</p>
        </div>
    </div>

    {{-- =========================================================
         ACCOUNT REAL BALANCES
    ========================================================== --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
        <h2 class="text-base font-bold text-slate-800 mb-4">Saldo Riil Tempat Simpan</h2>
        @if($accounts->isEmpty())
            <p class="text-sm text-slate-400 text-center py-4">Belum ada akun/dompet aktif terdaftar.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($accounts as $acc)
                    @php
                        $typeLabel = $acc['type']->label();
                        $iconClass = match($acc['type']->value) {
                            'cash'    => 'bg-emerald-100 text-emerald-700',
                            'bank'    => 'bg-blue-100 text-blue-700',
                            'ewallet' => 'bg-violet-100 text-violet-700',
                            'savings' => 'bg-amber-100 text-amber-700',
                            default   => 'bg-slate-100 text-slate-700',
                        };
                        $isNeg = $acc['balance'] < 0;
                    @endphp
                    <div class="flex items-center gap-3 p-3.5 bg-slate-50 rounded-xl border border-slate-200/80">
                        <div class="w-10 h-10 rounded-xl {{ $iconClass }} flex items-center justify-center shrink-0 text-sm font-bold">
                            {{ strtoupper(substr($acc['name'], 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate">{{ $acc['name'] }}</p>
                            <p class="text-[10px] text-slate-400">{{ $typeLabel }}</p>
                            <p class="text-sm font-bold {{ $isNeg ? 'text-rose-600' : 'text-slate-900' }} mt-0.5 leading-tight">
                                {{ $isNeg ? '-' : '' }}Rp {{ number_format(abs($acc['balance']), 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- =========================================================
         CHARTS ROW
    ========================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

        {{-- Bar Chart: Income vs Expense Trend (3/5 width on lg) --}}
        <div class="lg:col-span-3 bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
            <h2 class="text-base font-bold text-slate-800 mb-1">Tren Pemasukan vs Pengeluaran</h2>
            <p class="text-xs text-slate-400 mb-4">6 bulan terakhir</p>
            <div
                id="bar-chart"
                wire:ignore
                x-data="{
                    chart: null,
                    init() {
                        this.render();
                        this.$watch('$wire.month', () => this.render());
                        this.$watch('$wire.year', () => this.render());
                    },
                    async render() {
                        await this.$nextTick();
                        const labels  = @js($trendData['labels']);
                        const incomes = @js($trendData['incomes']);
                        const expenses = @js($trendData['expenses']);

                        const options = {
                            chart: {
                                type: 'bar',
                                height: 240,
                                toolbar: { show: false },
                                fontFamily: 'inherit',
                                animations: { enabled: true, easing: 'easeinout', speed: 600 },
                            },
                            plotOptions: {
                                bar: { borderRadius: 6, columnWidth: '60%', dataLabels: { position: 'top' } }
                            },
                            dataLabels: { enabled: false },
                            series: [
                                { name: 'Pemasukan', data: incomes },
                                { name: 'Pengeluaran', data: expenses },
                            ],
                            xaxis: {
                                categories: labels,
                                labels: { style: { fontSize: '11px', colors: '#94a3b8' } },
                                axisBorder: { show: false },
                                axisTicks: { show: false },
                            },
                            yaxis: {
                                labels: {
                                    formatter: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v),
                                    style: { fontSize: '10px', colors: '#94a3b8' },
                                }
                            },
                            colors: ['#10b981', '#f43f5e'],
                            legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' },
                            grid: { borderColor: '#f1f5f9', strokeDashArray: 4, padding: { left: 0, right: 0 } },
                            tooltip: {
                                y: { formatter: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) }
                            },
                            responsive: [{ breakpoint: 480, options: { chart: { height: 200 } } }],
                        };

                        if (this.chart) {
                            this.chart.updateOptions(options, true, true);
                        } else {
                            this.chart = new ApexCharts(document.getElementById('bar-chart'), options);
                            this.chart.render();
                        }
                    }
                }"
            ></div>
        </div>

       {{-- Transaction Log: Recent activity (2/5 width on lg) --}}
        <div class="lg:col-span-2 bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex flex-col">
            {{-- Header Widget dengan Select Dropdown --}}
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-800">Log Transaksi Terbaru</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Mengikuti filter bulan dan tahun aktif.</p>
                </div>

                {{-- Dropdown Filter Jenis Transaksi --}}
                <div class="relative min-w-[130px]">
                    <select
                        wire:model.live="transactionTypeFilter"
                        class="w-full text-xs font-semibold text-slate-700 bg-slate-100/80 border border-slate-200/60 rounded-xl px-3 py-1.5 pr-8 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all appearance-none cursor-pointer"
                    >
                        @foreach($transactionTypeOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    
                    {{-- Chevron Arrow Icon --}}
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Transaction List / Empty State --}}
            @if($recentTransactions->isEmpty())
                <div class="flex-1 flex items-center justify-center py-10 text-slate-400 text-sm">
                    Belum ada transaksi pada periode ini.
                </div>
            @else
                <div class="space-y-3 overflow-y-auto max-h-85 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                    @foreach($recentTransactions as $transaction)
                        @php
                            $typeClass = match($transaction->type->value) {
                                'income' => 'bg-emerald-100 text-emerald-700',
                                'expense' => 'bg-rose-100 text-rose-700',
                                'transfer' => 'bg-blue-100 text-blue-700',
                            };

                            $sign = match($transaction->type->value) {
                                'income' => '+',
                                'expense' => '-',
                                'transfer' => '↔',
                            };

                            $accountLabel = $transaction->type->value === 'transfer'
                                ? trim(($transaction->account?->name ?? '-') . ' → ' . ($transaction->targetAccount?->name ?? $transaction->targetItem?->name ?? '-'))
                                : ($transaction->account?->name ?? '-');

                            $contextLabel = $transaction->category?->name ?? $transaction->item?->name ?? $transaction->targetItem?->name ?? 'Tanpa kategori';
                        @endphp

                        <div class="rounded-2xl border border-slate-100 bg-slate-50/80 px-4 py-3 hover:bg-slate-50 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $typeClass }}">
                                            {{ $transaction->type->label() }}
                                        </span>
                                        <span class="text-[10px] text-slate-400">
                                            {{ $transaction->transaction_date?->format('d M Y') }}
                                        </span>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-800 mt-1 truncate">
                                        {{ $transaction->description ?: 'Tanpa catatan' }}
                                    </p>
                                    <p class="text-[11px] text-slate-500 mt-0.5 truncate">
                                        {{ $accountLabel }} · {{ $contextLabel }}
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-bold {{ $transaction->type->value === 'expense' ? 'text-rose-600' : 'text-slate-800' }}">
                                        {{ $sign }}Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- =========================================================
         SINKING FUND TARGETS WITH FILTERS
    ========================================================== --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-4">
        <div class="flex items-start justify-between gap-3 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-slate-800">Target & Sinking Fund</h2>
                <p class="text-xs text-slate-400 mt-0.5">Fokus pada item dengan target terbesar, saldo terbesar, atau prioritas paling tinggi.</p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <select wire:model.live="itemPriorityFilter" class="text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @foreach($itemPriorityOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <select wire:model.live="itemSort" class="text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @foreach($itemSortOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach($itemStatusOptions as $value => $label)
                <button
                    type="button"
                    wire:click="$set('itemStatusFilter', '{{ $value }}')"
                    class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $itemStatusFilter === $value ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        @if($sinkingFundItems->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-200 p-8 text-center text-slate-400 text-sm">
                Belum ada item sinking fund yang cocok dengan filter.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($sinkingFundItems as $item)
                    @php
                        $target = (float) $item->target_amount;
                        $current = (float) $item->current_amount;
                        $remaining = max(0, $target - $current);
                        $progress = $target > 0 ? min(100, round(($current / $target) * 100, 1)) : 0;
                        $statusClass = match($item->status->value) {
                            'terpenuhi' => 'bg-emerald-100 text-emerald-700',
                            'proses' => 'bg-amber-100 text-amber-700',
                            'belum' => 'bg-slate-100 text-slate-600',
                        };
                        $priorityClass = match($item->priority->value) {
                            'wajib' => 'bg-rose-100 text-rose-700',
                            'emergency' => 'bg-orange-100 text-orange-700',
                            'rutin_bulanan' => 'bg-blue-100 text-blue-700',
                            'keinginan_shortterm' => 'bg-violet-100 text-violet-700',
                        };
                    @endphp

                    <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-4 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">{{ $item->name }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ $item->category?->name ?? 'Tanpa kategori' }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $statusClass }}">{{ $item->status->label() }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $priorityClass }}">{{ $item->priority->label() }}</span>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs text-slate-500">
                                <span>Progress</span>
                                <span class="font-semibold text-slate-700">{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                                <div class="h-2.5 rounded-full {{ $progress >= 100 ? 'bg-emerald-500' : ($progress > 0 ? 'bg-amber-500' : 'bg-slate-300') }}" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="rounded-xl bg-white p-3 border border-slate-100">
                                <p class="text-slate-400">Terkumpul</p>
                                <p class="font-bold text-slate-800 mt-1">Rp {{ number_format($current, 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-xl bg-white p-3 border border-slate-100">
                                <p class="text-slate-400">Sisa Target</p>
                                <p class="font-bold text-slate-800 mt-1">Rp {{ number_format($remaining, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <p class="text-[11px] text-slate-500">Target Rp {{ number_format($target, 0, ',', '.') }} @if($item->target_date) · {{ $item->target_date->format('d M Y') }} @endif</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- =========================================================
         WATERFALL ALLOCATION WIDGET
    ========================================================== --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs">
        <div class="flex items-start justify-between flex-wrap gap-2 mb-5">
            <div>
                <h2 class="text-base font-bold text-slate-800">Rekomendasi Alokasi Pemasukan</h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Berdasarkan Total Pemasukan: <span class="font-semibold text-slate-700">Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</span>
                    pada {{ $monthNames[$month] }} {{ $year }}
                </p>
            </div>
            @php $hasIncome = $summary['total_income'] > 0; @endphp
        </div>

        @if(!$hasIncome)
            <div class="text-center py-8 text-slate-400 text-sm">
                Tidak ada pemasukan bulan ini untuk dihitung alokasinya.
            </div>
        @else
            <div class="space-y-4">
                {{-- Keseharian & Tagihan Wajib: 60% --}}
                @php $pctKeseharian = 60; $amtKeseharian = $allocation['keseharian']; @endphp
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                            <span class="text-sm font-semibold text-slate-700">Keseharian & Tagihan Wajib</span>
                            <span class="text-xs text-slate-400 font-mono">60%</span>
                        </div>
                        <span class="text-sm font-bold text-slate-800">Rp {{ number_format($amtKeseharian, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div class="h-2.5 rounded-full bg-emerald-500 transition-all duration-700" style="width: {{ $pctKeseharian }}%"></div>
                    </div>
                </div>

                {{-- Sinking Fund / Target: 25% --}}
                @php $pctSinking = 25; $amtSinking = $allocation['sinkingfund']; @endphp
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-violet-500 inline-block"></span>
                            <span class="text-sm font-semibold text-slate-700">Target & Sinking Fund</span>
                            <span class="text-xs text-slate-400 font-mono">25%</span>
                        </div>
                        <span class="text-sm font-bold text-slate-800">Rp {{ number_format($amtSinking, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div class="h-2.5 rounded-full bg-violet-500 transition-all duration-700" style="width: {{ $pctSinking }}%"></div>
                    </div>
                </div>

                {{-- Buffer / Emergency: 15% --}}
                @php $pctBuffer = 15; $amtBuffer = $allocation['buffer']; @endphp
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                            <span class="text-sm font-semibold text-slate-700">Buffer & Emergency Fund</span>
                            <span class="text-xs text-slate-400 font-mono">15%</span>
                        </div>
                        <span class="text-sm font-bold text-slate-800">Rp {{ number_format($amtBuffer, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div class="h-2.5 rounded-full bg-amber-500 transition-all duration-700" style="width: {{ $pctBuffer }}%"></div>
                    </div>
                </div>

                {{-- Visual stacked bar summary --}}
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-xs text-slate-400 mb-2 font-medium">Porsi Alokasi Keseluruhan</p>
                    <div class="flex w-full h-4 rounded-full overflow-hidden gap-px">
                        <div class="bg-emerald-500 transition-all duration-700" style="width: 60%"
                             title="Keseharian 60%"></div>
                        <div class="bg-violet-500 transition-all duration-700" style="width: 25%"
                             title="Sinking Fund 25%"></div>
                        <div class="bg-amber-500 transition-all duration-700" style="width: 15%"
                             title="Buffer 15%"></div>
                    </div>
                    <div class="flex justify-between mt-1 text-[10px] text-slate-400">
                        <span>Keseharian 60%</span>
                        <span>Sinking Fund 25%</span>
                        <span>Buffer 15%</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- =========================================================
         FAB - Fast Entry Transaction Shortcut
    ========================================================== --}}
    @livewire('transactions.fast-entry-modal')

</div>

{{-- ApexCharts CDN --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3" defer></script>

