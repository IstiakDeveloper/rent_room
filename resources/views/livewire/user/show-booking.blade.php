<div class="container my-5">
    <div class="row">
        <div class="col-lg-12">
            <!-- Header Card -->
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Reference: #{{ $booking->id }}</h5>
                        <small>{{ $booking->package->name }}</small>
                    </div>
                    <span class="badge badge-{{ $booking->payment_status === 'paid' ? 'success' : 'warning' }}">
                        {{ ucfirst($booking->payment_status) }}
                    </span>
                </div>

                <!-- Package Details Card -->
                <div class="card-body">
                    <!-- Package Info -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Booked Roome Details</h6>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><i class="fas fa-building mr-2"></i> <strong>Name:</strong>
                                        {{ $booking->package->name }}</p>
                                    <p class="mb-2"><i class="fas fa-map-marker-alt mr-2"></i>
                                        <strong>Address:</strong> {{ $booking->package->address }}</p>

                                </div>

                                <div class="col-md-6">
                                    <div class="bg-light p-3 rounded">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span><i class="fas fa-calendar-check mr-2"></i>Check In:</span>
                                            <strong>{{ \Carbon\Carbon::parse($booking->from_date)->format('M d, Y') }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span><i class="fas fa-calendar-times mr-2"></i>Check Out:</span>
                                            <strong>{{ \Carbon\Carbon::parse($booking->to_date)->format('M d, Y') }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span><i class="fas fa-clock mr-2"></i>Duration:</span>
                                            <strong>{{ $booking->number_of_days }} Days
                                               </strong>
                                        </div>
                                    </div>
                                </div>

                                @if ($booking->package->description)
                                    <div class="col-12 mt-3">
                                        <p class="mb-0"><i class="fas fa-info-circle mr-2"></i>
                                            {{ $booking->package->description }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Rooms Section -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Booked Rooms</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @php
                                    $roomIds = json_decode($booking->room_ids, true) ?? [];
                                    $rooms = \App\Models\Room::whereIn('id', $roomIds)->get();
                                @endphp
                                @foreach ($rooms as $room)
                                    <div class="col-md-4">
                                        <div class="card h-100 border">
                                            <div class="card-body">
                                                <h6
                                                    class="card-title d-flex justify-content-between align-items-center">
                                                    {{ $room->name }}
                                                </h6>
                                                <div class="text-muted mb-2">
                                                    <small>
                                                        <i class="fas fa-bed mr-1"></i> {{ $room->number_of_beds }}
                                                        Beds
                                                        <i class="fas fa-bath ms-2 mr-1"></i>
                                                        {{ $room->number_of_bathrooms }} Bath
                                                    </small>
                                                </div>
                                                @if ($room->description)
                                                    <small class="text-muted">{{ $room->description }}</small>
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
                            <div class="mb-3">
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $paymentPercentage }}%">
                                        <strong>{{ number_format($paymentPercentage, 1) }}% Paid</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <span><i class="fas fa-pound-sign mr-2"></i>Total Price:</span>
                                                <strong>£{{ number_format($booking->price + $booking->booking_price, 2) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <span><i class="fas fa-check-circle mr-2"></i>Paid:</span>
                                                <strong
                                                    class="text-success">£{{ number_format($payments->sum('amount'), 2) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <span><i class="fas fa-exclamation-circle mr-2"></i>Due:</span>
                                                <strong class="text-{{ $dueBill > 0 ? 'danger' : 'success' }}">
                                                    £{{ number_format($dueBill, 2) }}
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Schedule -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Payment Schedule</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $currentMilestone = $booking->bookingPayments
                                    ->where('is_paid', false)
                                    ->sortBy('due_date')
                                    ->first();

                                $hasOverdue = $booking->bookingPayments
                                    ->where('is_paid', false)
                                    ->where('due_date', '<', now())
                                    ->isNotEmpty();
                            @endphp

                            @if ($currentMilestone)
                                <div class="alert {{ $hasOverdue ? 'alert-danger' : 'alert-info' }} mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">
                                                {{ $hasOverdue ? 'Payment Overdue' : 'Next Payment Due' }}</h6>
                                            <p class="mb-0">
                                                {{ $currentMilestone->milestone_type }}
                                                {{ $currentMilestone->milestone_number }} Payment -
                                                Due:
                                                {{ \Carbon\Carbon::parse($currentMilestone->due_date)->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <h5 class="mb-1">£{{ number_format($currentMilestone->amount, 2) }}</h5>
                                            @if ($dueBill > 0)
                                                <button class="btn btn-sm btn-{{ $hasOverdue ? 'danger' : 'primary' }}"
                                                    wire:click="showPaymentM">
                                                    Pay Now
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Payment</th>
                                            <th>Due Date</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-end">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $previousUnpaidMilestone = null;
                                        @endphp
                                        @foreach ($booking->bookingPayments->sortBy('due_date') as $milestone)
                                            @php
                                                $dueDate = \Carbon\Carbon::parse($milestone->due_date);
                                                $isOverdue = $dueDate->isPast() && !$milestone->is_paid;
                                                $isCurrentPayment =
                                                    !$milestone->is_paid &&
                                                    (!$previousUnpaidMilestone ||
                                                        ($previousUnpaidMilestone &&
                                                            $previousUnpaidMilestone->due_date > $milestone->due_date));
                                            @endphp
                                            <tr
                                                class="{{ $isOverdue ? 'table-danger' : ($milestone->is_paid ? 'table-success' : ($isCurrentPayment ? 'table-warning' : '')) }}">
                                                <td>
                                                    @if ($isCurrentPayment)
                                                        <span class="badge bg-warning mr-2">Current</span>
                                                    @endif
                                                    {{ $milestone->milestone_type }}
                                                    {{ $milestone->milestone_number }}
                                                </td>
                                                <td>{{ $dueDate->format('M d, Y') }}</td>
                                                <td class="text-end">£{{ number_format($milestone->amount, 2) }}</td>
                                                <td class="text-end">
                                                    <span
                                                        class="badge bg-{{ $milestone->is_paid ? 'success' : ($isOverdue ? 'danger' : 'warning') }}">
                                                        {{ $milestone->is_paid ? 'Paid' : ($isOverdue ? 'Overdue' : 'Pending') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @php
                                                if (!$milestone->is_paid) {
                                                    $previousUnpaidMilestone = $milestone;
                                                }
                                            @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                @if ($dueBill > 0 && $booking->payment_status != 'finished')
                                    <button class="btn btn-primary" wire:click="showPaymentM">
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
                                        class="btn btn-{{ $canRenew ? 'outline-primary' : 'secondary' }}"
                                        {{ !$canRenew ? 'disabled' : '' }}>
                                        <i class="fas fa-sync-alt mr-2"></i>Renew Package
                                    </button>

                                    @if (!$canRenew)
                                        <small class="text-muted">
                                            Package can be renewed after {{ $toDate->format('M d, Y') }}
                                        </small>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Payment History -->
                    @if ($payments->isNotEmpty())
                        <div class="card shadow-lg">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">Payment History</h5>
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
                                                            class="badge badge-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
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

                <!-- Payment Modal -->
                @if ($showPaymentModal)
                    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Payment for {{ $currentMilestone->milestone_type }}
                                        {{ $currentMilestone->milestone_number }}</h5>
                                    <button type="button" class="close"
                                        wire:click="$set('showPaymentModal', false)">×</button>
                                </div>
                                <form wire:submit.prevent="proceedPayment">
                                    <div class="modal-body">
                                        <div class="alert {{ $hasOverdue ? 'alert-danger' : 'alert-info' }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>Payment Amount:</span>
                                                <strong>£{{ number_format($currentMilestone->amount, 2) }}</strong>
                                            </div>
                                            <small class="d-block mt-1">Due Date:
                                                {{ $dueDate->format('M d, Y') }}</small>
                                        </div>

                                        <div class="form-group">
                                            <label>Payment Method</label>
                                            <select class="form-control" wire:model.live="paymentMethod">
                                                <option value="card">Card Payment</option>
                                                <option value="bank_transfer">Bank Transfer</option>
                                                <option value="cash">Cash</option>
                                            </select>
                                        </div>

                                        @if ($paymentMethod === 'bank_transfer')
                                            <div class="alert alert-secondary mt-3">
                                                <small class="d-block mb-2">{{ $bankDetails }}</small>
                                                <div class="form-group mb-0">
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter transfer reference"
                                                        wire:model.live="bankTransferReference" required>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            wire:click="$set('showPaymentModal', false)">Close</button>
                                        <button type="submit" class="btn btn-primary">Proceed Payment</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Renewal Modal -->
                @if ($showRenewalModal)
                    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Renew Package</h5>
                                    <button type="button" class="close"
                                        wire:click="$set('showRenewalModal', false)">×</button>
                                </div>
                                <form wire:submit.prevent="renewPackage">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="date" class="form-control" wire:model.defer="newFromDate"
                                                required>
                                            @error('newFromDate')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group mt-3">
                                            <label>End Date</label>
                                            <input type="date" class="form-control" wire:model.defer="newToDate"
                                                required>
                                            @error('newToDate')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            wire:click="$set('showRenewalModal', false)">Close</button>
                                        <button type="submit" class="btn btn-primary">Confirm Renewal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
