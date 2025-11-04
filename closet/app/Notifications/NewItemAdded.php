<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewItemAdded extends Notification
{
    use Queueable;

    protected $item;
    protected $company;

    public function __construct($item, $company)
    {
        $this->item = $item;
        $this->company = $company;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "<strong>{$this->company->name}</strong> adicionou um novo item: <em>{$this->item->name}</em> 🛍️",
        ];
    }
}
