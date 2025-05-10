<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupWhatsappNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup WhatsApp notification feature (run migrations and seeders)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up WhatsApp notification feature...');

        $this->info('Running migrations...');
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2023_10_30_000000_create_whatsapp_notifications_table.php',
            '--force' => true,
        ]);
        $this->info(Artisan::output());

        $this->info('Running migrations for WhatsApp number in documents...');
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2023_10_30_000001_add_whatsapp_number_to_documents_table.php',
            '--force' => true,
        ]);
        $this->info(Artisan::output());

        $this->info('Seeding Dripsender settings...');
        Artisan::call('db:seed', [
            '--class' => 'DripsenderSettingsSeeder',
            '--force' => true,
        ]);
        $this->info(Artisan::output());

        $this->info('Seeding WhatsApp notification templates...');
        Artisan::call('db:seed', [
            '--class' => 'WhatsappNotificationSeeder',
            '--force' => true,
        ]);
        $this->info(Artisan::output());

        $this->info('WhatsApp notification feature has been set up successfully!');
    }
} 