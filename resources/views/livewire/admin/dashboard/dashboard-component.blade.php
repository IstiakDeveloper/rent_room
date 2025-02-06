<div class="container-fluid">
    <div class="row">
        <!-- Total Users -->
        @role('Super Admin|Admin')
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="mr-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title">Total Users</h5>
                            <p class="card-text" style="font-size: 1.5rem; font-weight: bold;">{{ $totalUsers }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endrole

        <!-- Total Partners -->
        @role('Super Admin|Admin')
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card text-white bg-success h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="mr-3">
                            <i class="fas fa-handshake fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title">Total Partners</h5>
                            <p class="card-text" style="font-size: 1.5rem; font-weight: bold;">{{ $totalPartner }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endrole

        <!-- Total Packages -->
        @role('Super Admin|Admin')
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card text-white bg-info h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="mr-3">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title">Total Packages</h5>
                            <p class="card-text" style="font-size: 1.5rem; font-weight: bold;">{{ $totalPackages }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endrole

        @role('User')
            <div class="container-fluid">
                <!-- User Overview Section -->
                <div class="row">
                    <!-- My Active Packages -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card border-left-primary h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="mr-3">
                                        <i class="fas fa-box-open fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title text-primary mb-0">My Active Packages</h6>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h2 class="mb-0 font-weight-bold">{{ $activePackages }}</h2>
                                    <a href="{{ route('user.bookings.index') }}" class="btn btn-sm btn-primary mt-3">View
                                        Details</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Bookings -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card border-left-success h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="mr-3">
                                        <i class="fas fa-calendar-alt fa-2x text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title text-success mb-0">Upcoming Bookings</h6>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h2 class="mb-0 font-weight-bold">{{ $upcomingBookings }}</h2>
                                    <a href="" class="btn btn-sm btn-success mt-3">View Schedule</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Spent -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card border-left-info h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="mr-3">
                                        <i class="fas fa-pound-sign fa-2x text-info"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title text-info mb-0">Total Spent</h6>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h2 class="mb-0 font-weight-bold">£{{ number_format($totalSpent, 2) }}</h2>
                                    <span class="text-muted small">Lifetime spending</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Quick Actions</h5>
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="" class="btn btn-light btn-block py-3">
                                            <i class="fas fa-search mb-2"></i>
                                            <br>Browse Packages
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="{{ route('package.list') }}" class="btn btn-light btn-block py-3">
                                            <i class="fas fa-calendar-plus mb-2"></i>
                                            <br>New Booking
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="{{ route('profile') }}" class="btn btn-light btn-block py-3">
                                            <i class="fas fa-user-edit mb-2"></i>
                                            <br>Edit Profile
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="" class="btn btn-light btn-block py-3">
                                            <i class="fas fa-question-circle mb-2"></i>
                                            <br>Get Support
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="card-title mb-0">Recent Bookings</h5>
                                    <a href="{{ route('user.bookings.index') }}" class="btn btn-sm btn-primary">View All</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Package Name</th>
                                                <th>Created Date</th>
                                                <th>Payment Status</th>
                                                <th>Amount</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentBookings as $booking)
                                                <tr>
                                                    <td>#{{ $booking->id }}</td>
                                                    <td>{{ $booking->package->name }}</td>
                                                    <td>{{ $booking->created_at->format('d M Y') }}</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-{{ $booking->payment_status === 'completed' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($booking->payment_status) }}
                                                        </span>
                                                    </td>
                                                    <td>£{{ number_format($booking->total_amount, 2) }}</td>
                                                    <td>
                                                        <a href="{{ route('bookings.show', $booking) }}"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">No recent bookings found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endrole
    </div>

    <div class="row mt-5">
        <!-- Total Bookings -->
        @role('Super Admin')
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card text-white bg-warning h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="mr-3">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title">Total Bookings</h5>
                            <p class="card-text" style="font-size: 1.5rem; font-weight: bold;">{{ $totalBookings }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endrole

        <!-- Monthly Revenue -->
        @role('Super Admin')
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card text-white bg-danger h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="mr-3">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title">Monthly Revenue</h5>
                            <p class="card-text" style="font-size: 1.5rem; font-weight: bold;">
                                £{{ number_format($monthlyRevenue, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endrole

        <!-- Total Booking Revenue -->
        @role('Super Admin')
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card text-white bg-secondary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="mr-3">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title">Total Booking Revenue</h5>
                            <p class="card-text" style="font-size: 1.5rem; font-weight: bold;">
                                £{{ number_format($totalBookingRevenue, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endrole
    </div>
</div>
