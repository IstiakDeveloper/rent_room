<?php

namespace App\Livewire\User;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Livewire\Component;
use Stripe\StripeClient;

class ShowBooking extends Component
{
    public $booking;
    public $payments;
    public $paymentsDue;
    public $dueBill;
    public $showPaymentModal = false;
    public $showRenewalModal = false;
    public $paymentMethod = 'cash'; // Default payment method
    public $bankTransferReference;
    public $bankDetails = 'Bank: ABC Bank, Account Number: 12345678, Sort Code: 12-34-56';
    public $newFromDate;
    public $newToDate;

    protected $rules = [
        'bankTransferReference' => 'required_if:paymentMethod,bank_transfer',
        'newFromDate' => 'required|date',
        'newToDate' => 'required|date|after_or_equal:newFromDate',
    ];

    public function mount($id)
    {
        $this->booking = Booking::findOrFail($id);
        

        $this->payments = Payment::where('booking_id', $id)->get();
        $this->paymentsDue = Payment::where('booking_id', $this->booking->id)
            ->where('status', '!=', 'rejected')
            ->get();
        // Calculate due bill
        $this->dueBill = $this->booking->price + $this->booking->booking_price - $this->paymentsDue->sum('amount');
    }

    public function render()
    {
        return view('livewire.user.show-booking');
    }

    public function cancelBooking()
    {
        // Update booking details
        $this->booking->update([
            'from_date' => null,
            'to_date' => null,
            'payment_status' => 'cancelled',
        ]);

        // Optionally, you may want to remove or handle associated payments here
        Payment::where('booking_id', $this->booking->id)->delete();

        session()->flash('success', 'Booking cancelled successfully!');

        return redirect()->route('user.bookings.index');
    }


    public function renewPackage()
    {
        // Validate the new dates
        $this->validate([
            'newFromDate' => 'required|date',
            'newToDate' => 'required|date|after_or_equal:newFromDate',
        ]);

        // Calculate the number of days for the new booking
        $number_of_days = $this->calculateNumberOfDays($this->newFromDate, $this->newToDate);

        // Create a new booking with updated dates
        $newBooking = $this->booking->replicate(); // Duplicate the booking
        $newBooking->from_date = $this->newFromDate;
        $newBooking->to_date = $this->newToDate;
        $newBooking->number_of_days = $number_of_days;

        // Calculate the price per day from the original booking
        $originalNumberOfDays = $this->booking->number_of_days;
        $singleDayPrice = $this->booking->price / $originalNumberOfDays;

        // Calculate the new price based on the new number of days
        $newBooking->price = $singleDayPrice * $number_of_days;

        // Set the payment status to pending
        $newBooking->payment_status = 'pending';

        // Save the new booking
        $newBooking->save();

        // Duplicate the rooms for the new booking
        if ($this->booking->rooms) { // Ensure the rooms relationship is not null
            foreach ($this->booking->rooms as $room) {
                $newBooking->rooms()->create([
                    'room_id' => $room->room_id,
                    'room_type' => $room->room_type,
                    'price' => $room->price, // Assuming the Room model has these fields
                    // Add any other fields as needed
                ]);
            }
        }

        $this->booking->payment_status = 'finished'; // Assuming the status should be updated
        $this->booking->save();

        flash()->success('Package renewed successfully!');

        return redirect()->route('bookings.show', ['id' => $newBooking->id]);
    }


    public function finishBooking()
    {
        $this->booking->update([
            'payment_status' => 'finished',
            'status' => 'finished', // Assuming you have a `status` field as well
        ]);

        session()->flash('success', 'Booking finished successfully!');

        return redirect()->route('bookings.show', ['id' => $this->booking->id]);
    }



    
    protected function calculateNumberOfDays($fromDate, $toDate)
    {
        $from = \Carbon\Carbon::parse($fromDate);
        $to = \Carbon\Carbon::parse($toDate);
        
        // Ensure the 'to' date is always after the 'from' date
        return $from->diffInDays($to) + 1;
    }
    

    public function showPaymentM()
    {
        // Fetch the latest booking details
        $this->booking = Booking::findOrFail($this->booking->id);
        
        // Fetch the latest payment details for the booking, excluding rejected payments
        $this->payments = Payment::where('booking_id', $this->booking->id)
                                ->where('status', '!=', 'rejected')
                                ->get();
                                
        // Calculate the due bill
        $this->dueBill = $this->booking->price + $this->booking->booking_price - $this->payments->sum('amount');
        
        // Show the payment modal
        $this->showPaymentModal = true;
    }

    public function showRenewModal()
    {
        // Fetch the latest booking details
        $this->booking = Booking::findOrFail($this->booking->id);
    
        // Compute the default dates
        $toDate = \Carbon\Carbon::parse($this->booking->to_date);
        $this->newFromDate = $toDate->addDay()->format('Y-m-d'); // Default to the day after the current toDate
        $this->newToDate = $toDate->addDay(2)->format('Y-m-d'); // Default to two days after the new from date
    
        // Show the renewal modal
        $this->showRenewalModal = true;
    }

    public function proceedPayment()
    {
        // $this->validate();

        $paymentAmount = $this->dueBill;

        if ($this->paymentMethod === 'card') {
            return $this->handleStripePayment($paymentAmount);
        } elseif ($this->paymentMethod === 'bank_transfer') {
            Payment::create([
                'booking_id' => $this->booking->id,
                'payment_method' => $this->paymentMethod,
                'amount' => $paymentAmount,
                'status' => 'pending',
                'transaction_id' => $this->bankTransferReference
            ]);

            $this->booking->payment_status = 'pending';
            $this->booking->save();

            flash()->success('Payment successful! Due bill paid.');
            $this->showPaymentModal = false;
    } elseif ($this->paymentMethod === 'cash') {
            Payment::create([
                'booking_id' => $this->booking->id,
                'payment_method' => $this->paymentMethod,
                'amount' => $paymentAmount,
                'status' => 'pending'
            ]);

            $this->booking->payment_status = 'pending';
            $this->booking->save();

            flash()->success('Payment successful! Due bill paid.');
            $this->showPaymentModal = false;
        }
    }

    protected function handleStripePayment($paymentAmount)
    {
        $stripe = new StripeClient(config('stripe.stripe_sk'));

        try {
            $session = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'gbp',
                        'product_data' => [
                            'name' => 'Booking Payment',
                        ],
                        'unit_amount' => $paymentAmount * 100, // Amount in cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('user.bookings.index') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('user.bookings.index'),
                'metadata' => [
                    'booking_id' => $this->booking->id,
                ],
            ]);

            return redirect($session->url);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return redirect()->back()->with('error', 'Stripe Error: ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        try {
            $stripe = new StripeClient(config('stripe.stripe_sk'));
            $checkout_session = $stripe->checkout->sessions->retrieve($sessionId);

            if ($checkout_session->payment_status === 'paid') {
                $bookingId = $checkout_session->metadata->booking_id;
                $booking = Booking::findOrFail($bookingId);
                $booking->payment_status = 'paid';
                $booking->save();

                flash()->success('Payment successful! Due bill paid.');
            } else {
                return redirect()->route('booking.cancel')->with('error', 'Payment unsuccessful.');
            }

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return redirect()->route('booking.cancel')->with('error', 'Stripe Error: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('booking.details', $this->booking->id)->with('error', 'Payment canceled.');
    }

}
