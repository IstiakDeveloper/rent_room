<!-- resources/views/livewire/booking-details.blade.php -->
<div>
    <!-- Auto Renewal Modal -->
    <div class="modal fade" id="autoRenewalModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-sync-alt mr-2"></i>Auto-Renewal Settings
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                        wire:click="closeAutoRenewalModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="autoRenewalToggle"
                                wire:model.live="autoRenewal">
                            <label class="custom-control-label" for="autoRenewalToggle">
                                Enable Auto-Renewal
                            </label>
                        </div>
                    </div>

                    <div class="form-group" x-show="$wire.autoRenewal">
                        <label for="renewalPeriodDays">Renewal Period (Days)</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="renewalPeriodDays"
                                wire:model="renewalPeriodDays" min="1" max="365">
                            <div class="input-group-append">
                                <span class="input-group-text">Days</span>
                            </div>
                        </div>
                        @error('renewalPeriodDays')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="alert alert-info mt-4">
                        <div class="d-flex">
                            <div class="mr-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="alert-heading">How Auto-Renewal Works</h6>
                                <p class="mb-0 small">
                                    When enabled, your booking will be automatically renewed 7 days before it expires.
                                    You'll receive an email notification when the renewal is processed.
                                </p>
                            </div>
                        </div>
                    </div>

                    @if ($booking->auto_renewal)
                        <div class="alert alert-warning mt-3">
                            <div class="d-flex">
                                <div class="mr-3">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="alert-heading">Important Note</h6>
                                    <p class="mb-0 small">
                                        Disabling auto-renewal will prevent future automatic renewals,
                                        but won't affect your current booking period.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="toggleAutoRenewal">
                        <i class="fas fa-save mr-2"></i>Save Settings
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <!-- Main Card Container -->
        <div class="card shadow">
            <!-- Main Card Header -->
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 text-white">Booking Ref #{{ $booking->id }}</h5>
                    <small>{{ $booking->package->name }}</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge badge-{{ $booking->payment_status === 'paid' ? 'success' : 'warning' }} mr-3">
                        {{ ucfirst($booking->payment_status) }}
                    </span>
                    <button type="button" wire:click="showAutoRenewalSettings" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-sync-alt mr-1"></i>Auto-Renewal
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Sub-cards Container -->
                <div class="row">
                    <!-- Booking Information Sub-card -->
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-info-circle mr-2"></i>Booking Information
                                </h6>
                                <div class="d-flex align-items-center">
                                    <span
                                        class="badge {{ $booking->auto_renewal ? 'badge-success' : 'badge-secondary' }} mr-2">
                                        <i class="fas {{ $booking->auto_renewal ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                        {{ $booking->auto_renewal ? 'Auto-Renewal Active' : 'Auto-Renewal Inactive' }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Package Info -->
                                        <div class="bg-light p-3 rounded mb-3">
                                            <h6 class="text-primary mb-3">Package Details</h6>
                                            <div class="row mb-2">
                                                <div class="col-sm-4 text-muted">Package Name:</div>
                                                <div class="col-sm-8">{{ $booking->package->name }}</div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-4 text-muted">Address:</div>
                                                <div class="col-sm-8">{{ $booking->package->address }}</div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-4 text-muted">Booked Date:</div>
                                                <div class="col-sm-8">{{ $booking->created_at->format('M d, Y') }}
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-4 text-muted">Check In:</div>
                                                <div class="col-sm-8">
                                                    {{ \Carbon\Carbon::parse($booking->from_date)->format('M d, Y') }}
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-4 text-muted">Check Out:</div>
                                                <div class="col-sm-8">
                                                    {{ \Carbon\Carbon::parse($booking->to_date)->format('M d, Y') }}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4 text-muted">Duration:</div>
                                                <div class="col-sm-8">{{ $booking->number_of_days }} Days</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Booked Rooms -->
                                        <div class="bg-light p-3 rounded">
                                            <h6 class="text-primary mb-3">Booked Rooms</h6>
                                            <div class="rooms-container">
                                                @php
                                                    $roomIds = json_decode($booking->room_ids, true) ?? [];
                                                    $rooms = \App\Models\Room::whereIn('id', $roomIds)->get();
                                                @endphp
                                                @foreach ($rooms as $room)
                                                    <div class="bg-white p-3 rounded mb-2 shadow-sm">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <h6 class="mb-1">{{ $room->name }}</h6>
                                                                <div class="text-muted small">
                                                                    <i class="fas fa-bed mr-1"></i>
                                                                    {{ $room->number_of_beds }} Beds
                                                                    <span class="mx-2">|</span>
                                                                    <i class="fas fa-bath mr-1"></i>
                                                                    {{ $room->number_of_bathrooms }} Ensuite
                                                                </div>
                                                            </div>
                                                            @if ($room->is_available)
                                                                <span class="badge badge-success">Available</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Modal -->
                    <div class="modal fade" id="paymentModal" tabindex="-1" wire:ignore.self>
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-credit-card mr-2"></i>Make Payment
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal"
                                        aria-label="Close" wire:click="closePaymentModal">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="paymentAmount">Payment Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">£</span>
                                            </div>
                                            <input type="text" class="form-control" readonly
                                                value="{{ number_format($selectedMilestoneAmount ?? 0, 2) }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="paymentMethod">Payment Method</label>
                                        <select class="form-control @error('paymentMethod') is-invalid @enderror"
                                            wire:model="paymentMethod">
                                            <option value="">Select Payment Method</option>
                                            <option value="card">Credit/Debit Card</option>
                                            <option value="bank_transfer">Bank Transfer</option>
                                        </select>
                                        @error('paymentMethod')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @if ($paymentMethod === 'bank_transfer')
                                        <div class="form-group">
                                            <label for="bankTransferReference">Bank Transfer Reference</label>
                                            <input type="text"
                                                class="form-control @error('bankTransferReference') is-invalid @enderror"
                                                wire:model="bankTransferReference"
                                                placeholder="Enter your bank transfer reference">
                                            @error('bankTransferReference')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <strong>Bank Details:</strong><br>
                                            {{ $bankDetails }}<br><br>
                                            <small>Please use the reference: BOK-{{ $booking->id }}</small>
                                        </div>
                                    @endif

                                    @if ($paymentMethod === 'card')
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            You will be redirected to our secure payment gateway to complete your card
                                            payment.
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                        wire:click="closePaymentModal">
                                        <i class="fas fa-times mr-2"></i>Cancel
                                    </button>
                                    <button type="button" class="btn btn-primary" wire:click="processPayment">
                                        <i class="fas fa-check mr-2"></i>Proceed Payment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Section with Updated Modal Integration -->
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-credit-card mr-2"></i>Payment Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <!-- Payment Summary -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3">Payment Summary</h6>
                                        @if ($dueBill > 0 && $booking->payment_status != 'finished')
                                            <button class="btn btn-primary btn-sm text-white"
                                                wire:click="showPaymentM({{ $currentMilestone?->id ?? null }}, {{ $currentMilestone?->amount ?? $dueBill }})">
                                                <i class="fas fa-credit-card mr-2"></i>Make Payment
                                            </button>
                                        @endif
                                        <div class="progress mb-3" style="height: 25px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $paymentPercentage }}%">
                                                <strong>{{ number_format($paymentPercentage, 1) }}% Paid</strong>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="bg-light p-3 rounded text-center">
                                                    <div class="text-muted mb-2">Total Price</div>
                                                    <h5 class="mb-0">
                                                        £{{ number_format($booking->price + $booking->booking_price, 2) }}
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="bg-light p-3 rounded text-center">
                                                    <div class="text-muted mb-2">Paid Amount</div>
                                                    <h5 class="text-success mb-0">
                                                        £{{ number_format($payments ? $payments->where('status', 'completed')->sum('amount') : 0, 2) }}
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="bg-light p-3 rounded text-center">
                                                    <div class="text-muted mb-2">Due Amount</div>
                                                    <h5 class="text-{{ $dueBill > 0 ? 'danger' : 'success' }} mb-0">
                                                        £{{ number_format($dueBill, 2) }}
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Schedule -->
                                <div class="timeline-wrapper">
                                    @foreach ($booking->bookingPayments->sortBy('due_date') as $milestone)
                                        @php
                                            $isPaid = $milestone->payment_status === 'paid';
                                            $dueDate = \Carbon\Carbon::parse($milestone->due_date);
                                            $isOverdue = !$isPaid && $dueDate->isPast();
                                            $isNextPayment = !$isPaid && $milestone->id === $currentMilestone?->id;
                                        @endphp

                                        <div
                                            class="timeline-item {{ $isPaid ? 'paid' : ($isOverdue ? 'overdue' : '') }}">
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
                                                                    <i
                                                                        class="fas fa-clock text-warning fa-2x mr-2"></i>
                                                                @endif
                                                                <div>
                                                                    <h6 class="mb-0">
                                                                        {{ $milestone->milestone_type }}
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
                                                                <span
                                                                    class="badge badge-success text-white">Paid</span>
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
                    </div>

                    <script>
                        document.addEventListener('livewire:initialized', () => {
                            const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));

                            Livewire.on('closePaymentModal', () => {
                                paymentModal.hide();
                            });

                            Livewire.on('paymentProcessed', () => {
                                paymentModal.hide();
                                // Additional success handling if needed
                            });
                        });
                    </script>

                    <style>
                        /* Add to your existing styles */
                        .modal-content {
                            border: none;
                            border-radius: 0.5rem;
                        }

                        .modal-header {
                            border-top-left-radius: 0.5rem;
                            border-top-right-radius: 0.5rem;
                        }

                        /* Loading spinner animation */
                        @keyframes spin {
                            0% {
                                transform: rotate(0deg);
                            }

                            100% {
                                transform: rotate(360deg);
                            }
                        }
                    </style>
                    <!-- Instructions Sub-card -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card shadow-sm">
                                    <div
                                        class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-list-alt mr-2 text-primary"></i>Package Instructions
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if ($booking->package->instructions->isEmpty())
                                            <div class="text-center py-4">
                                                <div class="mb-3">
                                                    <i class="fas fa-clipboard-list fa-3x text-muted"></i>
                                                </div>
                                                <p class="text-muted mb-0">No specific instructions provided for this
                                                    package.</p>
                                            </div>
                                        @else
                                            <div class="timeline-instructions">
                                                @foreach ($booking->package->instructions->sortBy('order') as $instruction)
                                                    <div class="instruction-item mb-4">
                                                        <div class="d-flex align-items-start">
                                                            <div class="instruction-number">
                                                                <span
                                                                    class="badge badge-primary rounded-circle d-flex align-items-center justify-content-center"
                                                                    style="width: 35px; height: 35px;">
                                                                    {{ $loop->iteration }}
                                                                </span>
                                                            </div>

                                                            <div class="instruction-content ml-3 flex-grow-1">
                                                                <div class="card bg-light border-0 hover-card">
                                                                    <div class="card-body">
                                                                        <h6 class="card-title mb-2 text-primary">
                                                                            {{ $instruction->title }}
                                                                        </h6>
                                                                        <p class="card-text text-muted mb-0">
                                                                            {{ $instruction->description }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="alert alert-info mt-4 mb-0">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-info-circle fa-lg mr-3"></i>
                                                    <div>
                                                        <h6 class="alert-heading mb-1">Important Note</h6>
                                                        <p class="mb-0 small">Please follow these instructions
                                                            carefully to ensure a smooth stay.
                                                            If you have any questions, don't hesitate to contact
                                                            support.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* General Styles */
        .card {
            border: none;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            border-top-left-radius: 0.5rem !important;
            border-top-right-radius: 0.5rem !important;
            padding: 1rem 1.25rem;
        }

        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        /* Timeline Styles */
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

        /* Progress Bar */
        .progress {
            height: 25px;
            border-radius: 1rem;
            margin: 1rem 0;
            background-color: #e9ecef;
        }

        .progress-bar {
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 500;
        }

        /* Instructions Timeline */
        .timeline-instructions {
            position: relative;
            padding-left: 1rem;
        }

        .instruction-item {
            position: relative;
            padding-left: 1rem;
        }

        .instruction-item::before {
            content: '';
            position: absolute;
            left: 17px;
            top: 35px;
            bottom: -15px;
            width: 2px;
            background-color: #e9ecef;
        }

        .instruction-item:last-child::before {
            display: none;
        }

        .hover-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-card:hover {
            transform: translateX(5px);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        /* Room Container */
        .rooms-container {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .rooms-container::-webkit-scrollbar {
            width: 6px;
        }

        .rooms-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .rooms-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        /* Toast Notifications */
        .toast {
            min-width: 300px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .fixed-bottom {
            position: fixed;
            bottom: 20px;
            right: 20px;
        }

        /* Print Styles */
        @media print {

            .btn-outline-primary,
            .btn-outline-light {
                display: none;
            }

            .card {
                break-inside: avoid;
            }

            .timeline-instructions {
                padding-left: 0;
            }

            .instruction-item::before {
                display: none;
            }
        }

        .card {
            border: none;
            border-radius: 0.5rem;
        }

        .card-header {
            border-top-left-radius: 0.5rem !important;
            border-top-right-radius: 0.5rem !important;
        }

        /* Sub-cards */
        .card .card {
            border: 1px solid rgba(0, 0, 0, .125);
        }

        .card .card .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, .125);
        }

        .rooms-container {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const modal = new bootstrap.Modal(document.getElementById('autoRenewalModal'));

            // Modal handling
            Livewire.on('openModal', (modalId) => {
                modal.show();
            });

            Livewire.on('closeModal', (modalId) => {
                modal.hide();
            });

            // Notifications
            Livewire.on('notify', (event) => {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: event[0]
                }));
            });

            // Initialize tooltips
            $(function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

            Livewire.on('contentChanged', () => {
                $('[data-toggle="tooltip"]').tooltip();
            });
        });
    </script>
</div>
