<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SampleMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }
  
    public function build()
    {


        // return $this->view('email.sample')
        // ->subject($this->mailData['subject'])
        // ->with(['mailData' => $this->mailData])
        // ->attach($this->mailData['file']->getRealPath(), [
        //     'as' => 'attachment.pdf',
        //     'mime' => 'application/pdf',
        // ]);\
        $mail = $this->view('email.sample')
    ->subject($this->mailData['subject'])
    ->with(['mailData' => $this->mailData]);

// if ($this->mailData['file'] !== null) {
//     $mail->attach($this->mailData['file']->getRealPath(), [
//         'as' => 'attachment.pdf',
//         'mime' => 'application/pdf',
//     ]);
// }

    
    }
}