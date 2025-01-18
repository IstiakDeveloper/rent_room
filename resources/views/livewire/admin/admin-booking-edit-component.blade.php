<div>
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mt-4">Edit Booking #{{ $booking->id }}</h2>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Bookings
            </a>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card mb-4 mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Booking Details</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="updateBooking">
                    <!-- User Selection -->
                    <div class="mb-3">
                        <label class="form-label">User</label>
                        <div class="position-relative">
                            <input type="text" class="form-control" wire:model.live="searchQuery"
                                placeholder="Search user by name or email" autocomplete="off">

                            @if (!empty($users))
                                <div class="position-absolute w-100 mt-1 bg-white border rounded shadow-sm z-10">
                                    @foreach ($users as $user)
                                        <div class="p-2 border-bottom cursor-pointer hover:bg-gray-100"
                                            wire:click="selectUser({{ $user->id }})">
                                            {{ $user->name }} ({{ $user->email }})
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if ($selectedUser)
                            <div class="mt-2 p-2 bg-light rounded">
                                Selected: {{ $selectedUser->name }} ({{ $selectedUser->email }})
                            </div>
                        @endif

                        @error('selectedUser')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Package Selection -->
                    <div class="mb-3">
                        <label class="form-label">Package</label>
                        <select class="form-control" wire:model.live="packageId">
                            <option value="">Choose a package</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}">{{ $package->name }}</option>
                            @endforeach
                        </select>
                        @error('packageId')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Room Selection -->
                    @if ($packageId)
                        <div class="card shadow-sm">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-2">
                                    <i class="fas fa-bed mr-2 text-primary"></i>Room Selection
                                </h6>

                                @if ($packages->find($packageId)->rooms->count() > 0)
                                    <div class="room-list">
                                        @foreach ($packages->find($packageId)->rooms as $room)
                                            <div wire:key="room-{{ $room->id }}"
                                                wire:click="selectRoom({{ $room->id }})"
                                                class="room-item d-flex justify-content-between align-items-center p-2 mb-2 rounded-2 {{ $selectedRoom == $room->id ? 'selected' : '' }}"
                                                style="cursor: pointer;">
                                                <span class="fw-medium">
                                                    {{ $room->name }} •
                                                    <i class="fas fa-bed small"></i> {{ $room->number_of_beds }} •
                                                    <i class="fas fa-bath small"></i> {{ $room->number_of_bathrooms }}
                                                </span>
                                                @if ($selectedRoom == $room->id)
                                                    <i class="fas fa-check-circle text-success"></i>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-muted small py-2">
                                        <i class="fas fa-info-circle mr-1"></i>No rooms available
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Date Selection -->
                    @if ($selectedRoom && $calendarView)
                    <div x-data="datePickerComponent(@js(['disabledDates' => $disabledDates, 'fromDate' => $fromDate, 'toDate' => $toDate]))" class="mt-4">
                        <label class="form-label">Check-in and Check-out Dates</label>
                        <input x-ref="dateRangePicker" type="text" class="form-control w-full"
                            placeholder="Select dates" readonly>

                        @error('dateRange')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <script>
                        function datePickerComponent(config) {
                            return {
                                disabledDates: config.disabledDates,
                                init() {
                                    const picker = flatpickr(this.$refs.dateRangePicker, {
                                        mode: 'range',
                                        dateFormat: 'Y-m-d',
                                        minDate: 'today',
                                        disable: this.disabledDates.map(date => new Date(date)),
                                        defaultDate: [config.fromDate, config.toDate],
                                        onChange: (selectedDates) => {
                                            if (selectedDates.length === 2) {
                                                @this.call('selectDates', {
                                                    start: selectedDates[0].toISOString().split('T')[0],
                                                    end: selectedDates[1].toISOString().split('T')[0]
                                                });
                                            }
                                        }
                                    });

                                    // Watch for changes in disabled dates
                                    this.$watch('disabledDates', (newValue) => {
                                        picker.set('disable', newValue.map(date => new Date(date)));
                                    });
                                }
                            };
                        }
                    </script>
                @endif

                    @if ($fromDate && $toDate)
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" wire:model="phone"
                                placeholder="Enter phone number">
                            @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <!-- Booking Summary -->
                    @if ($fromDate && $toDate && $totalAmount > 0)
                        <div class="card mt-4 mb-3">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Updated Booking Summary</h5>
                            </div>
                            <div class="card-body">
                                <!-- Dates and Basic Info -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <p><strong><i class="far fa-calendar-alt mr-2"></i>From:</strong>
                                            {{ Carbon\Carbon::parse($fromDate)->format('d M Y') }}</p>
                                        <p><strong><i class="far fa-calendar-alt mr-2"></i>To:</strong>
                                            {{ Carbon\Carbon::parse($toDate)->format('d M Y') }}</p>
                                        <p><strong><i class="far fa-clock mr-2"></i>Duration:</strong>
                                            {{ Carbon\Carbon::parse($fromDate)->diffInDays(Carbon\Carbon::parse($toDate)) }}
                                            days
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong><i class="fas fa-tag mr-2"></i>Price Type:</strong>
                                            {{ $priceType }} Rate</p>
                                        <p><strong><i class="fas fa-money-bill mr-2"></i>Payment Option:</strong>
                                            {{ $paymentOption === 'full' ? 'Full Payment' : 'Booking Price Only' }}</p>
                                    </div>
                                </div>

                                <!-- Payment Breakdown -->
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr class="table-light">
                                                <td colspan="2">
                                                    <strong><i class="fas fa-calendar mr-1"></i>Updated Charges</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-3">Base Price:</td>
                                                <td class="text-end">£{{ number_format($totalAmount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-3">Booking Fee:</td>
                                                <td class="text-end">£{{ number_format($bookingPrice, 2) }}</td>
                                            </tr>

                                            <!-- Milestone Breakdown -->
                                            <tr class="table-light">
                                                <td colspan="2">
                                                    <strong><i class="fas fa-clock mr-1"></i>Updated Payment Schedule
                                                        ({{ $priceType }})</strong>
                                                </td>
                                            </tr>
                                            @if (!empty($priceBreakdown))
                                                @foreach ($priceBreakdown as $milestone)
                                                    <tr>
                                                        <td class="ps-3">
                                                            {{ $milestone['description'] }}
                                                            @if (isset($milestone['note']))
                                                                <small class="text-muted d-block">{{ $milestone['note'] }}</small>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">
                                                            £{{ number_format($milestone['total'], 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif

                                            <!-- Total Section -->
                                            <tr class="border-top">
                                                <td><strong>Updated Payment Summary</strong></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td class="ps-3">Total Amount:</td>
                                                <td class="text-end">
                                                    £{{ number_format($totalAmount + $bookingPrice, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-3">Amount Due:</td>
                                                <td class="text-end">
                                                    <strong class="text-primary">
                                                        £{{ number_format($paymentOption === 'full' ? $totalAmount + $bookingPrice : $bookingPrice, 2) }}
                                                    </strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Payment Schedule Info -->
                                    <div class="alert alert-info mt-3 mb-0">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        @if ($priceType === 'Month')
                                            Monthly payments are due at the start of each month
                                        @elseif($priceType === 'Week')
                                            Weekly payments are due at the start of each week
                                        @else
                                            Daily payments are due at the start of each day
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Options Section -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Payment Option</label>
                                    <select class="form-control" wire:model.live="paymentOption">
                                        <option value="booking_only">Pay Booking Price Only
                                            (£{{ number_format($bookingPrice, 2) }})</option>
                                        <option value="full">Pay Full Amount
                                            (£{{ number_format($totalAmount + $bookingPrice, 2) }})</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <select class="form-control" wire:model.live="paymentMethod">
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="card">Card Payment</option>
                                        <option value="cash">Cash</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        @if ($paymentMethod === 'bank_transfer')
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">Bank Transfer Details</h6>
                                    <p class="mb-1"><strong>Account Name:</strong> Netsoftuk Solution</p>
                                    <p class="mb-1"><strong>Account Number:</strong> 17855008</p>
                                    <p class="mb-1"><strong>Sort Code:</strong> 04-06-05</p>

                                    <div class="form-group mt-3">
                                        <label class="form-label">Reference Number</label>
                                        <input type="text" class="form-control" wire:model="bankTransferReference"
                                            placeholder="Enter bank transfer reference">
                                        @error('bankTransferReference')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Update Button -->
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                                <span wire:loading wire:target="updateBooking">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                    Processing...
                                </span>
                                <span wire:loading.remove>
                                    Update Booking
                                </span>
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <style>
        .room-item {
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .room-item:hover {
            background-color: #f8f9fa;
            border-color: #0d6efd;
        }

        .room-item.selected {
            background-color: #e8f0fe;
            border-color: #0d6efd;
        }

        .z-10 {
            z-index: 1000;
        }
    </style>
</div>
