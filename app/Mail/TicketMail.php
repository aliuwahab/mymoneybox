<?php

namespace App\Mail;

use App\Models\EventBoxTicket;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $qrCodeData;

    public function __construct(public EventBoxTicket $ticket)
    {
        $qrCode = new QrCode(
            data: $this->ticket->code,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(21, 20, 15),
            backgroundColor: new Color(255, 255, 255)
        );

        $writer = new PngWriter();
        $this->qrCodeData = $writer->write($qrCode)->getString();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your ticket for {$this->ticket->eventBox->title} — {$this->ticket->code}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket',
            with: [
                'ticket'      => $this->ticket,
                'eventBox'    => $this->ticket->eventBox,
                'qrCodeData'  => $this->qrCodeData,
            ],
        );
    }
}