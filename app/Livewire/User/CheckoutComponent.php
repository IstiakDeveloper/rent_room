<?php

namespace App\Livewire\User;

use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Room;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CheckoutComponent extends Component
{
    public $package;
    public $packageId;
    public $fromDate;
    public $toDate;
    public $name;
    public $email;
    public $phone;
    public $selectedRoom;
    public $roomDetails;
    public $totalNights;
    public $totalAmount = 0;
    public $bookingPrice = 0;
    public $amenitiesTotal = 0;
    public $maintainsTotal = 0;
    public $selectedMaintains = [];
    public $selectedAmenities = [];
    public $paymentOption = 'booking_only';
    public $paymentMethod = 'cash';
    public $bankTransferReference;
    public $showPaymentModal = false;
    public $bankDetails;

    public $priceType;
    public $priceBreakdown;

    protected $rules = [
        'paymentMethod' => 'required|in:cash,card,bank_transfer',
        'bankTransferReference' => 'required_if:paymentMethod,bank_transfer',
    ];

    public function mount()
    {
        $data = session()->get('checkout_data');
        if (!$data) {
            return redirect()->route('home')->with('error', 'No checkout data found.');
        }

        // Existing assignments
        $this->packageId = $data['packageId'];
        $this->fromDate = $data['fromDate'];
        $this->toDate = $data['toDate'];
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->phone = $data['phone'];
        $this->selectedRoom = Room::find($data['selectedRoom']);
        $this->selectedMaintains = $data['selectedMaintains'] ?? [];
        $this->selectedAmenities = $data['selectedAmenities'] ?? [];
        $this->totalNights = Carbon::parse($this->fromDate)->diffInDays(Carbon::parse($this->toDate));
        $this->package = Package::findOrFail($this->packageId);

        // New price breakdown handling
        $this->priceBreakdown = $data['priceBreakdown'] ?? [];
        $this->totalAmount = $data['roomTotal'];
        $this->bookingPrice = $data['roomDetails']['booking_price'];
        $this->amenitiesTotal = collect($this->selectedAmenities)->sum('price');
        $this->maintainsTotal = collect($this->selectedMaintains)->sum('price');
        $this->bankDetails = "Netsoftuk Solution A/C 17855008 S/C 04-06-05";
    }

    public function calculateTotalAmount()
    {
        $total = $this->totalAmount + $this->amenitiesTotal + $this->maintainsTotal;
        return $this->paymentOption === 'full' ? $total + $this->bookingPrice : $this->bookingPrice;
    }

    public function submitBooking()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $this->showPaymentModal = true;
    }

    public function proceedPayment()
    {
        $this->validate();

        $paymentAmount = $this->calculateTotalAmount();

        DB::beginTransaction();
        try {
            // Step 1: Create the booking
            $booking = $this->createBooking($paymentAmount);

            // Step 2: Create associated services (amenities and maintains)
            $this->createBookingServices($booking);

            // Step 3: Handle payment
            if ($this->paymentMethod === 'card') {
                DB::commit(); // Commit prior to redirect for Stripe
                return $this->handleStripePayment($booking, $paymentAmount);
            }

            // For non-card payments (bank transfer or cash)
            $this->createPayment($booking, $paymentAmount);

            DB::commit();

            // Step 4: Clear session and redirect
            session()->forget('checkout_data');
            session()->flash('success', 'Booking submitted successfully!');
            return redirect()->route('booking.complete', $booking->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error processing payment: ' . $e->getMessage());
            return redirect()->route('checkout');
        }
    }

    private function createBooking($paymentAmount)
    {
        // Calculate total milestones and milestone amount based on price type
        $priceBreakdown = session('checkout_data.priceBreakdown', []);
        $priceType = session('checkout_data.priceType', 'Day');

        // Count total milestones based on the breakdown
        $totalMilestones = collect($priceBreakdown)->count();

        // Calculate amount per milestone
        $milestoneAmount = collect($priceBreakdown)->pluck('total')->first() ?? 0;

        // Add a booking fee milestone (you can adjust this based on your logic)
        $bookingFeeMilestone = [
            'type' => 'Booking Fee',
            'total' => $this->bookingPrice, // Assuming the booking fee is the booking price
        ];

        // Add the booking fee milestone to the breakdown
        $priceBreakdown[] = $bookingFeeMilestone;
        $totalMilestones = count($priceBreakdown); // Update total milestones count

        // Create the booking
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'package_id' => $this->packageId,
            'from_date' => $this->fromDate,
            'to_date' => $this->toDate,
            'room_ids' => json_encode([$this->selectedRoom->id]),
            'number_of_days' => $this->totalNights,
            'price_type' => $priceType,
            'price' => $this->totalAmount,
            'booking_price' => $this->bookingPrice,
            'payment_option' => $this->paymentOption,
            'total_amount' => $paymentAmount,
            'payment_status' => 'pending',
            'total_milestones' => $totalMilestones,
            'milestone_amount' => $milestoneAmount,
            'milestone_breakdown' => $priceBreakdown
        ]);

        // Generate milestone payments
        $this->createMilestonePayments($booking, $priceBreakdown);

        return $booking;
    }


    private function createMilestonePayments($booking, $priceBreakdown)
    {
        $startDate = Carbon::parse($booking->from_date);

        foreach ($priceBreakdown as $index => $milestone) {
            $dueDate = match ($milestone['type']) {
                'Month' => $startDate->copy()->addMonths($index),
                'Week' => $startDate->copy()->addWeeks($index),
                'Day' => $startDate->copy()->addDays($index),
                'Booking Fee' => $startDate->copy()->addDays(0), // Booking Fee due immediately, adjust as needed
            };

            // Create booking payment record for the milestone
            DB::table('booking_payments')->insert([
                'booking_id' => $booking->id,
                'milestone_type' => $milestone['type'],
                'milestone_number' => $index + 1,
                'due_date' => $dueDate,
                'amount' => $milestone['total'],
                'payment_status' => $index === 0 ? 'pending' : 'pending', // First payment will be handled separately
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }


    private function createPayment($booking, $paymentAmount)
    {
        // Create the main payment record
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_method' => $this->paymentMethod,
            'amount' => $paymentAmount,
            'status' => 'pending',
            'transaction_id' => $this->bankTransferReference ?? null,
            'payment_option' => $this->paymentOption,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // If this is the booking payment, update the first milestone payment
        if ($this->paymentOption === 'booking_only') {
            DB::table('booking_payments')
                ->where('booking_id', $booking->id)
                ->where('milestone_number', 1)
                ->update([
                    'payment_status' => 'pending',
                    'payment_method' => $this->paymentMethod,
                    'transaction_reference' => $this->bankTransferReference ?? null,
                    'updated_at' => now()
                ]);
        }
        // If full payment, mark all milestones as pending payment
        else {
            DB::table('booking_payments')
                ->where('booking_id', $booking->id)
                ->update([
                    'payment_status' => 'pending',
                    'payment_method' => $this->paymentMethod,
                    'transaction_reference' => $this->bankTransferReference ?? null,
                    'updated_at' => now()
                ]);
        }

        return $payment;
    }

    // Add this method to handle successful payments
    private function updatePaymentStatus($booking, $paymentAmount)
    {
        if ($this->paymentOption === 'booking_only') {
            // Update only the first milestone
            DB::table('booking_payments')
                ->where('booking_id', $booking->id)
                ->where('milestone_number', 1)
                ->update([
                    'payment_status' => 'paid',
                    'paid_at' => now()
                ]);

            $booking->update(['payment_status' => 'partially_paid']);
        } else {
            // Update all milestones
            DB::table('booking_payments')
                ->where('booking_id', $booking->id)
                ->update([
                    'payment_status' => 'paid',
                    'paid_at' => now()
                ]);

            $booking->update(['payment_status' => 'paid']);
        }
    }

    private function createBookingServices($booking)
    {
        if (!empty($this->selectedAmenities)) {
            foreach ($this->selectedAmenities as $amenity) {
                DB::table('booking_amenities')->insert([
                    'booking_id' => $booking->id,
                    'amenity_id' => $amenity['id'],
                    'price' => $amenity['price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if (!empty($this->selectedMaintains)) {
            foreach ($this->selectedMaintains as $maintain) {
                DB::table('booking_maintains')->insert([
                    'booking_id' => $booking->id,
                    'maintain_id' => $maintain['id'],
                    'price' => $maintain['price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }


    protected function handleStripePayment($booking, $paymentAmount)
    {
        try {
            Stripe::setApiKey(config('stripe.stripe_sk'));

            $description = $this->paymentOption === 'booking_only'
                ? "Booking Payment for {$this->package->name}"
                : "Full Payment for {$this->package->name}";

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'gbp',
                        'product_data' => [
                            'name' => $description,
                            'description' => "Booking from {$this->fromDate} to {$this->toDate}",
                        ],
                        'unit_amount' => (int)($paymentAmount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('stripe.success', ['booking' => $booking->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.cancel', ['booking' => $booking->id]),
                'metadata' => [
                    'booking_id' => $booking->id,
                    'payment_option' => $this->paymentOption,
                ]
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            session()->flash('error', 'Stripe payment error: ' . $e->getMessage());
            return redirect()->route('checkout');
        }
    }

    public function render()
    {
        return view('livewire.user.checkout-component', [
            'paymentAmount' => $this->calculateTotalAmount()
        ])->layout('layouts.guest');
    }
}
