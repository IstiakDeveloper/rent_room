<div class="container my-5">
    <div class="row">
        <div class="col-lg-12">
            <!-- Header Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Booking Ref #{{ $booking->id }}</h5>
                        <small>{{ $booking->package->name }}</small>
                    </div>
                    <span
                        class="badge badge-{{ $booking->payment_status === 'paid' ? 'success' : 'warning' }} {{ $booking->payment_status === 'paid' ? 'text-white' : '' }}">
                        {{ ucfirst($booking->payment_status) }}
                    </span>
                </div>

                <div class="card-body">
                    <!-- Package Info -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Package Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Package Information -->
                                <div class="col-md-6">
                                    <div class="info-card p-3 rounded bg-light">
                                        <h6 class="mb-3 text-primary">Package Information</h6>
                                        <p class="mb-2">
                                            <i class="fas fa-building mr-2 text-muted"></i>
                                            <strong>Name:</strong> {{ $booking->package->name }}
                                        </p>
                                        <p class="mb-2">
                                            <i class="fas fa-map-marker-alt mr-2 text-muted"></i>
                                            <strong>Address:</strong> {{ $booking->package->address }}
                                        </p>
                                        @if ($booking->package->description)
                                            <p class="mb-0">
                                                <i class="fas fa-info-circle mr-2 text-muted"></i>
                                                {{ $booking->package->description }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Booking Details -->
                                <div class="col-md-6">
                                    <div class="info-card p-3 rounded bg-light">
                                        <h6 class="mb-3 text-primary">Booking Details</h6>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span><i class="fas fa-calendar-check mr-2 text-muted"></i>Check In:</span>
                                            <strong>{{ \Carbon\Carbon::parse($booking->from_date)->format('M d, Y') }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span><i class="fas fa-calendar-times mr-2 text-muted"></i>Check Out:</span>
                                            <strong>{{ \Carbon\Carbon::parse($booking->to_date)->format('M d, Y') }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-clock mr-2 text-muted"></i>Duration:</span>
                                            <strong>{{ $booking->number_of_days }} Days</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rooms Section -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Booked Rooms</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @php
                                    $roomIds = json_decode($booking->room_ids, true) ?? [];
                                    $rooms = \App\Models\Room::whereIn('id', $roomIds)->get();
                                @endphp
                                @foreach ($rooms as $room)
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100 info-card">
                                            <div class="card-body">
                                                <h6
                                                    class="card-title d-flex justify-content-between align-items-center">
                                                    {{ $room->name }}
                                                    @if ($room->is_available)
                                                        <span class="badge badge-success text-white">Available</span>
                                                    @endif
                                                </h6>
                                                <div class="text-muted mb-2">
                                                    <small>
                                                        <i class="fas fa-bed mr-1"></i> {{ $room->number_of_beds }}
                                                        Beds
                                                        <i class="fas fa-bath ml-2 mr-1"></i>
                                                        {{ $room->number_of_bathrooms }} Ensuite
                                                    </small>
                                                </div>
                                                @if ($room->description)
                                                    <p class="card-text small text-muted mb-0">
                                                        {{ $room->description }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Payment Progress -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Payment Summary</h6>
                        </div>
                        <div class="card-body">
                            <!-- Progress Bar -->
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $paymentPercentage }}%">
                                    <strong>{{ number_format($paymentPercentage, 1) }}% Paid</strong>
                                </div>
                            </div>

                            <!-- Payment Summary Cards -->
                            <div class="row mt-4">
                                <div class="col-md-4">
                                    <div class="summary-card">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-pound-sign mr-2"></i>Total Price:</span>
                                            <strong>£{{ number_format($booking->price + $booking->booking_price, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="summary-card">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-check-circle mr-2"></i>Paid Amount:</span>
                                            <strong class="text-success">
                                                £{{ number_format($payments ? $payments->where('status', 'completed')->sum('amount') : 0, 2) }}
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="summary-card">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-exclamation-circle mr-2"></i>Due Amount:</span>
                                            <strong class="text-{{ $dueBill > 0 ? 'danger' : 'success' }}">
                                                £{{ number_format($dueBill, 2) }}
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Schedule -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Payment Schedule</h6>
                            @if ($dueBill > 0)
                                <span class="badge badge-danger text-white">
                                    Outstanding: £{{ number_format($dueBill, 2) }}
                                </span>
                            @else
                                <span class="badge badge-success text-white">Fully Paid</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="timeline-wrapper">
                                @foreach ($booking->bookingPayments->sortBy('due_date') as $milestone)
                                    @php
                                        $isPaid = $milestone->payment_status === 'paid';
                                        $dueDate = \Carbon\Carbon::parse($milestone->due_date);
                                        $isOverdue = !$isPaid && $dueDate->isPast();
                                        $isNextPayment = !$isPaid && $milestone->id === $currentMilestone?->id;
                                    @endphp

                                    <div class="timeline-item {{ $isPaid ? 'paid' : ($isOverdue ? 'overdue' : '') }}">
                                        <div
                                            class="card border-{{ $isOverdue ? 'danger' : ($isNextPayment ? 'warning' : 'success') }}">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <!-- Milestone Info -->
                                                    <div class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            @if ($isPaid)
                                                                <i
                                                                    class="fas fa-check-circle text-success fa-2x mr-2"></i>
                                                            @elseif($isOverdue)
                                                                <i
                                                                    class="fas fa-exclamation-circle text-danger fa-2x mr-2"></i>
                                                            @else
                                                                <i class="fas fa-clock text-warning fa-2x mr-2"></i>
                                                            @endif
                                                            <div>
                                                                <h6 class="mb-0">{{ $milestone->milestone_type }}
                                                                </h6>
                                                                <small class="text-muted">Phase
                                                                    {{ $milestone->milestone_number }}</small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Due Date -->
                                                    <div class="col-md-3">
                                                        <div class="text-muted">
                                                            <i class="fas fa-calendar-alt mr-1"></i>
                                                            {{ $dueDate->format('M d, Y') }}
                                                        </div>
                                                    </div>

                                                    <!-- Amount -->
                                                    <div class="col-md-3">
                                                        <div class="h6 mb-0">
                                                            £{{ number_format($milestone->amount, 2) }}
                                                        </div>
                                                    </div>

                                                    <!-- Status/Action -->
                                                    <div class="col-md-3 text-right">
                                                        @if ($isPaid)
                                                            <span class="badge badge-success text-white">Paid</span>
                                                        @elseif($isOverdue)
                                                            <button class="btn btn-danger btn-sm text-white"
                                                                wire:click="showPaymentM"
                                                                {{ $isNextPayment ? '' : 'disabled' }}>
                                                                Pay Now (Overdue)
                                                            </button>
                                                        @elseif($isNextPayment)
                                                            <button class="btn btn-warning btn-sm text-white"
                                                                wire:click="showPaymentM">
                                                                Pay Now
                                                            </button>
                                                        @else
                                                            <span
                                                                class="badge badge-secondary text-white">Pending</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                @if ($dueBill > 0 && $booking->payment_status != 'finished')
                                    <button class="btn btn-primary text-white" wire:click="showPaymentM">
                                        <i class="fas fa-credit-card mr-2"></i>Make Payment
                                    </button>
                                @endif

                                @if ($booking->payment_status !== 'cancelled')
                                    @php
                                        $toDate = \Carbon\Carbon::parse($booking->to_date);
                                        $currentDate = \Carbon\Carbon::today();
                                        $canRenew =
                                            ($toDate->isSameDay($currentDate) || $toDate->isPast()) &&
                                            $booking->payment_status !== 'finished';
                                    @endphp

                                    <button wire:click="showRenewModal"
                                        class="btn {{ $canRenew ? 'btn-outline-primary' : 'btn-secondary' }} text-{{ $canRenew ? 'primary' : 'white' }}"
                                        {{ !$canRenew ? 'disabled' : '' }} data-toggle="tooltip" data-placement="top"
                                        title="{{ !$canRenew ? 'Package can be renewed after ' . $toDate->format('M d, Y') : '' }}">
                                        <i class="fas fa-sync-alt mr-2"></i>Renew Package
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Payment History -->
                    @if ($payments && $payments->isNotEmpty())
                        <div class="card shadow-lg">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0 text-white">Payment History</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Method</th>
                                                <th>Reference</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($payments as $payment)
                                                <tr>
                                                    <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                                    <td>{{ $payment->transaction_id ?? 'N/A' }}</td>
                                                    <td>£{{ number_format($payment->amount, 2) }}</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-{{ $payment->status === 'completed' ? 'success' : 'warning' }} text-white">
                                                            {{ ucfirst($payment->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Initialize tooltips -->
            @push('scripts')
                <script>
                    document.addEventListener('livewire:load', function() {
                        $(function() {
                            $('[data-toggle="tooltip"]').tooltip();
                        });

                        Livewire.on('contentChanged', function() {
                            $('[data-toggle="tooltip"]').tooltip();
                        });
                    });
                </script>
            @endpush
        </div>
    </div>


    <style>
        /* General card styles */
        .card {
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.125);
        }

        .card:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            padding: 1rem 1.25rem;
        }

        /* Badge enhancements */
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        .badge-warning {
            color: #212529;
        }

        .badge-success,
        .badge-danger,
        .badge-primary,
        .badge-secondary {
            color: #fff;
        }

        /* Button styles */
        .btn {
            font-weight: 500;
            letter-spacing: 0.3px;
            padding: 0.375rem 1rem;
        }

        .btn-sm {
            padding: 0.25rem 0.75rem;
        }

        /* Progress bar */
        .progress {
            height: 25px;
            border-radius: 1rem;
            background-color: #e9ecef;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
            margin: 1rem 0;
        }

        .progress-bar {
            border-radius: 1rem;
            transition: width 0.6s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        /* Timeline styles */
        .timeline-wrapper {
            position: relative;
            padding: 1.5rem 0;
        }

        .timeline-item {
            position: relative;
            padding-left: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0.75rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item:last-child::before {
            height: 50%;
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            left: 0.25rem;
            top: 1.5rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            background: #6c757d;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #e9ecef;
        }

        .timeline-item.paid::after {
            background: #28a745;
        }

        .timeline-item.overdue::after {
            background: #dc3545;
        }

        /* Modal styles */
        .modal-content {
            border-radius: 0.5rem;
            border: none;
        }

        .modal-header {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            background-color: #f8f9fa;
        }

        /* Form styles */
        .form-control {
            border-radius: 0.25rem;
            border: 1px solid #ced4da;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Custom hover effects */
        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        /* Summary cards */
        .summary-card {
            background: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        /* Responsive table */
        .table-responsive {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        /* Tooltip customization */
        .tooltip-inner {
            max-width: 200px;
            padding: 0.5rem 1rem;
            background-color: #333;
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }

        .tooltip.bs-tooltip-top .arrow::before {
            border-top-color: #333;
        }
    </style>
</div>
