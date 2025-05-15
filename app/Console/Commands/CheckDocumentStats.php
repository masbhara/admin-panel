<?php

namespace App\Console\Commands;

use App\Models\Document;
use Illuminate\Console\Command;

class CheckDocumentStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memeriksa statistik dokumen dalam database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $total = Document::count();
        $approved = Document::where('status', 'approved')->count();
        $pending = Document::where('status', 'pending')->count();
        $rejected = Document::where('status', 'rejected')->count();

        $this->info('Statistik Dokumen:');
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total Dokumen', $total],
                ['Dokumen Disetujui', $approved],
                ['Dokumen Pending', $pending],
                ['Dokumen Ditolak', $rejected],
            ]
        );

        return Command::SUCCESS;
    }
} 