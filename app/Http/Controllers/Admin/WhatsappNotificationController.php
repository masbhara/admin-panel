<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappNotification;
use App\Models\Setting;
use App\Services\DripsenderService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WhatsappNotificationController extends Controller
{
    protected $dripsenderService;

    public function __construct(DripsenderService $dripsenderService)
    {
        $this->dripsenderService = $dripsenderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notifications = WhatsappNotification::orderBy('created_at', 'desc')->paginate(10);
        
        return Inertia::render('Admin/WhatsappNotification/Index', [
            'notifications' => $notifications
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/WhatsappNotification/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'template' => 'required|string',
            'event_type' => 'required|string|max:255',
            'is_active' => 'boolean',
            'variables' => 'nullable|array',
        ]);

        $notification = WhatsappNotification::create($request->all());

        return redirect()->route('admin.whatsapp-notifications.index')
            ->with('success', 'Template notifikasi WhatsApp berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(WhatsappNotification $whatsappNotification)
    {
        return Inertia::render('Admin/WhatsappNotification/Show', [
            'notification' => $whatsappNotification
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WhatsappNotification $whatsappNotification)
    {
        return Inertia::render('Admin/WhatsappNotification/Edit', [
            'notification' => $whatsappNotification
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WhatsappNotification $whatsappNotification)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'template' => 'required|string',
            'event_type' => 'required|string|max:255',
            'is_active' => 'boolean',
            'variables' => 'nullable|array',
        ]);

        $whatsappNotification->update($request->all());

        return redirect()->route('admin.whatsapp-notifications.index')
            ->with('success', 'Template notifikasi WhatsApp berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WhatsappNotification $whatsappNotification)
    {
        $whatsappNotification->delete();

        return redirect()->route('admin.whatsapp-notifications.index')
            ->with('success', 'Template notifikasi WhatsApp berhasil dihapus');
    }

    /**
     * Display settings page.
     */
    public function settings()
    {
        $settings = [
            'dripsender_api_key' => Setting::where('key', 'dripsender_api_key')->first(),
            'dripsender_webhook_url' => Setting::where('key', 'dripsender_webhook_url')->first(),
            'whatsapp_notification_enabled' => Setting::where('key', 'whatsapp_notification_enabled')->first(),
        ];
        
        return Inertia::render('Admin/WhatsappNotification/Settings', [
            'settings' => $settings
        ]);
    }

    /**
     * Update settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'dripsender_api_key' => 'required|string',
            'dripsender_webhook_url' => 'required|string|url',
            'whatsapp_notification_enabled' => 'boolean',
        ]);

        Setting::updateOrCreate(
            ['key' => 'dripsender_api_key'],
            [
                'value' => $request->dripsender_api_key,
                'group' => 'notification',
                'type' => 'text',
                'is_public' => false,
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'dripsender_webhook_url'],
            [
                'value' => $request->dripsender_webhook_url,
                'group' => 'notification',
                'type' => 'text',
                'is_public' => false,
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'whatsapp_notification_enabled'],
            [
                'value' => $request->whatsapp_notification_enabled,
                'group' => 'notification',
                'type' => 'boolean',
                'is_public' => false,
            ]
        );

        return redirect()->back()
            ->with('success', 'Pengaturan notifikasi WhatsApp berhasil diupdate');
    }

    /**
     * Test connection to Dripsender.
     */
    public function testConnection()
    {
        $result = $this->dripsenderService->testConnection();
        
        if ($result['success']) {
            return redirect()->back()
                ->with('success', $result['message']);
        }
        
        return redirect()->back()
            ->with('error', $result['message']);
    }
} 