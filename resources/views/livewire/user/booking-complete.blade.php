<div>
    <main id="content">
        <section class="pb-4 shadow-xs-5">
          <div class="container">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb pt-6 pt-lg-2 lh-15 pb-5">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Payment Completed</li>
              </ol>
            </nav>
            <h1 class="fs-30 lh-1 mb-0 text-heading font-weight-600 mb-6">Payment Completed</h1>
          </div>
        </section>
        <section class="pt-8 pb-11">
          <div class="container">
            <div class="row">
              <div class="col-md-4 col-sm-8 mb-6 mb-md-0">
                <h4 class="text-heading fs-22 font-weight-500 lh-15">My Order</h4>
                <ul class="list-unstyled">
                    <li class="d-flex justify-content-between lh-22">
                      <p class="text-gray-light mb-0">Stay:</p>
                      <p class="font-weight-500 text-heading mb-0">{{ $booking->number_of_days }} Days</p>
                  </li>
                    <li class="d-flex justify-content-between lh-22">
                        <p class="text-gray-light mb-0">Total:</p>
                        <p class="font-weight-500 text-heading mb-0">£{{ $booking->price + $booking->booking_price }}</p>
                    </li>
                    <li class="d-flex justify-content-between lh-22">
                        <p class="text-gray-light mb-0">Paid:</p>
                        <p class="font-weight-500 text-heading mb-0">{{ $booking->total_amount }}</p>
                    </li>
                    <li class="d-flex justify-content-between lh-22">
                        <p class="text-gray-light mb-0">Payment Method:</p>
                        <p class="font-weight-500 text-heading mb-0">{{ $payment->payment_method }}</p>
                    </li>
                    <li class="d-flex justify-content-between lh-22">
                        <p class="text-gray-light mb-0">Payment Type:</p>
                        <p class="font-weight-500 text-heading mb-0">{{ $booking->price_type }}</p>
                    </li>
                    @if ($booking->package)
                    <li class="d-flex justify-content-between lh-22">
                        <p class="text-gray-light mb-0">Package Name:</p>
                        <p class="font-weight-500 text-heading mb-0">{{ $booking->package->name }}</p>
                    </li>
                    @if ($booking->package->rooms->isNotEmpty())
                    <li class="d-flex justify-content-between lh-22">
                        <p class="text-gray-light mb-0">Rooms:</p>
                        <p class="font-weight-500 text-heading mb-0">
                            @foreach ($booking->package->rooms as $room)
                                {{ $room->name }}@if (!$loop->last), @endif
                            @endforeach
                        </p>
                    </li>
                    @endif
                    @endif

                    <li class="d-flex justify-content-between lh-22">
                        <p class="text-gray-light mb-0">Due Bill:</p>
                        <p class="font-weight-500 text-heading mb-0">£{{ $booking->price + $booking->booking_price - $booking->total_amount }}</p>
                    </li>
                </ul>
              </div>
              <div class="col-md-7 offset-md-1">
                <h4 class="text-heading fs-22 font-weight-500 lh-15">Thank you for your booking</h4>
                <a href="{{route('dashboard')}}" class="btn btn-primary px-4 py-2 lh-238">Go to Dashboard</a>
              </div>
            </div>
          </div>
        </section>
      </main>

</div>
