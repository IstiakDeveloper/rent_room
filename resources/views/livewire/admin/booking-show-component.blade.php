<div class="container my-5">
    <h1 class="mb-4">Booking Details</h1>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Booking Information</h5>
        </div>
        @if (session()->has('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6"><strong>ID:</strong> {{ $booking->id }}</div>
                <div class="col-md-6"><strong>User ID:</strong> {{ $booking->user_id }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><strong>Package ID:</strong> {{ $booking->package_id }}</div>
                <div class="col-md-6"><strong>Package Name:</strong> {{ $booking->package->name }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><strong>From Date:</strong> {{ $booking->from_date }}</div>
                <div class="col-md-6"><strong>To Date:</strong> {{ $booking->to_date }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><strong>Number of Days:</strong> {{ $booking->number_of_days }}</div>
                <div class="col-md-6"><strong>Price Type:</strong> {{ ucfirst($booking->price_type) }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><strong>Total Price:</strong> £{{ $booking->price + $booking->booking_price }}</div>
                <div class="col-md-6"><strong>Booking Price:</strong> £{{ $booking->booking_price }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><strong>Total Paid:</strong> <span class="text-success">£{{ $booking->total_amount }}</span></div>
                <div class="col-md-6"><strong>Payment Option:</strong> {{ ucfirst($booking->payment_option) }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><strong>Rents Status:</strong> <span class="{{ $booking->payment_status === 'paid' ? 'text-success' : 'text-danger' }}">{{ ucfirst($booking->payment_status) }}</span></div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="card-title mb-0">Payment Information</h5>
        </div>
        <div class="card-body">
            @if ($booking->payments->count() > 0)
                <ul class="list-unstyled">
                    @foreach ($booking->payments as $payment)
                        <li class="d-flex justify-content-between lh-22 mb-2">
                            <p class="text-gray-light mb-0">Payment Method:</p>
                            <p class="font-weight-500 text-heading mb-0">{{ ucfirst($payment->payment_method) }}</p>
                        </li>
                        <li class="d-flex justify-content-between lh-22 mb-2">
                            <p class="text-gray-light mb-0">Amount:</p>
                            <p class="font-weight-500 text-heading mb-0">£{{ $payment->amount }}</p>
                        </li>
                        <li class="d-flex justify-content-between lh-22 mb-2">
                            <p class="text-gray-light mb-0">Transaction ID:</p>
                            <p class="font-weight-500 text-heading mb-0">{{ $payment->transaction_id ?? 'N/A' }}</p>
                        </li>
                        <li class="d-flex justify-content-between lh-22 mb-2">
                            <p class="text-gray-light mb-0">Status:</p>
                            <p class="font-weight-500 text-heading mb-0 {{ $payment->status === 'completed' ? 'text-success' : 'text-warning' }}">{{ ucfirst($payment->status) }}</p>
                        </li>
                        <div class="d-flex justify-content-end">
                            @if($payment->status == 'pending')
                                <div>
                                    <button wire:click="approvePayment({{ $payment->id }})" class="btn btn-success btn-sm">Approve</button>
                                    <button wire:click="rejectPayment({{ $payment->id }})" class="btn btn-danger btn-sm">Reject</button>
                                </div>
                            @endif
                        </div>
                        <hr>
                    @endforeach
                </ul>
            @else
                <p class="text-danger">No payments made yet.</p>
            @endif

            <div class="mt-4 p-3 bg-light rounded">
                <h5 class="text-dark">Due Bill:</h5>
                <p class="font-weight-bold text-primary">£{{ $dueBill }}</p>
            </div>
        </div>

        <div class="card mt-4">
            {{-- <div class="card-header bg-light">
                <h5 class="card-title mb-0">Change Status</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="updateStatus">
                    <div class="form-group">
                        <label for="status">Select Status:</label>
                        <select wire:model="selectedStatus" class="form-control" id="status">
                            <option value="approve">Approve</option>
                            <option value="pending">Pending</option>
                            <option value="decline">Decline</option>
                        </select>
                        @error('selectedStatus') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
            </div> --}}
            <!-- Generate Invoice and Send Email Buttons -->
            <div class="card-body">
                <button wire:click="generateInvoice" class="btn btn-primary">Generate & Download Invoice</button>
                <button wire:click="sendInvoiceEmail" class="btn btn-secondary">Send Invoice Email</button>
            </div>
        </div>

        <!-- Cancellation Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="card-title mb-0">Cancellation</h5>
        </div>
        <div class="card-body">
            @php
                $currentDate = \Carbon\Carbon::today();
                $fromDate = \Carbon\Carbon::parse($booking->from_date);
            @endphp
            <button
                wire:click="cancelBooking"
                class="btn btn-danger"
                {{ $booking->payment_status === 'cancelled' ? 'disabled' : '' }}>
                Cancel Booking
            </button>
            @if ($fromDate->lessThan($currentDate))
                <div class="mt-4 p-3 text-danger rounded">
                    <p>You can only cancel a booking that is set to start today or in the future.</p>
                </div>
            @endif
            @if ($booking->payment_status === 'cancelled')
                <div class="mt-4 p-3 text-danger rounded">
                    <p>This booking has already been cancelled.</p>
                </div>
            @endif
        </div>

    </div>
</div>
