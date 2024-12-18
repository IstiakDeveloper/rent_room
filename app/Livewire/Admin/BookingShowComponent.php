<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use Livewire\Component;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use App\Models\Payment;

class BookingShowComponent extends Component
{

    public $booking;
    public $selectedStatus;
    public $bookingDue;
    public $paymentsDue;
    public $dueBill;

    protected $rules = [
        'selectedStatus' => 'required|in:approve,pending,decline',
    ];

    public function mount($id)
    {
        $this->booking = Booking::with(['package', 'payments'])->findOrFail($id);
        $this->bookingDue = Booking::findOrFail($id);
        $this->paymentsDue = Payment::where('booking_id', $this->booking->id)
        ->where('status', '!=', 'rejected')
        ->get();
        $this->dueBill = $this->bookingDue->price + $this->bookingDue->booking_price - $this->paymentsDue->sum('amount');
        $this->updateDueBill();

    }

    public function updateStatus()
    {
        $this->validate();

        $this->booking->update([
            'payment_status' => $this->selectedStatus,
        ]);

        flash()->success('Booking status updated successfully!');
    }

    public function approvePayment($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->update(['status' => 'completed']);
        $this->updateDueBill();

        $this->updateBookingPaymentStatus();
    }

    public function rejectPayment($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->update(['status' => 'rejected']);
        $this->updateDueBill();

        $this->updateBookingPaymentStatus();
    }

    public function cancelBooking()
    {
        if ($this->booking->payment_status === 'cancelled') {
            flash()->error('Booking is already cancelled.');
            return;
        }

        $this->booking->update(['payment_status' => 'cancelled']);
        flash()->success('Booking cancelled successfully!');
    }

    protected function updateBookingPaymentStatus()
    {
        $completedPayments = $this->booking->payments->where('status', 'completed')->count();
        $totalPayments = $this->booking->payments->count();

        if ($completedPayments === $totalPayments) {
            $this->booking->update(['payment_status' => 'Approved']);
        } elseif ($totalPayments > 0) {
            $this->booking->update(['payment_status' => 'pending']);
        } else {
            $this->booking->update(['payment_status' => 'unpaid']);
        }
    }

    protected function updateDueBill()
    {
        $totalPayments = $this->booking->payments->where('status', '!=', 'rejected')->sum('amount');
        $this->dueBill = $this->booking->price + $this->booking->booking_price - $totalPayments;
    }

    public function generateInvoice()
    {
        $data = ['booking' => $this->booking];
        $pdf = Pdf::loadView('invoice', $data);
        $fileName = 'invoice_' . $this->booking->id . '.pdf';
        $filePath = 'public/invoices/' . $fileName;

        if (!Storage::exists('public/invoices')) {
            Storage::makeDirectory('public/invoices');
        }

        Storage::put($filePath, $pdf->output());

        return response()->streamDownload(
            fn () => Storage::get($filePath),
            $fileName
        );
    }

    public function sendInvoiceEmail()
    {
        $data = ['booking' => $this->booking];
        $pdf = Pdf::loadView('invoice', $data);
        $fileName = 'invoice_' . $this->booking->id . '.pdf';
        $filePath = 'public/invoices/' . $fileName;

        if (!Storage::exists('public/invoices')) {
            Storage::makeDirectory('public/invoices');
        }

        Storage::put($filePath, $pdf->output());

        Mail::to($this->booking->user->email)->send(new InvoiceMail($this->booking, Storage::path($filePath)));

        flash()->success('Invoice sent to customer successfully!');
    }

    public function render()
    {
        return view('livewire.admin.booking-show-component');
    }

}
