<?php

namespace App\Notifications;

use App\Models\Community;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommunityJoined extends Notification
{
    use Queueable;

    protected $community;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Community $community)
    {
        $this->community = $community;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $communityUrl = url('/communities/' . $this->community->slug);
        
        return (new MailMessage)
            ->subject('Welcome to ' . $this->community->name . ' Community!')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have successfully joined the "' . $this->community->name . '" community.')
            ->line($this->community->description)
            ->action('Visit Community', $communityUrl)
            ->line('Start engaging with other members and participate in discussions!')
            ->line('Thank you for being part of our community!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'community_id' => $this->community->id,
            'community_name' => $this->community->name,
            'community_slug' => $this->community->slug,
        ];
    }
}
