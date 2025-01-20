<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Package;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AdminBookingEditComponent extends Component
{
    public $booking;
    public $bookingId;
    public $package;
    public $packageId;
    public $selectedUser;
    public $searchQuery = '';
    public $users = [];
    public $fromDate;
    public $toDate;
    public $name;
    public $email;
    public $phone;
    public $selectedRooms = [];
    public $availableRooms = [];
    public $showCalendar = false;
    public $dateRange = '';
    public $selectedRoom;
    public $disabledDates = [];
    public $selectedDates = [];
    public $calendarView = false;
    public $roomPrices = [];
    public $totalAmount = 0;
    public $bookingPrice = 0;
    public $paymentOption = 'booking_only';
    public $paymentMethod = 'bank_transfer';
    public $bankTransferReference;
    public $priceType;
    public $packages;
    public $priceBreakdown = [];
    public $selectedRoomDetails;
    public $milestone_breakdown;
    public $total_milestones;
    public $milestone_amount;
    public $useCustomBookingFee = false;
    public $customBookingFee = 0;
    protected $listeners = ['dates-selected' => 'calculateTotals'];

    protected $rules = [
        'selectedUser' => 'required',
        'selectedRoom' => 'required',
        'fromDate' => 'required|date',
        'toDate' => 'required|date|after:fromDate',
        'phone' => 'required|string|max:15',
        'paymentMethod' => 'required|in:cash,card,bank_transfer',
        'bankTransferReference' => 'required_if:paymentMethod,bank_transfer',
        'customBookingFee' => 'required_if:useCustomBookingFee,true|numeric|min:0'
    ];

    protected $casts = [
        'bookingPrice' => 'float',
        'customBookingFee' => 'float',
        'totalAmount' => 'float',
    ];  

    public function mount(Booking $booking)
    {
        $this->booking = $booking->load(['user', 'package', 'payments']);
        $this->bookingId = $booking->id;

        // Initialize component properties with existing booking data
        $this->selectedUser = $this->booking->user;
        $this->packageId = $this->booking->package_id;
        $this->fromDate = $this->booking->from_date;
        $this->toDate = $this->booking->to_date;
        $this->phone = $this->booking->user->phone;
        $this->selectedRoom = json_decode($this->booking->room_ids)[0] ?? null;
        $this->paymentOption = $this->booking->payment_option;
        $this->totalAmount = $this->booking->price;
        $this->bookingPrice = $this->booking->booking_price;
        $this->customBookingFee = $this->bookingPrice;
        $this->priceType = $this->booking->price_type;
        $this->milestone_breakdown = $this->booking->milestone_breakdown;

        // Load packages for selection
        $this->packages = Package::with(['rooms.roomPrices'])->get();

        // Initialize calendar view and dates
        $this->calendarView = true;
        $this->fetchDisabledDates();

        // Remove current booking dates from disabled dates
        $this->disabledDates = array_filter($this->disabledDates, function ($date) {
            $checkDate = Carbon::parse($date);
            return !$checkDate->between(
                Carbon::parse($this->fromDate),
                Carbon::parse($this->toDate)
            );
        });

        $this->calculateTotals();
    }

    public function updatedUseCustomBookingFee($value)
    {
        if ($value) {
            $this->customBookingFee = $this->bookingPrice;
        } else {
            $room = Room::with('roomPrices')->find($this->selectedRoom);
            $this->bookingPrice = $room->roomPrices->first()?->booking_price ?? 0;
            $this->calculateTotals();
        }
    }

    public function updatedCustomBookingFee($value)
    {
        if ($this->useCustomBookingFee) {
            $this->bookingPrice = (float) $value;
            $this->calculateTotals();
        }
    }

    public function updateBooking()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            // Get price breakdown and determine price type
            $priceBreakdownData = $this->getPriceBreakdown();
            if (!empty($priceBreakdownData['breakdown'])) {
                $this->priceType = $priceBreakdownData['breakdown'][0]['type'];
            } else {
                throw new \Exception("Unable to determine price type");
            }

            // Calculate payment amount
            $paymentAmount = $this->paymentOption === 'full'
                ? $this->totalAmount + $this->bookingPrice
                : $this->bookingPrice;

            // Update the booking
            $this->booking->update([
                'user_id' => $this->selectedUser->id,
                'package_id' => $this->packageId,
                'from_date' => $this->fromDate,
                'to_date' => $this->toDate,
                'room_ids' => json_encode([$this->selectedRoom]),
                'number_of_days' => Carbon::parse($this->fromDate)->diffInDays(Carbon::parse($this->toDate)),
                'price_type' => $this->priceType,
                'price' => $this->totalAmount,
                'booking_price' => $this->useCustomBookingFee ? $this->customBookingFee : $this->bookingPrice,
                'payment_option' => $this->paymentOption,
                'total_amount' => $paymentAmount,
                'total_milestones' => count($priceBreakdownData['breakdown']),
                'milestone_amount' => collect($priceBreakdownData['breakdown'])->first()['total'] ?? 0,
                'milestone_breakdown' => $priceBreakdownData['breakdown']
            ]);

            // Update booking room prices
            $this->updateBookingRoomPrices();

            // Update milestone payments
            $this->updateMilestonePayments();

            DB::commit();
            session()->flash('success', 'Booking updated successfully!');
            return redirect()->route('admin.bookings.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error updating booking: ' . $e->getMessage());
        }
    }

    private function updateBookingRoomPrices()
    {
        // Delete existing room prices
        DB::table('booking_room_prices')
            ->where('booking_id', $this->booking->id)
            ->delete();

        // Create new room prices
        $room = Room::with('roomPrices')->find($this->selectedRoom);
        $priceType = $this->priceType ?? 'Day';

        $roomPrice = $room->roomPrices->first(function ($price) use ($priceType) {
            return $price->type === $priceType;
        });

        if ($roomPrice) {
            DB::table('booking_room_prices')->insert([
                'booking_id' => $this->booking->id,
                'room_id' => $this->selectedRoom,
                'price_type' => $priceType,
                'fixed_price' => $roomPrice->fixed_price,
                'discount_price' => $roomPrice->discount_price,
                'booking_price' => $roomPrice->booking_price,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    private function updateMilestonePayments()
    {
        // Delete existing milestone payments
        DB::table('booking_payments')
            ->where('booking_id', $this->booking->id)
            ->delete();

        $startDate = Carbon::parse($this->booking->from_date);
        $bookingFee = $this->booking->booking_price;

        // Insert booking fee payment
        DB::table('booking_payments')->insert([
            'booking_id' => $this->booking->id,
            'milestone_type' => 'Booking Fee',
            'milestone_number' => 0,
            'due_date' => $startDate,
            'amount' => $bookingFee,
            'payment_status' => 'pending',
            'payment_method' => $this->paymentMethod,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create milestone payments
        foreach ($this->priceBreakdown as $index => $milestone) {
            $dueDate = match ($milestone['type']) {
                'Month' => $startDate->copy()->addMonths($index + 1),
                'Week' => $startDate->copy()->addWeeks($index + 1),
                'Day' => $startDate->copy()->addDays($index + 1)
            };

            DB::table('booking_payments')->insert([
                'booking_id' => $this->booking->id,
                'milestone_type' => $milestone['type'],
                'milestone_number' => $index + 1,
                'due_date' => $dueDate,
                'amount' => $milestone['total'],
                'payment_status' => 'pending',
                'payment_method' => $this->paymentMethod,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }



    public function calculateTotals()
    {
        if ($this->selectedRoom && $this->fromDate && $this->toDate) {
            $this->calculateRoomTotal();
        }
    }
    public function updatedSearchQuery()
    {
        if (strlen($this->searchQuery) >= 2) {
            $this->users = User::where('name', 'like', '%' . $this->searchQuery . '%')
                ->orWhere('email', 'like', '%' . $this->searchQuery . '%')
                ->take(5)
                ->get();
        }
    }

    public function selectUser($userId)
    {
        $this->selectedUser = User::find($userId);
        $this->searchQuery = '';
        $this->users = [];
    }

    public function selectRoom($roomId)
    {
        $this->selectedRoom = $roomId;
        $this->calendarView = true;

        $room = Room::with('roomPrices')->find($roomId);
        $this->roomPrices[$roomId] = $room->roomPrices->groupBy('type')->map(function ($prices) {
            return $prices->first();
        })->toArray();

        // Set packageId for the selected room
        $this->packageId = $room->package_id;

        // Reset dates when room changes
        $this->fromDate = null;
        $this->toDate = null;
        $this->totalAmount = 0;

        // Reset booking fee to default when room changes
        $this->useCustomBookingFee = false;
        $this->bookingPrice = $room->roomPrices->first()?->booking_price ?? 0;
        $this->customBookingFee = $this->bookingPrice;

        $this->disabledDates = $this->fetchDisabledDates();
    }

    public function selectDates($dates)
    {
        $this->fromDate = $dates['start'];
        $this->toDate = $dates['end'];

        if ($this->validateDateRange()) {
            $this->calculateRoomTotal();

            if ($this->selectedUser && empty($this->phone)) {
                $this->phone = $this->selectedUser->phone;
            }
        }
    }

    private function calculateRoomTotal()
    {
        if (!$this->selectedRoom || !$this->fromDate || !$this->toDate) {
            return 0;
        }

        $room = Room::with('roomPrices')->find($this->selectedRoom);
        if (!$room) return 0;

        // Get price breakdown
        $priceBreakdownData = $this->getPriceBreakdown();

        // Set price type from the first milestone
        if (!empty($priceBreakdownData['breakdown'])) {
            $this->priceType = $priceBreakdownData['breakdown'][0]['type'];
        }

        $this->priceBreakdown = $priceBreakdownData['breakdown'];
        $this->totalAmount = $priceBreakdownData['total'];

        // Handle booking price based on custom fee setting
        if (!$this->useCustomBookingFee) {
            $this->bookingPrice = $room->roomPrices->first()?->booking_price ?? 0;
        }
        // If using custom fee, bookingPrice is already set through customBookingFee

        return $this->totalAmount;
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
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        // Get total days
        $totalDays = $startDate->diffInDays($endDate);

        // Get days in first month
        $daysInFirstMonth = $startDate->daysInMonth;

        // If total days is less than or equal to days in first month, return 1 month
        if ($totalDays <= $daysInFirstMonth) {
            return [
                'Month' => 1,
                'Week' => 0,
                'Day' => 0
            ];
        }

        // If days exceed first month, calculate how many months are needed
        // Example: 35 days with 31 days in month = 2 months (31 + 4 days = 2 months)
        $extraDays = $totalDays - $daysInFirstMonth;
        if ($extraDays > 0) {
            // Add 1 for first month plus any additional months needed
            $months = 1 + ceil($extraDays / $endDate->daysInMonth);
        } else {
            $months = 1;
        }

        return [
            'Month' => (int)$months,
            'Week' => 0,
            'Day' => 0
        ];
    }

    private function calculateWeeklyBreakdown($totalDays)
    {
        $fullWeeks = ceil($totalDays / 7);

        return [
            'Month' => 0,
            'Week' => $fullWeeks,
            'Day' => 0
        ];
    }

    public function render()
    {
        return view('livewire.admin.admin-booking-edit-component');
    }
}
