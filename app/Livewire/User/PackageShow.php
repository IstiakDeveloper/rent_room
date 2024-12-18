<?php

namespace App\Livewire\User;

use App\Models\Package;
use App\Models\Room;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class PackageShow extends Component
{
    public $package;
    public $packageId;
    public $views;
    public $fromDate;
    public $toDate;
    public $name;
    public $email;
    public $phone;
    public $password;
    public $viewMore = false;
    public $showAuthWarning;
    public $currentPhotoIndex = 0;
    public $showTermsModal = false;
    public $selectedRooms = [];
    public $availableRooms = [];
    public $showCalendar = false;
    public $dateRange = '';
    public $selectedRoom;
    public $disabledDates = [];
    public $selectedDates = [];
    public $calendarView = false;
    public $roomPrices = [];

    protected $rules = [
        'fromDate' => 'required|date',
        'toDate' => 'required|date|after:fromDate',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:15',
        'selectedRoom' => 'required',
    ];

    public function mount($id)
    {
        $this->packageId = $id;
        $this->fetchPackage();
        $this->incrementViews();
        $this->disabledDates = $this->fetchDisabledDates();

        if (Auth::check()) {
            $this->name = Auth::user()->name;
            $this->email = Auth::user()->email;
            $this->phone = Auth::user()->phone;
        }
    }


    private function determineOptimalPriceType($room, $startDate, $endDate)
    {
        $availableTypes = collect($room->roomPrices)->pluck('type')->unique();
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $totalDays = $startDate->diffInDays($endDate);

        $priceBreakdown = [
            'Month' => 0,
            'Week' => 0,
            'Day' => 0
        ];

        // First check if duration is over a month (28 days)
        if ($totalDays >= 28) {
            if (!$availableTypes->contains('Month')) {
                throw new \Exception("Monthly pricing is required for bookings of 28 days or more.");
            }
            return $this->calculateMonthlyBreakdown($startDate, $endDate);
        }

        // Then check if duration is 7 days or more
        if ($totalDays >= 7) {
            if (!$availableTypes->contains('Week')) {
                if ($availableTypes->contains('Month')) {
                    return $this->calculateMonthlyBreakdown($startDate, $endDate);
                }
                throw new \Exception("Weekly pricing is required for bookings of 7 days or more.");
            }
            return $this->calculateWeeklyBreakdown($totalDays);
        }

        // Finally, check for daily bookings (less than 7 days)
        if (!$availableTypes->contains('Day')) {
            if ($availableTypes->contains('Week')) {
                return $this->calculateWeeklyBreakdown($totalDays);
            } elseif ($availableTypes->contains('Month')) {
                return $this->calculateMonthlyBreakdown($startDate, $endDate);
            }
            throw new \Exception("Daily pricing is required for bookings less than 7 days.");
        }

        return [
            'Month' => 0,
            'Week' => 0,
            'Day' => $totalDays
        ];
    }

    private function calculateMonthlyBreakdown($startDate, $endDate)
    {
        // Get the first day of the next month for start date
        $nextMonthStart = $startDate->copy()->firstOfMonth()->addMonth();
        // Get the first day of the month for end date
        $lastMonthStart = $endDate->copy()->firstOfMonth();

        $months = 0;

        // Count full months between the dates
        if ($nextMonthStart->lt($lastMonthStart)) {
            $months = $nextMonthStart->diffInMonths($lastMonthStart);
        }

        // Add one month if there are days in the first month
        if ($startDate->day !== 1) {
            $months++;
        }

        // Add one month if there are days in the last month
        if ($endDate->day !== 1) {
            $months++;
        }

        // If the total days span into another month, add an extra month
        if ($endDate->day > 1) {
            $months++;
        }

        return [
            'Month' => $months,
            'Week' => 0,
            'Day' => 0
        ];
    }

    private function calculateWeeklyBreakdown($totalDays)
    {
        // Calculate full weeks and round up to next week if there are remaining days
        $fullWeeks = ceil($totalDays / 7);

        return [
            'Month' => 0,
            'Week' => $fullWeeks,
            'Day' => 0
        ];
    }

    public function getPriceBreakdown()
    {
        if (!$this->selectedRoom || !$this->fromDate || !$this->toDate) {
            return null;
        }

        $room = Room::with('roomPrices')->find($this->selectedRoom);
        if (!$room) return null;

        $startDate = Carbon::parse($this->fromDate);
        $endDate = Carbon::parse($this->toDate);
        $totalDays = $startDate->diffInDays($endDate);

        $priceBreakdown = $this->determineOptimalPriceType($room, $this->fromDate, $this->toDate);
        $prices = $room->roomPrices->keyBy('type');

        $breakdown = [];
        $total = 0;

        // Add monthly breakdown with month names
        if ($priceBreakdown['Month'] > 0 && isset($prices['Month'])) {
            $monthlyPrice = $prices['Month']->discount_price ?? $prices['Month']->fixed_price;
            $startDate = Carbon::parse($this->fromDate);

            for ($i = 0; $i < $priceBreakdown['Month']; $i++) {
                $currentMonth = $startDate->copy()->addMonths($i)->format('F Y');
                $breakdown[] = [
                    'type' => 'Month',
                    'quantity' => 1,
                    'price' => $monthlyPrice,
                    'total' => $monthlyPrice,
                    'description' => $currentMonth,
                    'note' => $i === $priceBreakdown['Month'] - 1 && $endDate->day > 1 ?
                        '(Includes partial month)' : ''
                ];
            }
            $total += $monthlyPrice * $priceBreakdown['Month'];
        }

        // Add weekly breakdown
        if ($priceBreakdown['Week'] > 0 && isset($prices['Week'])) {
            $weeklyPrice = $prices['Week']->discount_price ?? $prices['Week']->fixed_price;
            $weeklyTotal = $weeklyPrice * $priceBreakdown['Week'];
            $total += $weeklyTotal;

            $description = "{$priceBreakdown['Week']} " . ($priceBreakdown['Week'] > 1 ? 'Weeks' : 'Week');
            if ($totalDays % 7 > 0) {
                $description .= " (Includes " . ($totalDays % 7) . " extra days)";
            }

            $breakdown[] = [
                'type' => 'Week',
                'quantity' => $priceBreakdown['Week'],
                'price' => $weeklyPrice,
                'total' => $weeklyTotal,
                'description' => $description
            ];
        }

        // Add daily breakdown
        if ($priceBreakdown['Day'] > 0 && isset($prices['Day'])) {
            $dailyPrice = $prices['Day']->discount_price ?? $prices['Day']->fixed_price;
            $dailyTotal = $dailyPrice * $priceBreakdown['Day'];
            $total += $dailyTotal;
            $breakdown[] = [
                'type' => 'Day',
                'quantity' => $priceBreakdown['Day'],
                'price' => $dailyPrice,
                'total' => $dailyTotal,
                'description' => "{$priceBreakdown['Day']} " .
                    ($priceBreakdown['Day'] > 1 ? 'Days' : 'Day')
            ];
        }

        return [
            'breakdown' => $breakdown,
            'total' => round($total)
        ];
    }

    private function calculateRoomTotal()
    {
        if (!$this->selectedRoom || !$this->fromDate || !$this->toDate) {
            return 0;
        }

        $room = Room::with('roomPrices')->find($this->selectedRoom);
        if (!$room) return 0;

        // Get all available pricing types for this room
        $availablePrices = $room->roomPrices->keyBy('type');

        // Calculate based on price breakdown
        $priceBreakdown = $this->determineOptimalPriceType($room, $this->fromDate, $this->toDate);

        $total = 0;

        // Calculate total based on the breakdown
        if ($priceBreakdown['Month'] > 0 && isset($availablePrices['Month'])) {
            $monthlyPrice = $availablePrices['Month']->discount_price ?? $availablePrices['Month']->fixed_price;
            $total += $monthlyPrice * $priceBreakdown['Month'];
        }

        if ($priceBreakdown['Week'] > 0 && isset($availablePrices['Week'])) {
            $weeklyPrice = $availablePrices['Week']->discount_price ?? $availablePrices['Week']->fixed_price;
            $total += $weeklyPrice * $priceBreakdown['Week'];
        }

        if ($priceBreakdown['Day'] > 0 && isset($availablePrices['Day'])) {
            $dailyPrice = $availablePrices['Day']->discount_price ?? $availablePrices['Day']->fixed_price;
            $total += $dailyPrice * $priceBreakdown['Day'];
        }

        return round($total);
    }




    public function submit()
    {
        try {
            $this->validate();

            $selectedRoom = Room::with(['roomPrices'])->find($this->selectedRoom);
            $priceBreakdown = $this->determineOptimalPriceType($selectedRoom, $this->fromDate, $this->toDate);
            $totalAmount = $this->calculateRoomTotal();

            session()->put('checkout_data', [
                'packageId' => $this->packageId,
                'fromDate' => $this->fromDate,
                'toDate' => $this->toDate,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'selectedRoom' => $selectedRoom->id,
                'roomDetails' => [
                    'id' => $selectedRoom->id,
                    'name' => $selectedRoom->name,
                    'booking_price' => $selectedRoom->roomPrices->first()->booking_price,
                ],
                'priceBreakdown' => $this->getPriceBreakdown()['breakdown'],
                'roomTotal' => $totalAmount,
            ]);

            return redirect()->route('checkout');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();
        }
    }

    public function selectDates($dates)
    {
        $this->fromDate = $dates['start'];
        $this->toDate = $dates['end'];
        $this->fetchAvailableRooms();
        $this->validateDateRange();
    }

    public function validateDateRange()
    {
        if (!$this->fromDate || !$this->toDate) return;

        $from = Carbon::parse($this->fromDate);
        $to = Carbon::parse($this->toDate);

        foreach ($this->disabledDates as $disabledDate) {
            $checkDate = Carbon::parse($disabledDate);
            if ($checkDate->between($from, $to)) {
                $this->addError('dateRange', 'Some dates in your selection are already booked.');
                return false;
            }
        }
        return true;
    }


    public function selectRoom($roomId)
    {
        $this->selectedRoom = $roomId;
        $this->calendarView = true;

        $room = Room::with('roomPrices')->find($roomId);
        $this->roomPrices[$roomId] = $room->roomPrices->groupBy('type')->map(function ($prices) {
            return $prices->first();
        })->toArray();

        if ($this->fromDate && $this->toDate) {
            $this->calculateRoomTotal();
        }

        $this->selectedRoom = $roomId;
        $this->calendarView = true;
        $this->disabledDates = $this->fetchDisabledDates();
    }
    public function fetchDisabledDates()
    {
        if (!$this->packageId || !$this->selectedRoom) {
            return [];
        }

        try {
            return Booking::where('package_id', $this->packageId)
                // Filter out cancelled bookings
                ->whereNotIn('payment_status', ['cancelled', 'refunded'])
                ->whereRaw('JSON_CONTAINS(REPLACE(REPLACE(room_ids, "\\\", ""), "\"", ""), ?)', ["[$this->selectedRoom]"])
                ->get()
                ->flatMap(function ($booking) {
                    $bookedDates = [];
                    $from = Carbon::parse($booking->from_date);
                    $to = Carbon::parse($booking->to_date);

                    while ($from->lte($to)) {
                        $bookedDates[] = $from->format('Y-m-d');
                        $from->addDay();
                    }
                    return $bookedDates;
                })
                ->unique()
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            \Log::error('Error fetching disabled dates: ' . $e->getMessage());
            return [];
        }
    }

    public function updatedDisabledDates($value)
    {
        if (!is_array($value)) {
            $this->disabledDates = [];
        }
    }

    public function hydrate()
    {
        $this->fetchDisabledDates();
    }



    public function toggleModal()
    {
        $this->showTermsModal = !$this->showTermsModal;
    }

    public function previousPhoto()
    {
        if ($this->currentPhotoIndex > 0) {
            $this->currentPhotoIndex--;
        }
    }

    public function nextPhoto()
    {
        if ($this->currentPhotoIndex < $this->package->photos->count() - 1) {
            $this->currentPhotoIndex++;
        }
    }

    public function fetchPackage()
    {
        $this->package = Package::with([
            'country',
            'city',
            'area',
            'property',
            'rooms.roomPrices',
            'photos',
        ])->findOrFail($this->packageId);
    }

    public function getFirstAvailablePrice($prices)
    {
        $types = ['Day', 'Week', 'Month'];
        foreach ($types as $type) {
            foreach ($prices as $price) {
                if ($price->type === $type) {
                    return [
                        'price' => $price,
                        'type' => $type
                    ];
                }
            }
        }
        return null;
    }

    public function getPriceIndicator($type)
    {
        switch ($type) {
            case 'Day':
                return '(P/N by Room)';
            case 'Week':
                return '(P/W by Room)';
            case 'Month':
                return '(P/M by Room)';
            default:
                return '';
        }
    }
    public function getPropertyPriceIndicator($type)
    {
        switch ($type) {
            case 'Day':
                return '(P/N by Property)';
            case 'Week':
                return '(P/W by Property)';
            case 'Month':
                return '(P/M by Property)';
            default:
                return '';
        }
    }

    public function incrementViews()
    {
        $sessionKey = 'package_' . $this->packageId . '_views';
        if (!Session::has($sessionKey)) {
            Session::put($sessionKey, 0);
        }
        $this->views = Session::increment($sessionKey);
    }

    public function fetchAvailableRooms()
    {
        if (!$this->fromDate || !$this->toDate || !$this->packageId) {
            $this->reset(['availableRooms', 'disabledDates']);
            return [];
        }

        $package = Package::find($this->packageId);
        if (!$package) {
            $this->addError('package', 'Invalid package selected.');
            return [];
        }

        $bookedRoomIds = Booking::where('package_id', $this->packageId)
            ->where(function ($query) {
                $query->whereBetween('from_date', [$this->fromDate, $this->toDate])
                    ->orWhereBetween('to_date', [$this->fromDate, $this->toDate])
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('from_date', '<=', $this->fromDate)
                            ->where('to_date', '>=', $this->toDate);
                    });
            })
            ->get()
            ->flatMap(function ($booking) {
                return json_decode($booking->room_ids, true) ?: [];
            })
            ->unique()
            ->toArray();

        $allRooms = Room::where('package_id', $this->packageId)->get();
        $this->availableRooms = $allRooms->filter(function ($room) use ($bookedRoomIds) {
            return !in_array($room->id, $bookedRoomIds);
        });

        return $this->availableRooms;
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['fromDate', 'toDate'])) {
            $this->availableRooms = $this->fetchAvailableRooms();
        }
    }

    public function showAuthMessage($field)
    {
        if (!Auth::check()) {
            $this->showAuthWarning = $field;
        }
    }

    public function toggleViewMore()
    {
        $this->viewMore = !$this->viewMore;
    }

    public function render()
    {
        $similarPackages = Package::with(['country', 'city', 'area', 'rooms', 'photos'])
            ->take(4)
            ->get();

        return view('livewire.user.package-show', [
            'package' => $this->package,
            'views' => $this->views,
            'similarPackages' => $similarPackages,
        ])->layout('layouts.guest');
    }
}