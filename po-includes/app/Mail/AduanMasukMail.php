<?php

namespace App\Mail;

use App\Aduan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AduanMasukMail extends Mailable
{
    use Queueable, SerializesModels;

    public $aduan;

    public function __construct(Aduan $aduan)
    {
        $this->aduan = $aduan;
    }

    public function build()
    {
        return $this->subject('[MBG] Aduan Baru - '.$this->aduan->kode_tiket)
            ->view('emails.aduan-masuk')
            ->with([
                'aduan' => $this->aduan,
            ]);
    }
}
