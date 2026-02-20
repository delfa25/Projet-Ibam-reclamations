<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendReclamationEmail implements ShouldQueue
{
    use Queueable;

    public $email;
    public $mailable;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $mailable)
    {
        $this->email = $email;
        $this->mailable = $mailable;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send($this->mailable);
    }
}
