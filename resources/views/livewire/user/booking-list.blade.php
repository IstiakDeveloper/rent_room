<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h4 class="mb-0">
                <i class="fas fa-list mr-2" style="color: #252525;"></i>My Bookings
            </h4>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3">Package Details</th>
                            <th class="py-3">Duration</th>
                            <th class="py-3">Amount</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium mb-1"
                                            style="color: #252525;">{{ $booking->package->name }}</span>
                                        <span class="text-muted small">
                                            <i class="fas fa-map-marker-alt mr-1" style="color: #252525;"></i>
                                            {{ $booking->package->address }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Duration -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="mb-2">
                                            <span class="badge text-white" style="background-color: #252525;">
                                                <i class="fas fa-calendar-check mr-1"></i>
                                                {{ \Carbon\Carbon::parse($booking->from_date)->format('d M Y') }}
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="badge text-white" style="background-color: #404040;">
                                                <i class="fas fa-calendar-times mr-1"></i>
                                                {{ \Carbon\Carbon::parse($booking->to_date)->format('d M Y') }}
                                            </span>
                                        </div>
                                        <span class="text-muted small">
                                            <i class="fas fa-clock mr-1" style="color: #252525;"></i>
                                            {{ $booking->number_of_days }} Days
                                        </span>
                                    </div>
                                </td>

                                <!-- Amount -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold"
                                            style="color: #252525;">£{{ number_format($booking->total_amount, 2) }}</span>
                                        @php
                                            $remainingAmount =
                                                $booking->price + $booking->booking_price - $booking->total_amount;
                                        @endphp
                                        @if ($remainingAmount > 0)
                                            <span class="small" style="color: #252525;">
                                                Due: £{{ number_format($remainingAmount, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="text-center">
                                    @php
                                        $statusColor = match ($booking->payment_status) {
                                            'finished' => '#252525',
                                            'cancelled' => '#404040',
                                            'pending' => '#666666',
                                            default => '#808080',
                                        };
                                    @endphp
                                    <div class="d-inline-flex flex-column align-items-center">
                                        <div class="status-indicator mb-2"
                                            style="background-color: {{ $statusColor }};"></div>
                                        <span class="badge text-white" style="background-color: {{ $statusColor }};">
                                            {{ ucfirst($booking->payment_status) }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('bookings.show', ['id' => $booking->id]) }}"
                                            class="btn btn-sm text-white" style="background-color: #252525;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if ($booking->payment_status !== 'cancelled')
                                            <a href="{{ route('bookings.show', ['id' => $booking->id]) }}"
                                                class="btn btn-sm text-white ml-2" style="background-color: #404040;">
                                                <i class="fas fa-redo"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-calendar-times fa-3x mb-3" style="color: #252525;"></i>
                                        <h6 style="color: #252525;">No bookings found</h6>
                                        <a href="{{ route('packages') }}" class="btn text-white mt-2"
                                            style="background-color: #252525;">
                                            <i class="fas fa-search mr-1"></i>Browse Packages
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        .table> :not(caption)>*>* {
            padding: 1rem;
        }

        .badge {
            padding: 0.5rem 0.8rem;
            font-weight: 500;
        }

        .btn {
            transition: opacity 0.3s ease;
        }

        .btn:hover {
            opacity: 0.9;
        }

        /* Custom hover effect for table rows */
        .table-hover tbody tr:hover {
            background-color: rgba(37, 37, 37, 0.05);
        }

        /* Ensure consistent button spacing */
        .btn+.btn {
            margin-left: 0.5rem;
        }

        /* Make sure all icons in badges have consistent spacing */
        .badge i {
            margin-right: 0.25rem;
        }
    </style>

</div>
