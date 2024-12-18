<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use App\Models\Package;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BookingComponent extends Component
{
    public $bookings;
    public $showDeleteModal = false;
    public $bookingToDelete;

    public function mount()
    {
        $this->loadBookings();
    }

    private function loadBookings()
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            $this->bookings = Booking::with(['user', 'package'])->get();
        } else {
            $packageIds = Package::where('user_id', $user->id)->pluck('id');
            $this->bookings = Booking::with(['user', 'package'])
                ->whereIn('package_id', $packageIds)
                ->get();
        }
    }

    public function confirmDelete($bookingId)
    {
        $this->bookingToDelete = $bookingId;
        $this->showDeleteModal = true;
    }

    public function deleteBooking()
    {
        try {
            $booking = Booking::findOrFail($this->bookingToDelete);

            // Delete related records
            $booking->payments()->delete();
            $booking->bookingPayments()->delete();
            $booking->paymentLinks()->delete();

            // Delete the booking
            $booking->delete();

            session()->flash('success', 'Booking deleted successfully.');
            $this->loadBookings(); // Reload the bookings
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting booking: ' . $e->getMessage());
        }

        $this->showDeleteModal = false;
    }

    public function render()
    {
        return view('livewire.admin.booking-component');
    }
}
