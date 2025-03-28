<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Subvention;

class SubventionRequestProcessed extends Mailable
{
    use Queueable, SerializesModels;

    public $subvention;
    public $status;

    public function __construct(Subvention $subvention, $status)
    {
        $this->subvention = $subvention;
        $this->status = $status;
    }

    public function build()
    {
        $subject = $this->status === 'approved' ? 'Subsidy Request Approved' : 'Subsidy Request Refused';
        return $this->subject($subject)
            ->view('emails.subvention_processed')
            ->with(['subvention' => $this->subvention, 'status' => $this->status]);
    }
}