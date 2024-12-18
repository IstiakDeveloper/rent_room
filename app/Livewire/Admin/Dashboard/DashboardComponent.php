<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;
class DashboardComponent extends Component
{
    public $totalUsers;
    public $totalPartner;
    public $totalPackages;
    public $totalBookings;
    public $monthlyRevenue;
    public $totalBookingRevenue;

    public function mount()
    {
        $user = Auth::user();

        // Fetch total number of users
        $this->totalUsers = User::role('User')->count();

        // Fetch total number of partners
        $this->totalPartner = User::role('Partner')->count();

        // Fetch total number of packages
        if ($user->hasRole('Super Admin')) {
            $this->totalPackages = Package::count();
        } else {
            $this->totalPackages = Package::where('user_id', $user->id)->count();
        }

        // Fetch total number of bookings
        if ($user->hasRole('Super Admin')) {
            $this->totalBookings = Booking::count();
        } else {
            $this->totalBookings = Booking::whereHas('package', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();
        }

        // Fetch monthly revenue
        $this->monthlyRevenue = Payment::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        // Fetch total booking revenue
        $this->totalBookingRevenue = Payment::sum('amount');
    }

    public function render()
    {
        return view('livewire.admin.dashboard.dashboard-component', [
            'totalUsers' => $this->totalUsers,
            'totalPartner' => $this->totalPartner,
            'totalPackages' => $this->totalPackages,
        ]);
    }

}
