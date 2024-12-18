<div class="container my-5">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Booking Information</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6"><strong>ID:</strong> {{ $booking->id }}</div>
                <div class="col-md-6"><strong>User ID:</strong> {{ $booking->user_id }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><strong>Package ID:</strong> {{ $booking->package_id }}</div>
                <div class="col-md-6"><strong>Number of Days:</strong> {{ $booking->number_of_days }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><strong>Price Type:</strong> {{ $booking->price_type }}</div>
                <div class="col-md-6"><strong>Total Price:</strong> £{{ $booking->price + $booking->booking_price }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><strong>Booking Price:</strong> £{{ $booking->booking_price }}</div>
                <div class="col-md-6"><strong>Total Paid:</strong> <span class="text-success">£{{ $booking->total_amount }}</span></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6"><strong>Payment Option:</strong> {{ $booking->payment_option }}</div>
                <div class="col-md-6"><strong>Rent Status:</strong> <span class="{{ $booking->payment_status === 'paid' ? 'text-success' : 'text-danger' }}">{{ ucfirst($booking->payment_status) }}</span></div>
            </div>
        </div>
    </div>

    @if ($booking->payment_status !== 'cancelled')
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Renewal Information</h5>
            </div>
            @php                  
                $currentDate = \Carbon\Carbon::today(); // Get today's date
                $toDate = \Carbon\Carbon::parse($booking->to_date); // Convert booking's to_date to a Carbon instance
            @endphp
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6"><strong>Start Date:</strong> {{ $booking->from_date }}</div>
                    <div class="col-md-6"><strong>End Date:</strong> {{ $booking->to_date }}</div>
                </div>
                <div class="mt-4">
                    <button 
                        wire:click="showRenewModal" 
                        class="btn btn-secondary" 
                        {{ !($toDate->isSameDay($currentDate) || $toDate->isBefore($currentDate)) || $booking->payment_status == 'finished' ? 'disabled' : '' }}>
                        Renew Package
                    </button>
                    <button 
                        wire:click="finishBooking" 
                        class="btn btn-primary ml-2" 
                        {{ !($toDate->isSameDay($currentDate) || $toDate->isBefore($currentDate)) || $booking->payment_status == 'finished' ? 'disabled' : '' }}>
                        Finish
                    </button>
                </div>
                @if (!($toDate->isSameDay($currentDate) || $toDate->isBefore($currentDate)) || $booking->payment_status == 'finished')
                    <div class="mt-4 p-3 text-danger rounded">
                        <p>You can renew or finish when your booking is over.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if ($booking->payment_status !== 'cancelled')
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0">Payment Information</h5>
            </div>
            <div class="card-body">
                @if ($payments->count() > 0)
                    <ul class="list-unstyled">
                        @foreach ($payments as $payment)
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
                            <hr>
                        @endforeach
                    </ul>
                @else
                    <p class="text-danger">No payment made here</p>
                @endif
                <li class="d-flex justify-content-between lh-22 mb-2">
                    <p class="text-gray-light mb-0">Total Due:</p>
                    <p class="font-weight-500 text-heading mb-0">£{{ $dueBill }}</p>
                </li>

                @if ($dueBill > 0 && $booking->payment_status != 'finished')
                    <div class="card p-2">
                        @php
                        // Parse dates and set them to start of the day to ensure accurate comparisons
                        $fromDate = \Carbon\Carbon::parse($booking->from_date)->startOfDay();
                        $today = \Carbon\Carbon::now()->startOfDay();
                        @endphp

                        <div class="mt-4 p-3 rounded 
                            @if($fromDate->lessThanOrEqualTo($today))
                                bg-danger text-white
                            @else
                                bg-light text-dark
                            @endif">
                            
                            <h5>
                                @if($fromDate->lessThanOrEqualTo($today))
                                    Overdue:
                                    £{{ $dueBill }}
                                @else
                                    Due Bill:
                                @endif
                            </h5>
                            
                            <p class="font-weight-bold 
                                @if($fromDate->lessThanOrEqualTo($today))
                                    text-danger
                                @else
                                    text-primary
                                @endif">
                                £{{ $dueBill }}
                            </p>

                            @if($dueBill > 0 && $booking->payment_status != 'finished')
                                <div class="mt-4">
                                    <button class="btn btn-primary" wire:click="showPaymentM">Pay Bill</button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

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
                {{ $booking->payment_status === 'cancelled' || $fromDate->lessThan($currentDate) ? 'disabled' : '' }}>
                Cancel Booking
            </button>
            @if ($fromDate->lessThan($currentDate))
                <div class="mt-4 p-3 text-danger rounded">
                    <p>You can only cancel a booking that is set to start today or in the future.</p>
                </div>
            @endif
        </div>
    </div>
    
    
    

    @if($showPaymentModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Choose Payment Method</h5>
                        <button type="button" class="close" aria-label="Close" wire:click="$set('showPaymentModal', false)">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="proceedPayment">
                        <div class="modal-body">
                            <p>Due Bill: £{{ number_format($dueBill, 2) }}</p>
                            <div class="form-group">
                                <label for="paymentMethod">Payment Method</label>
                                <select id="paymentMethod" class="form-control" wire:model.live="paymentMethod">
                                    <option value="cash">Cash</option>
                                    <option value="card">Card (Stripe)</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
        
                            @if($paymentMethod == 'card')
                                <p>Card payment will be processed through Stripe.</p>
                            @endif
        
                            @if($paymentMethod == 'bank_transfer')
                                <div>
                                    <p>Bank Details: {{ $bankDetails }}</p>
                                </div>
                                <div>
                                    <label for="bankTransferReference">Reference</label>
                                    <input type="text" wire:model.live="bankTransferReference" id="bankTransferReference" class="form-control" required>
                                    @error('bankTransferReference') <span class="error">{{ $message }}</span> @enderror
                                </div>
                            @endif
        
                            @if($paymentMethod == 'cash')
                                <div>
                                    <p>Please bring cash to the venue.</p>
                                </div>
                            @endif
        
                            @if(session()->has('error'))
                                <div class="alert alert-danger mt-3">
                                    {{ session('error') }}
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="$set('showPaymentModal', false)">Close</button>
                            <button type="submit" class="btn btn-primary">Proceed Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($showRenewalModal)
    <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Renew Package</h5>
                    <button type="button" class="close" aria-label="Close" wire:click="$set('showRenewalModal', false)">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form wire:submit.prevent="renewPackage">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="newFromDate">New From Date</label>
                            <input type="date" id="newFromDate" class="form-control" wire:model.defer="newFromDate" required>
                            @error('newFromDate') <span class="error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="newToDate">New To Date</label>
                            <input type="date" id="newToDate" class="form-control" wire:model.defer="newToDate" required>
                            @error('newToDate') <span class="error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showRenewalModal', false)">Close</button>
                        <button type="submit" class="btn btn-primary">Renew Package</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
