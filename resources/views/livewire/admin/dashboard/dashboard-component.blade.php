
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
        <div class="col-12">
            <a href="{{route('user.bookings.index')}}" class="btn btn-primary">Go To My Packages</a>
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
                        <p class="card-text" style="font-size: 1.5rem; font-weight: bold;">£{{ number_format($monthlyRevenue, 2) }}</p>
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
                        <p class="card-text" style="font-size: 1.5rem; font-weight: bold;">£{{ number_format($totalBookingRevenue, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endrole
    </div>
</div>
