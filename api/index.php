<?php

// Arahkan storage path ke /tmp karena Vercel bersifat read-only
$_ENV['APP_STORAGE'] = '/tmp/storage';

// Buat folder temporer untuk session, views, dan cache
$directories = [
  '/tmp/storage/framework/views',
  '/tmp/storage/framework/sessions',
  '/tmp/storage/framework/cache',
  '/tmp/storage/logs',
];

foreach ($directories as $directory) {
  if (!is_dir($directory)) {
    mkdir($directory, 0755, true);
  }
}

// Panggil entrypoint bawaan Laravel
require __DIR__ . '/../public/index.php';
