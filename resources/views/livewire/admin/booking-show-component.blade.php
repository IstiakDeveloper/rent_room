<div class="container my-4">
    <!-- Alert Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>{{ session('message') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Booking Header Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h5 class="mb-0">Booking #{{ $booking->id }}</h5>
                        <span class="badge text-white" style="background-color: {{ $this->statusColor }};">
                            {{ ucfirst($booking->payment_status) }}
                        </span>
                    </div>
                    <div class="text-right">
                        <strong
                            class="d-block">£{{ number_format($booking->price + $booking->booking_price, 2) }}</strong>
                        <small class="text-muted">Total Amount</small>
                    </div>
                </div>

                <!-- Quick Info Badges -->
                <div class="card-body border-bottom">
                    <div class="row no-gutters text-center">
                        <div class="col-4 px-2">
                            <div class="border rounded p-3">
                                <i class="fas fa-calendar-alt text-primary mb-2"></i>
                                <div class="small text-muted">Duration</div>
                                <strong>{{ $booking->number_of_days }} Days</strong>
                            </div>
                        </div>
                        <div class="col-4 px-2">
                            <div class="border rounded p-3">
                                <i class="fas fa-bed text-primary mb-2"></i>
                                <div class="small text-muted">Rooms</div>
                                <strong>{{ count($rooms) }}</strong>
                            </div>
                        </div>
                        <div class="col-4 px-2">
                            <div class="border rounded p-3">
                                <i class="fas fa-pound-sign text-primary mb-2"></i>
                                <div class="small text-muted">Due Amount</div>
                                <strong>£{{ number_format($dueBill, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="card-body border-bottom">
                    <h6 class="text-primary mb-3">Customer Information</h6>
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="rounded-circle bg-light p-3">
                                <i class="fas fa-user-circle fa-2x text-muted"></i>
                            </div>
                        </div>
                        <div class="col">
                            <h6 class="mb-1">{{ $booking->user->name }}</h6>
                            <div class="small text-muted">
                                <i class="fas fa-envelope mr-1"></i>{{ $booking->user->email }}
                                @if ($booking->user->phone)
                                    <span class="mx-2">|</span>
                                    <i class="fas fa-phone mr-1"></i>{{ $booking->user->phone }}
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-sm btn-outline-secondary"
                                onclick="window.location.href='mailto:{{ $booking->user->email }}'">
                                Contact
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stay Details -->
                <div class="card-body border-bottom">
                    <h6 class="text-primary mb-3">Stay Details</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="bg-light p-3 rounded">
                                <div class="mb-2">
                                    <small class="text-muted">Check In</small>
                                    <div class="font-weight-bold">
                                        {{ \Carbon\Carbon::parse($booking->from_date)->format('D, M d, Y') }}
                                    </div>
                                </div>
                                <div>
                                    <small class="text-muted">Check Out</small>
                                    <div class="font-weight-bold">
                                        {{ \Carbon\Carbon::parse($booking->to_date)->format('D, M d, Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="bg-light p-3 rounded">
                                <div class="mb-2">
                                    <small class="text-muted">Property</small>
                                    <div class="font-weight-bold">{{ $booking->package->name }}</div>
                                </div>
                                <div>
                                    <small class="text-muted">Location</small>
                                    <div class="font-weight-bold">{{ $booking->package->address }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="bg-light p-3 rounded">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clock text-primary mr-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Booking Created</small>
                                        <div class="font-weight-bold">
                                            {{ $booking->created_at->format('D, M d, Y \a\t h:i A') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booked Rooms -->
                <div class="card-body">
                    <h6 class="text-primary mb-3">Booked Rooms</h6>
                    <div class="row">
                        @foreach ($rooms as $room)
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">{{ $room->name }}</h6>
                                        <span class="badge badge-light">Room {{ $loop->iteration }}</span>
                                    </div>
                                    <div class="small text-muted">
                                        {{ $room->number_of_beds }} Beds • {{ $room->number_of_bathrooms }} Bath
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Payment Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <!-- Payment Summary -->
                    <div class="border-bottom pb-3 mb-3">
                        <h6 class="text-primary mb-3">Payment Summary</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Room Price</span>
                            <strong>£{{ number_format($booking->price, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Booking Fee</span>
                            <strong>£{{ number_format($booking->booking_price, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Paid Amount</span>
                            <strong class="text-success">£{{ number_format($booking->total_amount, 2) }}</strong>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @if (
                        $booking->payment_status !== 'cancelled' &&
                            $booking->payment_status !== 'approved' &&
                            $booking->payment_status !== 'rejected' &&
                            $booking->payment_status !== 'paid')
                        <div class="d-flex flex-column">
                            <button wire:click="approveBooking" class="btn text-white mb-2"
                                style="background-color: #252525;">
                                Approve Booking
                            </button>
                            <button wire:click="rejectBooking" class="btn text-white"
                                style="background-color: #404040;">
                                Reject Booking
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment History -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="text-primary mb-3">Payment History</h6>
                    @if ($booking->payments->count() > 0)
                        @foreach ($booking->payments as $payment)
                            <div class="border-bottom mb-3 pb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge"
                                        style="background-color: {{ $payment->status === 'completed' ? '#252525' : '#404040' }};">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                    <strong>£{{ number_format($payment->amount, 2) }}</strong>
                                </div>
                                <div class="small text-muted">
                                    {{ ucfirst($payment->payment_method) }} •
                                    {{ $payment->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-3">No payments recorded</div>
                    @endif
                </div>
            </div>

            <!-- Auto-Renewal Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-primary mb-0">Auto-Renewal Status</h6>
                        <button wire:click="toggleAutoRenewal"
                            class="btn btn-sm {{ $booking->auto_renewal ? 'btn-danger' : 'btn-success' }}"
                            {{ $canManageAutoRenewal ? '' : 'disabled' }}>
                            {{ $booking->auto_renewal ? 'Disable' : 'Enable' }} Auto-Renewal
                        </button>
                    </div>

                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex align-items-center mb-2">
                            @if ($booking->auto_renewal)
                                <span class="badge text-white mr-2" style="background-color: #252525;">
                                    <i class="fas fa-check-circle"></i> Enabled
                                </span>
                            @else
                                <span class="badge text-white mr-2" style="background-color: #808080;">
                                    <i class="fas fa-times-circle"></i> Disabled
                                </span>
                            @endif
                        </div>

                        @if ($booking->auto_renewal)
                            <div class="small text-muted mb-2">
                                Renews every {{ $booking->renewal_period_days }} days
                            </div>

                            @if ($booking->next_renewal_date)
                                <div class="bg-light p-2 rounded">
                                    <div class="small">
                                        <i class="fas fa-calendar-alt text-primary mr-1"></i>
                                        Next Renewal:
                                        <strong>
                                            {{ $booking->next_renewal_date->format('M d, Y') }}
                                            <small class="text-muted">
                                                ({{ $booking->next_renewal_date->diffForHumans() }})
                                            </small>
                                        </strong>
                                    </div>
                                </div>
                            @endif

                            <!-- Renewal Period Update -->
                            <div class="mt-3">
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control" wire:model.defer="renewalPeriodDays"
                                        placeholder="Days" min="1" max="365">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary" wire:click="updateRenewalPeriod"
                                            type="button">
                                            Update Period
                                        </button>
                                    </div>
                                </div>
                                @error('renewalPeriodDays')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        @endif
                    </div>

                    @if (!$canManageAutoRenewal && !$booking->auto_renewal)
                        <div class="alert alert-warning mb-0">
                            <small>
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Auto-renewal cannot be managed because:
                                <ul class="mb-0 mt-1">
                                    @if ($booking->payment_status === 'cancelled')
                                        <li>This booking has been cancelled</li>
                                    @endif
                                    @if ($booking->payment_status === 'finished')
                                        <li>This booking has been marked as finished</li>
                                    @endif
                                    @if (!$booking->from_date || !$booking->to_date)
                                        <li>Booking dates are not properly set</li>
                                    @endif
                                    @if ($booking->to_date && Carbon\Carbon::parse($booking->to_date)->isPast())
                                        <li>This booking has expired</li>
                                    @endif
                                </ul>
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cancel Booking -->
            @if ($booking->payment_status !== 'cancelled')
                <div class="card shadow-sm">
                    <div class="card-body">
                        <button wire:click="cancelBooking" class="btn btn-danger btn-block">Cancel Booking</button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        .bg-success {
            background-color: #252525 !important;
        }

        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeeba;
        }

        .alert-warning ul {
            padding-left: 1.25rem;
        }

        .input-group-sm {
            min-height: 31px;
        }

        .input-group-sm>.form-control {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.25rem 0 0 0.25rem;
        }

        .input-group-sm>.btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 0 0.25rem 0.25rem 0;
        }

        /* Additional utility classes for Bootstrap 4 compatibility */
        .shadow-sm {
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
        }

        /* Custom margins for Bootstrap 4 */
        .mr-1 {
            margin-right: 0.25rem !important;
        }

        .mr-2 {
            margin-right: 0.5rem !important;
        }

        .mr-3 {
            margin-right: 1rem !important;
        }

        .mb-1 {
            margin-bottom: 0.25rem !important;
        }

        .mb-2 {
            margin-bottom: 0.5rem !important;
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .mb-4 {
            margin-bottom: 1.5rem !important;
        }

        .mt-1 {
            margin-top: 0.25rem !important;
        }

        .mt-2 {
            margin-top: 0.5rem !important;
        }

        .mt-3 {
            margin-top: 1rem !important;
        }

        /* Custom padding utilities */
        .p-2 {
            padding: 0.5rem !important;
        }

        .p-3 {
            padding: 1rem !important;
        }

        .py-3 {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }

        /* Custom text utilities */
        .text-muted {
            color: #6c757d !important;
        }

        .small {
            font-size: 80%;
            font-weight: 400;
        }

        /* Custom border utilities */
        .border {
            border: 1px solid #dee2e6 !important;
        }

        .border-bottom {
            border-bottom: 1px solid #dee2e6 !important;
        }

        .rounded {
            border-radius: 0.25rem !important;
        }

        .rounded-circle {
            border-radius: 50% !important;
        }

        /* Custom background utilities */
        .bg-light {
            background-color: #f8f9fa !important;
        }

        .bg-white {
            background-color: #fff !important;
        }

        /* Custom flex utilities */
        .d-flex {
            display: flex !important;
        }

        .flex-column {
            flex-direction: column !important;
        }

        .justify-content-between {
            justify-content: space-between !important;
        }

        .align-items-center {
            align-items: center !important;
        }

        /* Custom button styles */
        .btn-block {
            display: block;
            width: 100%;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }

        /* Input group styles */
        .input-group-append {
            margin-left: -1px;
        }

        .input-group-append .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        /* Alert styles */
        .alert-dismissible .close {
            position: absolute;
            top: 0;
            right: 0;
            padding: 0.75rem 1.25rem;
            color: inherit;
        }

        /* Font weight utilities */
        .font-weight-bold {
            font-weight: 700 !important;
        }

        /* Text alignment */
        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }
    </style>

</div>
