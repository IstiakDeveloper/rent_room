<?php

namespace App\Livewire\User;

use App\Models\Booking;
use App\Models\Payment;
use Livewire\Component;

class BookingComplete extends Component
{
    public $booking;
    public $payment;


    public function mount($bookingId)
    {
        $this->booking = Booking::findOrFail($bookingId);
        $this->payment = Payment::where('booking_id', $this->booking->id)->first();
    }

    public function render()
    {
        return view('livewire.user.booking-complete')->layout('layouts.guest');
    }
}
