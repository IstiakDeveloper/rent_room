<div class="container my-5">
    <!-- Alert Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('message') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Main Content Column -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    <h5 class="mb-0 text-white">Booking Ref #{{ $booking->id }}</h5>
                </div>

                <div class="card-body">
                    <!-- Package Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded h-100">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-building mr-2"></i>Property Information
                                </h6>
                                <p class="mb-2">
                                    <strong class="text-muted">Name:</strong><br>
                                    {{ $booking->package->name }}
                                </p>
                                <p class="mb-0">
                                    <strong class="text-muted">Address:</strong><br>
                                    {{ $booking->package->address }}
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded h-100">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-calendar-alt mr-2"></i>Booking Period
                                </h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <strong class="text-muted">Check In:</strong>
                                    <span>{{ \Carbon\Carbon::parse($booking->from_date)->format('M d, Y') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <strong class="text-muted">Check Out:</strong>
                                    <span>{{ \Carbon\Carbon::parse($booking->to_date)->format('M d, Y') }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <strong class="text-muted">Duration:</strong>
                                    <span class="badge badge-primary text-white">{{ $booking->number_of_days }}
                                        Days</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description if exists -->
                    @if ($booking->package->description)
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle mr-2"></i>
                            {{ $booking->package->description }}
                        </div>
                    @endif

                    <!-- Booked Rooms -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-bed mr-2"></i>Booked Rooms
                        </h6>
                        <div class="row">
                            @foreach ($rooms as $room)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <h6 class="card-title d-flex justify-content-between align-items-center">
                                                {{ $room->name }}
                                                <span class="badge badge-info text-white">Room
                                                    {{ $loop->iteration }}</span>
                                            </h6>
                                            <div class="text-muted small mb-2">
                                                <span class="mr-3">
                                                    <i class="fas fa-bed mr-1"></i>
                                                    {{ $room->number_of_beds }} Beds
                                                </span>
                                                <span>
                                                    <i class="fas fa-bath mr-1"></i>
                                                    {{ $room->number_of_bathrooms }} Ensuite
                                                </span>
                                            </div>
                                            @if ($room->description)
                                                <p class="small text-muted mb-0">{{ $room->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Summary -->
                    <div class="bg-light p-4 rounded">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-calculator mr-2"></i>Price Summary
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Room Price:</span>
                                    <strong>£{{ number_format($booking->price, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Booking Fee:</span>
                                    <strong>£{{ number_format($booking->booking_price, 2) }}</strong>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between p-3 bg-white rounded">
                                    <span class="text-primary font-weight-bold">Total Amount:</span>
                                    <strong
                                        class="text-primary">£{{ number_format($booking->price + $booking->booking_price, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Payment Status Card -->
            <div class="card shadow-sm mb-4">
                <div
                    class="card-header bg-{{ $booking->payment_status === 'paid' ? 'success' : 'warning' }} text-white">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-chart-pie mr-2"></i>Payment Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @php
                            $statusClass = match ($booking->payment_status) {
                                'approved', 'paid' => 'success',
                                'pending' => 'warning',
                                'cancelled', 'rejected' => 'danger',
                                default => 'secondary',
                            };
                        @endphp
                        <span class="badge badge-{{ $statusClass }} text-white px-4 py-2">
                            <i class="fas fa-circle mr-1"></i>
                            {{ ucfirst($booking->payment_status) }}
                        </span>
                    </div>

                    <div class="bg-light p-3 rounded mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Paid:</span>
                            <strong class="text-success">£{{ number_format($booking->total_amount, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Due Amount:</span>
                            <strong class="text-danger">£{{ number_format($dueBill, 2) }}</strong>
                        </div>
                    </div>

                    @if (
                        $booking->payment_status !== 'cancelled' &&
                            $booking->payment_status !== 'approved' &&
                            $booking->payment_status !== 'rejected' &&
                            $booking->payment_status !== 'paid')
                        <div class="d-grid gap-2">
                            <button wire:click="approveBooking" class="btn btn-success text-white btn-block mb-2">
                                <i class="fas fa-check mr-2"></i>Approve Booking
                            </button>
                            <button wire:click="rejectBooking" class="btn btn-danger text-white btn-block">
                                <i class="fas fa-times mr-2"></i>Reject Booking
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment History -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-history mr-2"></i>Payment History
                    </h5>
                </div>
                <div class="card-body">
                    @if ($booking->payments->count() > 0)
                        @foreach ($booking->payments as $payment)
                            <div class="border-bottom mb-3 pb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span
                                        class="badge badge-{{ $payment->status === 'completed' ? 'success' : 'warning' }} text-white">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                    <strong>£{{ number_format($payment->amount, 2) }}</strong>
                                </div>
                                <div class="small text-muted">
                                    <span class="mr-3">
                                        <i class="fas fa-credit-card mr-1"></i>
                                        {{ ucfirst($payment->payment_method) }}
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $payment->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-receipt fa-2x mb-2"></i>
                            <p class="mb-0">No payments recorded yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cancellation Card -->
            @if ($booking->payment_status !== 'cancelled')
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-ban mr-2"></i>Cancel Booking
                        </h5>
                    </div>
                    <div class="card-body">
                        <button wire:click="cancelBooking" class="btn btn-danger text-white btn-block">
                            <i class="fas fa-times mr-2"></i>Cancel This Booking
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .card {
            border: none;
            border-radius: 0.5rem;
        }

        .card-header {
            border-top-left-radius: 0.5rem !important;
            border-top-right-radius: 0.5rem !important;
        }

        .btn {
            border-radius: 0.25rem;
            padding: 0.5rem 1rem;
        }

        .badge {
            font-weight: 500;
            padding: 0.5em 0.75em;
        }

        .alert {
            border: none;
            border-radius: 0.5rem;
        }

        .rounded {
            border-radius: 0.5rem !important;
        }
    </style>
</div>
