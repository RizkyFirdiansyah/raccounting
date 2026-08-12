<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Keseharian',
                'description' => 'Kebutuhan pokok dan pengeluaran harian seperti konsumsi, belanja, dll.',
                'color' => '#10B981',
                'icon' => 'heroicon-o-shopping-cart',
            ],
            [
                'name' => 'Tagihan Wajib',
                'description' => 'Tagihan rutin dan kewajiban bulanan seperti listrik, air, sewa, internet.',
                'color' => '#EF4444',
                'icon' => 'heroicon-o-receipt-percent',
            ],
            [
                'name' => 'Target/Keinginan',
                'description' => 'Sinking fund untuk impian, liburan, gadget, atau hobi.',
                'color' => '#3B82F6',
                'icon' => 'heroicon-o-sparkles',
            ],
            [
                'name' => 'Buffer',
                'description' => 'Dana cadangan dan proteksi darurat.',
                'color' => '#F59E0B',
                'icon' => 'heroicon-o-shield-check',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                [
                    'slug' => Str::slug($category['name']),
                    'description' => $category['description'],
                    'color' => $category['color'],
                    'icon' => $category['icon'],
                ]
            );
        }
    }
}
