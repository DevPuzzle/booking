<?php

namespace App\Mail;

use App\Page;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PagePublished extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The Page Instance
     *
     * @var Page
     */
    public $page;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('example@example.com')
            ->markdown('emails.published');
    }
}
