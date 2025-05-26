<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EnsureStorageDirectoriesExist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:ensure-directories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memastikan direktori storage yang diperlukan ada';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memastikan direktori storage yang diperlukan ada...');
        
        // Daftar direktori yang diperlukan
        $directories = [
            'public/documents',
            'public/screenshots',
            'public/previews',
        ];
        
        foreach ($directories as $directory) {
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
                $this->info("Direktori {$directory} berhasil dibuat.");
                Log::info("Direktori {$directory} berhasil dibuat.");
            } else {
                $this->info("Direktori {$directory} sudah ada.");
            }
        }
        
        // Pastikan symbolic link storage sudah dibuat
        $this->call('storage:link');
        
        // Periksa izin direktori
        $this->info('Memeriksa izin direktori...');
        
        $basePath = storage_path('app');
        $publicPath = public_path('storage');
        
        $this->info("Storage path: {$basePath}");
        $this->info("Public storage path: {$publicPath}");
        
        // Pastikan direktori public/storage ada
        if (!file_exists($publicPath)) {
            $this->error("Direktori {$publicPath} tidak ditemukan!");
            $this->info("Mencoba membuat symbolic link ulang...");
            $this->call('storage:link');
        }
        
        $this->info('Selesai.');
        
        return Command::SUCCESS;
    }
} 