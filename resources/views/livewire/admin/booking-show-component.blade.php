<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <button wire:click="generateInvoice" class="btn btn-primary">
                <i class="fas fa-file-pdf mr-2"></i>Download Invoice
            </button>
            <button wire:click="sendInvoiceEmail" class="btn btn-secondary ms-2">
                <i class="fas fa-envelope mr-2"></i>Email Invoice
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Booking Information -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle mr-2"></i>Booking Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Booking ID</small>
                                <strong>{{ $booking->id }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Package</small>
                                <strong>{{ $booking->package->name }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Check In</small>
                                <strong>{{ \Carbon\Carbon::parse($booking->from_date)->format('d M Y') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Check Out</small>
                                <strong>{{ \Carbon\Carbon::parse($booking->to_date)->format('d M Y') }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Booked Rooms Section -->
                    <div class="mt-4">
                        <h6 class="mb-3">Booked Rooms</h6>
                        <div class="row g-3">
                            @php
                                $roomIds = json_decode($booking->room_ids, true) ?? [];
                                $rooms = \App\Models\Room::whereIn('id', $roomIds)->get();
                            @endphp
                            @foreach ($rooms as $room)
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $room->name }}</h6>
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">Type: {{ $room->type }}</small>
                                                <span class="badge bg-info">{{ $booking->price_type }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Summary -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="mb-3">Price Summary</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Room Price:</span>
                            <strong>£{{ number_format($booking->price, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Booking Fee:</span>
                            <strong>£{{ number_format($booking->booking_price, 2) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Total Amount:</span>
                            <strong
                                class="text-primary">£{{ number_format($booking->price + $booking->booking_price, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Information Sidebar -->
        <div class="col-md-4">
            <!-- Payment Status Card -->
            <div class="card shadow-sm mb-4">
                <div
                    class="card-header bg-{{ $booking->payment_status === 'paid' ? 'success' : 'warning' }} text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-credit-card mr-2"></i>Payment Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @php
                            $statusColor = match ($booking->payment_status) {
                                'approved', 'paid' => 'success',
                                'pending' => 'warning',
                                'cancelled' => 'danger',
                                'rejected' => 'danger',
                                default => 'secondary',
                            };
                        @endphp
                        <span class="badge bg-{{ $statusColor }} p-2">
                            {{ ucfirst($booking->payment_status) }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Paid:</span>
                        <strong class="text-success">£{{ number_format($booking->total_amount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Due Amount:</span>
                        <strong class="text-danger">£{{ number_format($dueBill, 2) }}</strong>
                    </div>

                    @if (
                        $booking->payment_status !== 'cancelled' &&
                            $booking->payment_status !== 'approved' &&
                            $booking->payment_status !== 'rejected')
                        <div class="d-grid gap-2">
                            <button wire:click="approveBooking" class="btn btn-success">
                                <i class="fas fa-check mr-2"></i>Approve
                            </button>
                            <button wire:click="rejectBooking" class="btn btn-danger">
                                <i class="fas fa-times mr-2"></i>Reject
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment History -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history mr-2"></i>Payment History
                    </h5>
                </div>
                <div class="card-body">
                    @if ($booking->payments->count() > 0)
                        @foreach ($booking->payments as $payment)
                            <div class="border-bottom mb-3 pb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Amount</span>
                                    <strong>£{{ number_format($payment->amount, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Method</span>
                                    <span>{{ ucfirst($payment->payment_method) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Status</span>
                                    <span
                                        class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>
                                @if ($payment->status == 'pending')
                                    <div class="d-flex justify-content-end gap-2 mt-2">
                                        <button wire:click="approvePayment({{ $payment->id }})"
                                            class="btn btn-success btn-sm">
                                            <i class="fas fa-check mr-1"></i>Approve
                                        </button>
                                        <button wire:click="rejectPayment({{ $payment->id }})"
                                            class="btn btn-danger btn-sm">
                                            <i class="fas fa-times mr-1"></i>Reject
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">No payments recorded yet.</p>
                    @endif
                </div>
            </div>

            <!-- Cancellation Card -->
            @if ($booking->payment_status !== 'cancelled')
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-ban mr-2"></i>Cancellation
                        </h5>
                    </div>
                    <div class="card-body">
                        <button wire:click="cancelBooking" class="btn btn-danger w-100"
                            {{ \Carbon\Carbon::parse($booking->from_date)->isPast() ? 'disabled' : '' }}>
                            <i class="fas fa-times mr-2"></i>Cancel Booking
                        </button>
                        @if (\Carbon\Carbon::parse($booking->from_date)->isPast())
                            <small class="text-muted d-block mt-2">
                                Past bookings cannot be cancelled.
                            </small>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
