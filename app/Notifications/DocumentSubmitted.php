<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class DocumentSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $document;
    protected $sender;

    public function __construct($document, $sender)
    {
        $this->document = $document;
        $this->sender = $sender;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'document_id' => $this->document->id,
            'document_name' => $this->document->name,
            'sender_name' => $this->sender->name,
            'message' => "Dokumen baru diterima dari {$this->sender->name}",
            'time' => now()->toISOString(),
            'type' => 'document_submitted',
            'metadata' => [
                'file_name' => $this->document->file_name,
                'file_type' => $this->document->file_type,
                'status' => $this->document->status,
                'whatsapp' => $this->document->whatsapp_number,
                'city' => $this->document->metadata['city'] ?? null
            ]
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }

    /**
     * Get the broadcast channel name.
     *
     * @return string
     */
    public function broadcastOn()
    {
        return 'notifications.' . $this->document->id;
    }
} 