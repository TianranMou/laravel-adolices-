<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Subvention;

class SubventionRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $subvention;

    public function __construct(Subvention $subvention)
    {
        $this->subvention = $subvention;
    }

    public function build()
    {
        return $this->subject('New Subsidy Request Submitted')
            ->view('emails.subvention_submitted')
            ->with(['subvention' => $this->subvention]);
    }
}