<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment
    ) {
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject:
                'VELORA Eyes - Nhắc lịch hẹn '
                . $this->appointment->appointment_code
        );
    }


    public function content(): Content
    {
        return new Content(
            view:
                'emails.appointments.reminder'
        );
    }


    public function attachments(): array
    {
        return [];
    }
}