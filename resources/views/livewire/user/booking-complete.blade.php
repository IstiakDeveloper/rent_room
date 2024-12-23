<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="bg-light p-3 rounded mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i> Home</a></li>
            <li class="breadcrumb-item active">Payment Completed</li>
        </ol>
    </nav>

    <!-- Success Message -->
    <div class="card border-0 shadow-sm mb-4 bg-success text-white">
        <div class="card-body text-center py-4">
            <i class="fas fa-check-circle fa-3x mb-3"></i>
            <h2 class="mb-2">Payment Completed Successfully!</h2>
            <p class="mb-0">Thank you for your booking. Your transaction has been completed.</p>
        </div>
    </div>

    <div class="row">
        <!-- Booking Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Booking Details</h4>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Package Info -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-3">Package Information</h6>
                                <div class="mb-2">
                                    <small class="text-muted d-block">Package Name</small>
                                    <strong>{{ $booking->package->name }}</strong>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block">Duration</small>
                                    <strong>{{ $booking->number_of_days }} Days</strong>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Price Type</small>
                                    <span class="badge bg-info">{{ ucfirst($booking->price_type) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Room Details -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-3">Booked Rooms</h6>
                                @php
                                    $roomIds = json_decode($booking->room_ids, true) ?? [];
                                    $rooms = \App\Models\Room::whereIn('id', $roomIds)->get();
                                @endphp
                                @foreach($rooms as $room)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-bed text-primary mr-2"></i>
                                        <div>
                                            <strong class="d-block">{{ $room->name }}</strong>
                                            <small class="text-muted">{{ $room->type }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Stay Period -->
                        <div class="col-12">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-3">Stay Period</h6>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <small class="text-muted d-block">Check In</small>
                                        <strong class="text-success">
                                            <i class="fas fa-calendar-check mr-1"></i>
                                            {{ \Carbon\Carbon::parse($booking->from_date)->format('d M Y') }}
                                        </strong>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Check Out</small>
                                        <strong class="text-danger">
                                            <i class="fas fa-calendar-times mr-1"></i>
                                            {{ \Carbon\Carbon::parse($booking->to_date)->format('d M Y') }}
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-tachometer-alt mr-2"></i>Go to Dashboard
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-home mr-2"></i>Back to Home
                </a>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0"><i class="fas fa-receipt mr-2"></i>Payment Summary</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Room Price</span>
                            <strong>£{{ number_format($booking->price, 2) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Booking Fee</span>
                            <strong>£{{ number_format($booking->booking_price, 2) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Total Amount</span>
                            <strong class="text-primary">£{{ number_format($booking->price + $booking->booking_price, 2) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Amount Paid</span>
                            <strong class="text-success">£{{ number_format($booking->total_amount, 2) }}</strong>
                        </li>
                        @if(($booking->price + $booking->booking_price - $booking->total_amount) > 0)
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Due Amount</span>
                                <strong class="text-danger">£{{ number_format($booking->price + $booking->booking_price - $booking->total_amount, 2) }}</strong>
                            </li>
                        @endif
                    </ul>

                    <div class="mt-4">
                        <h6 class="text-muted mb-3">Payment Information</h6>
                        <div class="bg-light p-3 rounded">
                            <div class="mb-2">
                                <small class="text-muted d-block">Payment Method</small>
                                <strong><i class="fas fa-credit-card mr-2"></i>{{ ucfirst($payment->payment_method) }}</strong>
                            </div>
                            <div>
                                <small class="text-muted d-block">Transaction ID</small>
                                <strong class="text-primary">{{ $payment->transaction_id ?? 'N/A' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
