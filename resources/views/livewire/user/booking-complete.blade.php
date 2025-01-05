<div>
    <!-- resources/views/livewire/user/booking-complete.blade.php -->
    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="bg-light rounded-lg shadow-sm p-3 mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="/" class="text-decoration-none">
                        <i class="fas fa-home text-primary"></i>
                        <span class="ms-1">Home</span>
                    </a>
                </li>
                <li class="breadcrumb-item active">Booking Confirmation</li>
            </ol>
        </nav>

        <!-- Success Message with Booking Reference -->
        <div class="card border-0 shadow-sm mb-4 bg-gradient bg-success text-white">
            <div class="card-body text-center py-4">
                <div class="circle-check mb-3">
                    <i class="fas fa-check-circle fa-4x"></i>
                </div>
                <h2 class="mb-2">Booking Confirmed!</h2>
                <p class="mb-2">Thank you for choosing Rent & Rooms. Your booking has been confirmed.</p>
                <div class="booking-reference mt-3 py-2 px-4 bg-white bg-opacity-25 rounded-pill d-inline-block">
                    <span class="text-dark">Booking Reference: </span>
                    <strong class="ms-2 text-dark">#{{ $booking->id }}</strong>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Booking Details -->
            <div class="col-lg-8">
                <!-- Package and Room Details Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle text-primary mr-2"></i>
                            <h4 class="mb-0">Booking Information</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <!-- Package Details -->
                            <div class="col-md-6">
                                <div class="bg-light p-4 rounded-3 h-100">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-box-open mr-2"></i>Package Details
                                    </h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-3">
                                            <small class="text-muted d-block">Package Name</small>
                                            <strong>{{ $booking->package->name }}</strong>
                                        </li>
                                        <li class="mb-3">
                                            <small class="text-muted d-block">Duration</small>
                                            <strong>{{ $booking->number_of_days }} Days</strong>
                                        </li>
                                        <li>
                                            <small class="text-muted d-block">Property Address</small>
                                            <strong>{{ $booking->package->address }}</strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Room Information -->
                            <div class="col-md-6">
                                <div class="bg-light p-4 rounded-3 h-100">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-bed mr-2"></i>Room Information
                                    </h6>
                                    @php
                                        $roomIds = json_decode($booking->room_ids, true) ?? [];
                                        $rooms = \App\Models\Room::whereIn('id', $roomIds)->get();
                                    @endphp
                                    @foreach ($rooms as $room)
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="room-icon mr-3">
                                                <i class="fas fa-door-open text-primary"></i>
                                            </div>
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
                                <div class="bg-light p-4 rounded-3">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-calendar mr-2"></i>Stay Duration
                                    </h6>
                                    <div class="row align-items-center">
                                        <div class="col-md-5">
                                            <div class="text-center p-3 border border-success rounded-3 bg-white">
                                                <small class="text-muted d-block mb-1">Check In</small>
                                                <strong class="text-success d-block">
                                                    <i class="fas fa-calendar-check mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($booking->from_date)->format('D, d M Y') }}
                                                </strong>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center py-2">
                                            <i class="fas fa-arrow-right text-muted"></i>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="text-center p-3 border border-danger rounded-3 bg-white">
                                                <small class="text-muted d-block mb-1">Check Out</small>
                                                <strong class="text-danger d-block">
                                                    <i class="fas fa-calendar-times mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($booking->to_date)->format('D, d M Y') }}
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between gap-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary px-4">
                        <i class="fas fa-tachometer-alt mr-2"></i>View Dashboard
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4">
                        <i class="fas fa-home mr-2"></i>Return Home
                    </a>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 2rem;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-receipt text-primary mr-2"></i>
                            <h4 class="mb-0">Payment Summary</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Cost Breakdown -->
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Room Price</span>
                                <strong>£{{ number_format($booking->price, 2) }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Booking Fee</span>
                                <strong>£{{ number_format($booking->booking_price, 2) }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 border-bottom">
                                <span class="text-muted">Total Amount</span>
                                <strong
                                    class="text-primary">£{{ number_format($booking->price + $booking->booking_price, 2) }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Amount Paid</span>
                                <strong class="text-success">£{{ number_format($booking->total_amount, 2) }}</strong>
                            </li>
                            @if ($booking->price + $booking->booking_price - $booking->total_amount > 0)
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted">Remaining Balance</span>
                                    <strong
                                        class="text-danger">£{{ number_format($booking->price + $booking->booking_price - $booking->total_amount, 2) }}</strong>
                                </li>
                            @endif
                        </ul>

                        <!-- Payment Details -->
                        <div class="payment-details bg-light p-4 rounded-3">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-credit-card mr-2"></i>Payment Details
                            </h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3">
                                    <small class="text-muted d-block">Payment Method</small>
                                    <strong>{{ ucfirst($payment->payment_method) }}</strong>
                                </li>
                                <li>
                                    <small class="text-muted d-block">Transaction ID</small>
                                    <strong class="text-primary">{{ $payment->transaction_id ?? 'N/A' }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this CSS to your styles -->
    <style>
        .circle-check {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .sticky-top {
            z-index: 1020;
        }

        .booking-reference {
            font-size: 1.1rem;
        }

        .room-icon {
            width: 40px;
            height: 40px;
            background: rgba(var(--bs-primary-rgb), 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</div>
