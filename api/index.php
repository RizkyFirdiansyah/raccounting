<?php

// Forward kustom storage path Vercel ke /tmp agar tidak error read-only
$_ENV['APP_STORAGE'] = '/tmp/storage';

// Pastikan direktori temporary storage tersedia untuk sesi & cache Livewire
if (!is_dir('/tmp/storage/framework/views')) {
  mkdir('/tmp/storage/framework/views', 0755, true);
  mkdir('/tmp/storage/framework/sessions', 0755, true);
  mkdir('/tmp/storage/framework/caches', 0755, true);
  mkdir('/tmp/storage/logs', 0755, true);
}

// Panggil entry point public bawaan Laravel
require __DIR__ . '/../public/index.php';
