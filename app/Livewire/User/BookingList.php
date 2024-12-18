<?php

namespace App\Livewire\User;

use App\Models\Booking;
use Livewire\Component;

class BookingList extends Component
{
    public $bookings;

    public function mount()
    {
        // Fetch bookings for the currently authenticated user
        $this->bookings = Booking::where('user_id', auth()->id())->get();
    }

    public function render()
    {
        return view('livewire.user.booking-list');
    }
}
