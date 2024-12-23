<div class="mb-10">

    @php
        $videoUrl = $package->video_link;
        if (
            preg_match(
                '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i',
                $videoUrl,
                $matches,
            )
        ) {
            $videoId = $matches[1];
        } else {
            $videoId = null;
        }
    @endphp
    <section>
        <div class="container mt-4">
            <button onclick="window.history.back()" class="btn btn-primary">
                &larr; Back
            </button>
        </div>
        <div>


            <div class="container mt-4">
                <!-- Main Image with Lightbox Link -->
                <div class="mb-3">
                    <div class="w">
                        <a href="#img{{ $currentPhotoIndex }}" class="lightbox-trigger"
                            data-imgsrc="{{ asset('storage/' . $package->photos[$currentPhotoIndex]->url) }}">
                            <img src="{{ asset('storage/' . $package->photos[$currentPhotoIndex]->url) }}"
                                class="img-fluid rounded w-100" alt="Large Image"
                                style="max-height: 60vh; object-fit: cover;">
                        </a>
                    </div>
                </div>

                <div class="d-flex package-gallery">
                    @foreach ($package->photos as $index => $photo)
                        <div class="mb-2 mr-2">
                            <a href="#" class="lightbox-trigger"
                                data-imgsrc="{{ asset('storage/' . $photo->url) }}">
                                <img src="{{ asset('storage/' . $photo->url) }}"
                                    class="img-thumbnail package-gallery-img" alt="Thumbnail">
                            </a>
                        </div>
                    @endforeach
                </div>

                <section class="lightbox-container">
                    <span class="fas fa-chevron-left lightbox-btn left" id="left"></span>
                    <span class="fas fa-chevron-right lightbox-btn right" id="right"></span>
                    <span id="close" class="close fas fa-times"></span>
                    <div class="lightbox-image-wrapper">
                        <img alt="lightboximage" class="lightbox-image">
                    </div>
                </section>
            </div>



        </div>
    </section>

    <Section>
        <div class="container py-4 video-section">
            @if ($videoId)
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe src="https://www.youtube.com/embed/{{ $videoId }}" title="YouTube video player"
                        class="embed-responsive-item w-100 h-100 rounded-t-lg mb-4" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
            @else
                <p class="text-center text-danger">Unable to load the video. Please check the video link or try again
                    later.</p>
            @endif
        </div>
    </Section>

    <div class="primary-content pt-2">
        <div class="container">
            <div class="row">
                <article class="col-lg-8 pr-xl-7">
                    <section class="pb-2 border-bottom">
                        <div class="d-sm-flex justify-content-sm-between">
                            <div>
                                <h2 class="font-weight-600 text-heading">{{ $package->name }}</h2>
                                <p class="mb-0">{{ $package->address }} <i class="fal fa-map-marker-alt ml-2"></i>
                                    <a class="ml-3 border px-2 py-1" href="{{ $package->map_link }}"
                                        target="_blank">View Map</a>
                                </p>
                            </div>
                            <div class="mb-2">
                                @php
                                    $roomPrices = $package->rooms->flatMap(function ($room) {
                                        return $room->prices;
                                    });

                                    $roomPriceData = $this->getFirstAvailablePrice($roomPrices);
                                    $roomPrice = $roomPriceData['price'] ?? null;
                                    $roomPriceType = $roomPriceData['type'] ?? null;
                                    $roomPriceIndicator = $roomPriceType
                                        ? $this->getPriceIndicator($roomPriceType)
                                        : '';

                                    $propertyPrices = $package->entireProperty->prices ?? [];
                                    $propertyPriceData = $this->getFirstAvailablePrice($propertyPrices);
                                    $propertyPrice = $propertyPriceData['price'] ?? null;
                                    $propertyPriceType = $propertyPriceData['type'] ?? null;
                                    $propertyPriceIndicator = $propertyPriceType
                                        ? $this->getPropertyPriceIndicator($propertyPriceType)
                                        : '';
                                @endphp

                                @if ($roomPrice)
                                    @if ($roomPrice->discount_price)
                                        <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                            <del class="text-muted mr-2"> £{{ $roomPrice->fixed_price }}</del>
                                            <span class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                                £{{ $roomPrice->discount_price }}</span>
                                            <span class="price-indicate">{{ $roomPriceIndicator }}</span>
                                        </p>
                                    @else
                                        <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                            £{{ $roomPrice->fixed_price }}<span
                                                class="price-indicate">{{ $roomPriceIndicator }}</span></p>
                                    @endif
                                @endif

                                @if ($propertyPrice)
                                    @if ($propertyPrice->discount_price)
                                        <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                            <del class="text-muted mr-2"> £{{ $propertyPrice->fixed_price }}</del>
                                            <span class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                                £{{ $propertyPrice->discount_price }}</span>
                                            <span class="price-indicate">{{ $propertyPriceIndicator }}</span>
                                        </p>
                                    @else
                                        <p class="fs-17 font-weight-bold text-heading mb-0 lh-16">
                                            £{{ $propertyPrice->fixed_price }}<span
                                                class="price-indicate">{{ $propertyPriceIndicator }}</span></p>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <h4 class="text-heading mt-3 mb-2">Description</h4>
                        <p class="mb-0 lh-214">
                            @php
                                $words = explode(' ', $package->details);
                                $limitedWords = array_slice($words, 0, 50);
                                $remainingWords = array_slice($words, 50);
                            @endphp
                            {{ implode(' ', $viewMore ? $words : $limitedWords) }}
                            @if (count($words) > 50)
                                <span wire:click="toggleViewMore"
                                    class="text-primary cursor-pointer">{{ $viewMore ? 'View less' : 'View more' }}</span>
                            @endif
                        </p>
                    </section>


                    <section>
                        <div class="wrapper center-block">
                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                <div class="panel panel-default">
                                    <div class="panel-heading active" role="tab" id="headingOne">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                Features
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel"
                                        aria-labelledby="headingOne">
                                        <div class="panel-body">
                                            <div class="row px-4">
                                                <div class="col-lg-3 col-sm-4 mb-6">
                                                    <div class="media d-flex align-items-center">
                                                        <div class="p-2 shadow-xxs-1 rounded-lg mr-2">
                                                            <i class="fad fa-user-alt fs-32 text-primary"></i>
                                                        </div>
                                                        <div class="media-body ml-2">
                                                            <p class="m-0 fs-13 font-weight-bold text-heading">
                                                                {{ $package->user->name }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-4 mb-6">
                                                    <div class="media d-flex align-items-center">
                                                        <div class="p-2 shadow-xxs-1 rounded-lg mr-2">
                                                            <i class="fad fa-oven fs-32 text-primary"></i>
                                                        </div>
                                                        <div class="media-body ml-2">
                                                            <p class="m-0 fs-13 font-weight-bold text-heading">
                                                                {{ $package->number_of_kitchens }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-4 mb-6">
                                                    <div class="media d-flex align-items-center">
                                                        <div class="p-2 shadow-xxs-1 rounded-lg mr-2">
                                                            <i class="fas fa-loveseat fs-32 text-primary"></i>
                                                        </div>

                                                        <div class="media-body ml-2">
                                                            <p class="m-0 fs-13 font-weight-bold text-heading">
                                                                {{ $package->seating }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 col-sm-4 mb-6">
                                                    <div class="media d-flex align-items-center">

                                                        <div class="p-2 shadow-xxs-1 rounded-lg mr-2">
                                                            <i class="far fa-bed fs-32 text-primary"></i>
                                                        </div>
                                                        <div class="media-body ml-2">
                                                            <p class="m-0 fs-13 font-weight-bold text-heading">
                                                                {{ $package->number_of_rooms }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 col-sm-4 mb-6">
                                                    <div class="media d-flex align-items-center">
                                                        {{-- <div class="p-2 shadow-xxs-1 rounded-lg mr-2">
                                                            <i class="fad fa-toilet fs-32 text-primary"></i>
                                                        </div> --}}
                                                        <div class="p-2 shadow-xxs-1 rounded-lg mr-2">
                                                            <i class="fas fa-bath fs-32 text-primary"></i>
                                                        </div>
                                                        <div class="media-body ml-2">
                                                            <p class="m-0 fs-13 font-weight-bold text-heading">
                                                                {{ $package->common_bathrooms }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel panel-default">
                                    <div class="panel-heading active" role="tab" id="headingTwo">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                                Amenities
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel"
                                        aria-labelledby="headingTwo">
                                        <div class="panel-body">
                                            <div class="px-4">
                                                <ul class="list-unstyled mb-0 row no-gutters">
                                                    @foreach ($package->amenities as $amenity)
                                                        <li class="col-sm-3 col-6 mb-2">
                                                            <i
                                                                class="far fa-check mr-2 text-primary"></i>{{ $amenity->name }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                            <div class="px-4">
                                                <ul class="list-unstyled mb-0 row no-gutters">
                                                    @foreach ($package->amenities()->wherePivot('is_paid', true)->get() as $amenity)
                                                        <li class="list-group-item">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    wire:model="selectedAmenities.{{ $amenity->id }}"
                                                                    value="{{ $amenity->pivot->price }}"
                                                                    id="amenity{{ $amenity->id }}">
                                                                <label class="form-check-label"
                                                                    for="amenity{{ $amenity->id }}">
                                                                    {{ $amenity->name }} -
                                                                    £{{ $amenity->pivot->price }}
                                                                </label>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-heading active" role="tab" id="headingThree">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                href="#collapseThree" aria-expanded="true"
                                                aria-controls="collapseThree">
                                                Maintains
                                            </a>
                                        </h4>
                                    </div>

                                    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel"
                                        aria-labelledby="headingThree">
                                        <div class="panel-body">
                                            <div class="px-4">

                                                <ul class="list-unstyled mb-0 row no-gutters">
                                                    @foreach ($package->maintains()->wherePivot('is_paid', false)->get() as $maintain)
                                                        <li class="col-sm-3 col-6 mb-2">
                                                            <i
                                                                class="far fa-check mr-2 text-primary"></i>{{ $maintain->name }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="px-4">
                                                <ul class="list-unstyled mb-0 row no-gutters">
                                                    @foreach ($package->maintains()->wherePivot('is_paid', true)->get() as $maintain)
                                                        <li class="list-group-item">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    wire:model="selectedMaintains.{{ $maintain->id }}"
                                                                    value="{{ $maintain->pivot->price }}"
                                                                    id="maintain{{ $maintain->id }}">
                                                                <label class="form-check-label"
                                                                    for="maintain{{ $maintain->id }}">
                                                                    {{ $maintain->name }} -
                                                                    £{{ $maintain->pivot->price }}
                                                                </label>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if (!$propertyPrice)
                                    <div class="panel panel-default">
                                        <div class="panel-heading active" role="tab" id="headingFour">
                                            <h4 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                    href="#collapsFour" aria-expanded="true"
                                                    aria-controls="collapsFour">
                                                    Room Prices
                                                </a>
                                            </h4>
                                        </div>

                                        <div id="collapsFour" class="panel-collapse collapse" role="tabpanel"
                                            aria-labelledby="headingFour">
                                            <div class="panel-body">
                                                <div class="accordion accordion-03 mb-3" id="accordion-rooms">
                                                    @foreach ($package->rooms as $index => $room)
                                                        <div class="card border-0 shadow-xxs-2">
                                                            <div class="card-header bg-gray-01 border-gray border-0 p-0"
                                                                id="floor-plans-{{ $index + 1 }}">
                                                                <div class="heading d-flex justify-content-between align-items-center px-6 {{ $index !== 0 ? 'collapsed' : '' }}"
                                                                    data-toggle="collapse"
                                                                    data-target="#collapse-{{ $index + 1 }}"
                                                                    aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                                    aria-controls="collapse-{{ $index + 1 }}"
                                                                    role="button">
                                                                    <h2
                                                                        class="mb-0 fs-16 text-heading font-weight-500 py-4 lh-13">
                                                                        {{ $room->name }}</h2>
                                                                    <ul
                                                                        class="list-inline mb-0 d-none d-sm-block pr-2">
                                                                        <li class="list-inline-item text-muted mr-4">
                                                                            Beds :
                                                                            <span
                                                                                class="font-weight-500 text-heading">{{ $room->number_of_beds }}</span>
                                                                        </li>
                                                                        <li class="list-inline-item text-muted mr-4">
                                                                            Bath :
                                                                            <span
                                                                                class="font-weight-500 text-heading">{{ $room->number_of_bathrooms }}</span>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div id="collapse-{{ $index + 1 }}"
                                                                class="collapse {{ $index === 0 ? 'show' : '' }}"
                                                                aria-labelledby="floor-plans-{{ $index + 1 }}"
                                                                data-parent="#accordion-rooms">
                                                                <div class="card-body col-sm-12 mb-3">
                                                                    @php
                                                                        $pricesByType = $room->roomPrices->groupBy(
                                                                            'type',
                                                                        );
                                                                    @endphp
                                                                    @foreach ($pricesByType as $type => $prices)
                                                                        <div class="mb-4">
                                                                            <h6 class="fs-16 text-heading">
                                                                                {{ ucfirst($type) }} Prices</h6>
                                                                            <table class="table table-bordered">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th scope="col">Price</th>
                                                                                        <th scope="col">Booking</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach ($prices as $price)
                                                                                        <tr>
                                                                                            <td>£{{ number_format($price->discount_price, 2) }}
                                                                                            </td>
                                                                                            <td>£{{ number_format($price->booking_price, 2) }}
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>


                                                        </div>
                                                    @endforeach
                                                    <div class="container my-5">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </section>
                </article>

                <aside class="col-lg-4 pl-xl-4 primary-sidebar sidebar-sticky" id="sidebar">
                    <div class="primary-sidebar-inner">
                        <div class="card border-0 widget-request-tour">
                            <ul class="nav nav-tabs d-flex" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active px-3" data-toggle="tab" href="#schedule"
                                        role="tab" aria-selected="true">Book Your Place</a>
                                </li>
                            </ul>
                            <div class="card-body px-sm-6 shadow-xxs-2 pb-5 pt-0">
                                <form wire:submit.prevent="submit">
                                    <div class="tab-content pt-1 pb-0 px-0 shadow-none">
                                        <div id="signupform" class="tab-pane fade show active" role="tabpanel">

                                            @if (session('error'))
                                                <div class="alert alert-warning">
                                                    {{ session('error') }}
                                                </div>
                                            @endif
                                            @unless (Auth::check())
                                                <p class="mb-4">
                                                    Please <a class="text-primary" href="#signInModal"
                                                        data-toggle="modal">Sign in</a>
                                                    if not <a class="text-primary" href="#signUpModal"
                                                        data-toggle="modal">sign up</a>
                                                </p>
                                            @endunless
                                            <div class="card shadow-sm">
                                                <div class="card-body p-2">
                                                    <h6 class="card-title mb-2"><i class="fas fa-bed mr-2 text-primary"></i>Select Room</h6>

                                                    @if ($package->rooms->count() > 0)
                                                        <div class="room-list">
                                                            @foreach($package->rooms as $room)
                                                                <div wire:key="room-{{ $room->id }}"
                                                                     wire:click="selectRoom({{ $room->id }})"
                                                                     class="room-item d-flex justify-content-between align-items-center p-2 mb-2 rounded-2 {{ $selectedRoom == $room->id ? 'selected' : '' }}">
                                                                    <span class="fw-medium">{{ $room->name }} • <i class="fas fa-bed small"></i> {{ $room->number_of_beds }} • <i class="fas fa-bath small"></i> {{ $room->number_of_bathrooms }}</span>
                                                                    @if($selectedRoom == $room->id)
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


                                                @if ($selectedRoom && $calendarView)
                                                    <div x-data="datePickerComponent({{ json_encode($disabledDates) }})" wire:ignore.self class="mt-6">
                                                        <label class="block mb-2">Select Check-in and Check-out
                                                            Dates</label>
                                                        <input x-ref="dateRangePicker" type="text"
                                                            class="form-control w-full" placeholder="Select dates"
                                                            readonly {{ !Auth::check() ? 'disabled' : '' }}>

                                                        @error('dateRange')
                                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                @endif



                                                <script>
                                                    function datePickerComponent(disabledDates) {
                                                        return {
                                                            disabledDates: disabledDates,
                                                            init() {
                                                                const picker = flatpickr(this.$refs.dateRangePicker, {
                                                                    mode: 'range',
                                                                    dateFormat: 'Y-m-d',
                                                                    minDate: 'today',
                                                                    disable: this.disabledDates.map(date => new Date(date)),
                                                                    onChange: (selectedDates) => {
                                                                        if (selectedDates.length === 2) {
                                                                            // Call Livewire method with selected dates
                                                                            @this.call('selectDates', {
                                                                                start: selectedDates[0].toISOString().split('T')[0],
                                                                                end: selectedDates[1].toISOString().split('T')[0]
                                                                            });
                                                                        }
                                                                    }
                                                                });

                                                                // Watch for changes in disabled dates and update picker
                                                                this.$watch('disabledDates', (newValue) => {
                                                                    picker.set('disable', newValue.map(date => new Date(date)));
                                                                });
                                                            }
                                                        };
                                                    }
                                                </script>



                                                <!-- Phone Number -->
                                                <div class="form-group mb-2">
                                                    <label for="phone">Phone Number</label><span
                                                        class="text-danger">*</span>
                                                    <div class="position-relative">
                                                        <input type="text" id="phone"
                                                            class="form-control form-control-lg border-0"
                                                            wire:model="phone" placeholder="Your Phone" required
                                                            {{ !Auth::check() ? 'disabled' : '' }}>
                                                        @if (!Auth::check())
                                                            <div class="overlay auth-overlay"
                                                                wire:click="showAuthMessage('phone')"
                                                                style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;">
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @error('phone')
                                                        <span class="error text-danger">{{ $message }}</span>
                                                    @enderror
                                                    @if ($showAuthWarning === 'phone')
                                                        <span class="text-danger">Please sign in or sign up
                                                            first.</span>
                                                    @endif
                                                </div>

                                                <!-- Terms & Conditions -->
                                                <div class="form-group form-check mt-2 mb-4">
                                                    <input type="checkbox" class="form-check-input"
                                                        id="exampleCheck1" wire:model="terms" required
                                                        {{ !Auth::check() ? 'disabled' : '' }}>
                                                    <label class="form-check-label fs-13" for="exampleCheck1">I agree
                                                        to
                                                        the</label>
                                                    <a href="#" class="text-danger" id="openModal">Terms &
                                                        Conditions</a>
                                                    @error('terms')
                                                        <span
                                                            class="error text-danger d-block mt-1">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <!-- Error Messages -->
                                                @if ($errors->any())
                                                    <div class="alert alert-danger">
                                                        <ul class="mb-0">
                                                            @foreach ($errors->all() as $error)
                                                                <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif

                                                <!-- Submit Button -->
                                                <button type="submit"
                                                    class="btn btn-primary btn-lg btn-block rounded"
                                                    {{ !Auth::check() ? 'disabled' : '' }}>
                                                    Proceed to Checkout
                                                </button>
                                            </div>
                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </aside>



            </div>
        </div>
    </div>


    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @livewire('user.terms-condition-component')
                </div>
            </div>
        </div>
    </div>

</div>
