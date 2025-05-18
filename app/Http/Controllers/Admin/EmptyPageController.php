<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class EmptyPageController extends Controller
{
    public function index()
    {
        // Tidak perlu mengambil pengaturan lagi, karena sudah disediakan 
        // secara global oleh AppServiceProvider ke semua halaman Inertia
        Log::info('EmptyPageController: Loading index page');
        
        return Inertia::render('Admin/EmptyPage/Index');
    }
} 