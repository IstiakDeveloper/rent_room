<?php

namespace App\Livewire\Admin;

use App\Models\PaymentLink;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\BookingPayment;
use Livewire\Component;
use Stripe\StripeClient;

class PaymentComponent extends Component
{
    public $uniqueId;
    public $paymentLink;
    public $selectedPaymentMethod = "Card";
    public $bankReference;
    public $showModal = false;

    public function mount($uniqueId)
    {
        $this->uniqueId = $uniqueId;
        $this->paymentLink = PaymentLink::with([
            'user',
            'booking',
            'bookingPayment' // Add this
        ])->where('unique_id', $this->uniqueId)->firstOrFail();
    }

    public function showPaymentModal()
    {
        $this->showModal = true;
    }

    public function handlePaymentMethod()
    {
        if ($this->selectedPaymentMethod === 'Card') {
            return $this->handleStripePayment();
        } elseif ($this->selectedPaymentMethod === 'BankTransfer') {
            return $this->handleBankTransfer();
        }
    }

    protected function handleBankTransfer()
    {
        if (empty($this->bankReference)) {
            session()->flash('error', 'Please enter a reference number');
            return;
        }

        try {
            // Create payment record
            $bookingPayment = BookingPayment::where('booking_id', $this->paymentLink->booking_id)
                ->firstOrFail();

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $this->paymentLink->booking_id,
                'payment_method' => 'bank_transfer',
                'amount' => $this->paymentLink->amount, // Use amount from payment link
                'transaction_id' => $this->bankReference,
                'booking_payment_id' => $bookingPayment->id, // Use the found booking_payment_id
                'status' => 'pending',
            ]);

            // Update payment link status
            $this->paymentLink->update([
                'status' => 'pending_bank_transfer',
                'transaction_id' => $this->bankReference,
            ]);

            // Update booking payment status
            // $this->updateBookingPaymentStatus($this->paymentLink->booking_id);

            session()->flash('message', 'Bank transfer initiated. Please contact admin with transfer details.');
            return redirect()->route('payment.page', $this->paymentLink->unique_id);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to process bank transfer: ' . $e->getMessage());
            return null;
        }
    }

    protected function handleStripePayment()
    {
        $stripe = new StripeClient(config('stripe.stripe_sk'));

        try {
            $session = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'gbp',
                        'product_data' => [
                            'name' => "Booking Payment #" . $this->paymentLink->booking->id,
                            'description' => "Payment for booking",
                        ],
                        'unit_amount' => (int)($this->paymentLink->amount * 100), // Use amount from payment link
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}&payment_link=' . $this->uniqueId,
                'cancel_url' => route('payment.cancel') . '?payment_link=' . $this->uniqueId,
                'metadata' => [
                    'payment_link_id' => $this->paymentLink->id,
                    'booking_id' => $this->paymentLink->booking_id,
                    'amount' => $this->paymentLink->amount
                ],
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            session()->flash('error', 'Payment failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // protected function updateBookingPaymentStatus($bookingId)
    // {
    //     $booking = Booking::find($bookingId);
    //     if (!$booking) return;

    //     $totalPaid = $booking->payments()->where('status', 'success')->sum('amount');
    //     $totalAmount = $booking->price + $booking->booking_price;

    //     $status = 'pending';
    //     if ($totalPaid >= $totalAmount) {
    //         $status = 'paid';
    //     } elseif ($totalPaid > 0) {
    //         $status = 'partial';
    //     }

    //     $booking->update(['payment_status' => $status]);
    // }

    public function render()
    {
        return view('livewire.admin.payment-component', [
            'paymentLink' => $this->paymentLink
        ])->layout('layouts.guest');
    }
}
