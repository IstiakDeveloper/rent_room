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
    public $paymentMethod = 'bank_transfer'; // Default payment method
    public $bankTransferReference;
    public $bankDetails = 'Bank: ABC Bank, Account Number: 12345678, Sort Code: 12-34-56';
    public $newFromDate;
    public $newToDate;
    public $paymentPercentage;
    public $currentMilestone;

    protected $rules = [
        'bankTransferReference' => 'required_if:paymentMethod,bank_transfer',
        'newFromDate' => 'required|date',
        'newToDate' => 'required|date|after_or_equal:newFromDate',
    ];

    public function mount($id)
    {
        $this->booking = Booking::with(['package', 'payments', 'bookingPayments'])->findOrFail($id);
        $this->payments = $this->booking->payments ?? collect(); // Initialize payments

        // Calculate payment summaries
        $totalPrice = (float) $this->booking->price + (float) $this->booking->booking_price;
        $totalPaid = $this->payments->where('status', 'Paid')->sum('amount');
        $this->dueBill = $totalPrice - $totalPaid;
        $this->paymentPercentage = $totalPrice > 0 ? ($totalPaid / $totalPrice * 100) : 0;
    }

    public function render()
    {
        return view('livewire.user.show-booking');
    }

    public function showPaymentM()
    {
        try {
            // Calculate current milestone and due bill
            $this->calculatePayments();

            if (!$this->currentMilestone) {
                session()->flash('error', 'No pending payments found.');
                return;
            }

            $this->showPaymentModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error loading payment details: ' . $e->getMessage());
        }
    }

    public function calculatePayments()
    {
        try {
            // Refresh booking data
            $this->booking = Booking::with(['package', 'payments', 'bookingPayments'])
                ->findOrFail($this->booking->id);

            // Calculate totals
            $totalPrice = (float) $this->booking->price + (float) $this->booking->booking_price;
            $totalPaid = $this->booking->payments->where('status', 'Paid')->sum('amount');
            $this->dueBill = $totalPrice - $totalPaid;
            $this->paymentPercentage = $totalPrice > 0 ? ($totalPaid / $totalPrice * 100) : 0;

            // Get current milestone
            $this->currentMilestone = $this->booking->bookingPayments
                ->where('is_paid', false)
                ->sortBy('due_date')
                ->first();

            if (!$this->currentMilestone) {
                throw new \Exception('No pending payments found.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error calculating payments: ' . $e->getMessage());
            return null;
        }
    }

    public function proceedPayment()
    {
        try {
            // Validate inputs
            $this->validate([
                'paymentMethod' => 'required|in:card,bank_transfer',
                'bankTransferReference' => 'required_if:paymentMethod,bank_transfer',
            ], [
                'bankTransferReference.required_if' => 'Please enter the bank transfer reference number.'
            ]);

            if (!$this->currentMilestone) {
                throw new \Exception('No pending milestone found.');
            }

            // Handle payment based on method
            if ($this->paymentMethod === 'card') {
                return $this->handleStripePayment();
            } else {
                return $this->handleBankTransfer();
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', $e->getMessage());
            return null;
        } catch (\Exception $e) {
            session()->flash('error', 'Payment failed: ' . $e->getMessage());
            return null;
        }
    }

    protected function handleBankTransfer()
    {
        try {
            if (empty($this->bankTransferReference)) {
                throw new \Exception('Please enter a bank transfer reference.');
            }

            // Begin transaction
            \DB::beginTransaction();

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $this->booking->id,
                'payment_method' => 'bank_transfer',
                'amount' => $this->currentMilestone->amount,
                'transaction_id' => $this->bankTransferReference,
                'booking_payment_id' => $this->currentMilestone->id,
                'status' => 'pending',
            ]);

            // Update milestone status
            $this->currentMilestone->update([
                'status' => 'pending_bank_transfer'
            ]);

            // Update booking status
            $this->booking->update([
                'payment_status' => 'pending'
            ]);

            \DB::commit();

            session()->flash('success', 'Bank transfer initiated. Please contact admin with transfer details.');
            $this->showPaymentModal = false;
            $this->resetForm();

            return redirect()->route('bookings.show', ['id' => $this->booking->id]);

        } catch (\Exception $e) {
            \DB::rollBack();
            session()->flash('error', 'Failed to process bank transfer: ' . $e->getMessage());
            return null;
        }
    }

    protected function handleStripePayment()
    {
        try {
            $stripe = new StripeClient(config('stripe.stripe_sk'));

            // Begin transaction
            \DB::beginTransaction();

            $session = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'gbp',
                            'product_data' => [
                                'name' => "Booking Payment #" . $this->booking->id,
                                'description' => "Payment for " . $this->currentMilestone->milestone_type .
                                    " " . $this->currentMilestone->milestone_number,
                            ],
                            'unit_amount' => (int) ($this->currentMilestone->amount * 100),
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}&booking_id=' . $this->booking->id,
                'cancel_url' => route('payment.cancel') . '?booking_id=' . $this->booking->id,
                'metadata' => [
                    'booking_id' => $this->booking->id,
                    'booking_payment_id' => $this->currentMilestone->id,
                    'amount' => $this->currentMilestone->amount
                ],
            ]);

            // Create initial payment record
            Payment::create([
                'booking_id' => $this->booking->id,
                'payment_method' => 'card',
                'amount' => $this->currentMilestone->amount,
                'status' => 'pending',
                'booking_payment_id' => $this->currentMilestone->id,
                'stripe_session_id' => $session->id
            ]);

            // Update milestone status
            $this->currentMilestone->update([
                'status' => 'pending'
            ]);

            \DB::commit();

            return redirect($session->url);

        } catch (\Exception $e) {
            \DB::rollBack();
            session()->flash('error', 'Stripe payment failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    private function resetForm()
    {
        $this->paymentMethod = 'bank_transfer';
        $this->bankTransferReference = '';
        $this->resetValidation();
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
