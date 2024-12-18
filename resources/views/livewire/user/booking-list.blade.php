<div class="px-4">
    <div class="mx-auto">
        <table class="table">
            <thead>
                <tr>
                    <th class="d-none d-md-table-cell">SL</th>
                    <th>Package Name</th>
                    <th class="d-none d-md-table-cell">From Date</th>
                    <th class="d-none d-md-table-cell">To Date</th>
                    <th class="d-none d-md-table-cell">Total Amount</th>
                    <th class="d-none d-md-table-cell">Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $index => $booking)
                    <tr @if($booking->payment_status === 'finished') class="bg-success text-white" @elseif($booking->payment_status == 'cancelled') class="bg-danger text-white"  @endif>
                        <td class="d-none d-md-table-cell" data-label="SL">{{ $loop->iteration }}</td>
                        <td data-label="Package Name">{{ $booking->package->name }}</td>
                        <td class="d-none d-md-table-cell" data-label="From Date">{{ $booking->from_date }}</td>
                        <td class="d-none d-md-table-cell" data-label="To Date">{{ $booking->to_date }}</td>
                        <td class="d-none d-md-table-cell" data-label="Total Amount">£{{ $booking->total_amount }}</td>
                        <td class="d-none d-md-table-cell" data-label="Status">{{ $booking->payment_status }}</td>
                        <td class="text-center" data-label="Action">
                            @php
                                $currentDate = \Carbon\Carbon::today(); // Get today's date
                                $toDate = \Carbon\Carbon::parse($booking->to_date); // Convert booking's to_date to a Carbon instance
                            @endphp
                            <a class="btn btn-primary" href="{{ route('bookings.show', ['id' => $booking->id]) }}">
                                <i class="fas fa-eye"></i>
                            </a>
                            {{-- @if ($toDate->isSameDay($currentDate) || $toDate->isBefore($currentDate) && $booking->payment_status != 'finished') --}}
                                <a class="btn btn-secondary ml-2" href="{{ route('bookings.show', ['id' => $booking->id]) }}">
                                    <i class="fas fa-redo"></i>
                                </a>
                            {{-- @endif --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
