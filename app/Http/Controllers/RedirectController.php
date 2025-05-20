<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function handle()
    {
        if (auth()->user()->hasRole(['admin', 'super-admin'])) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }
} 