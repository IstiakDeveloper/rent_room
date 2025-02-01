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
                    <!-- Package Details Section -->
                    <section class="package-details py-3 border-bottom">
                        <!-- Package Header -->
                        <div class="row">
                            <!-- Package Info Column -->
                            <div class="col-12 col-md-8 mb-3 mb-md-0">
                                <!-- Package Title and Partner Info -->
                                <div
                                    class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center mb-3">
                                    <h2 class="h4 font-weight-600 text-heading mb-2 mb-sm-0">{{ $package->name }}</h2>

                                    <!-- Partner Info - Moves to new line on extra small screens -->
                                    <div
                                        class="d-flex align-items-center ml-0 ml-sm-4 mt-2 mt-sm-0 border-left-sm pl-sm-4">
                                        <!-- Partner Profile Photo -->
                                        <div class="partner-photo">
                                            @if ($package->user && $package->user->profile_photo_path)
                                                <img src="{{ Storage::url($package->user->profile_photo_path) }}"
                                                    alt="{{ $package->user->name }}"
                                                    class="rounded-circle border shadow-sm"
                                                    style="width: 32px; height: 32px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center border shadow-sm"
                                                    style="width: 32px; height: 32px;">
                                                    <i class="fas fa-user-circle"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Partner Name -->
                                        <div class="partner-name">
                                            @if ($package->assignedPartner)
                                                <a href="{{ route('partner.packages', ['partnerSlug' => str_replace(' ', '-', strtolower($package->assignedPartner->name))]) }}"
                                                    class="text-decoration-none">
                                                    <span class="font-weight-medium ml-2 text-success">
                                                        {{ $package->assignedPartner->name }}
                                                    </span>
                                                </a>
                                            @else
                                                <a href="{{ route('partner.packages', ['partnerSlug' => str_replace(' ', '-', strtolower($package->creator->name))]) }}"
                                                    class="text-decoration-none">
                                                    <span class="font-weight-medium ml-2 text-success">
                                                        {{ $package->creator->name }}
                                                    </span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Address and Map Link -->
                                <div class="d-flex flex-wrap align-items-center text-muted mb-2">
                                    <div class="d-flex align-items-center mr-3 mb-2 mb-sm-0">
                                        <i class="fal fa-map-marker-alt mr-2"></i>
                                        <span class="text-break">{{ $package->address }}</span>
                                    </div>
                                    @if ($package->map_link)
                                        <a href="{{ $package->map_link }}" target="_blank"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-map mr-1"></i>View Map
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Price Display Column -->
                            <div class="col-12 col-md-4">
                                <div class="price-section text-left text-md-right">
                                    @php
                                        $roomPrices = $package->rooms->flatMap(function ($room) {
                                            return $room->roomPrices;
                                        });
                                        $firstPrice = $roomPrices->first();
                                        $priceType = $firstPrice ? $firstPrice->type : null;
                                    @endphp

                                    @if ($firstPrice)
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary w-100 w-md-auto position-relative"
                                                type="button" id="priceDropdown" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex flex-column align-items-start">
                                                        <!-- Main Price -->
                                                        <span class="h5 mb-0 font-weight-bold">
                                                            @if ($firstPrice->discount_price)
                                                                £{{ number_format($firstPrice->discount_price, 2) }}
                                                            @else
                                                                £{{ number_format($firstPrice->fixed_price, 2) }}
                                                            @endif
                                                        </span>
                                                        <!-- Deleted Price and Rate Type -->
                                                        <div class="d-flex align-items-center">
                                                            @if ($firstPrice->discount_price)
                                                                <del
                                                                    class="small text-muted mr-1">£{{ number_format($firstPrice->fixed_price, 2) }}</del>
                                                            @endif
                                                            <small class="text-muted">{{ ucfirst($priceType) }}
                                                                Rate</small>
                                                        </div>
                                                    </div>
                                                    <!-- Dropdown Icon -->
                                                    <i class="fas fa-chevron-down ml-2"></i>
                                                </div>
                                            </button>

                                            <!-- Price Dropdown Menu -->
                                            <div class="dropdown-menu dropdown-menu-right p-3 w-100"
                                                style="min-width: 280px; max-width: 100%; max-height: 400px; overflow-y: auto;">
                                                @foreach ($package->rooms as $room)
                                                    <div class="room-price-item mb-3">
                                                        <h6 class="border-bottom pb-2">
                                                            <i class="fas fa-bed mr-2 text-primary"></i>
                                                            {{ $room->name }}
                                                        </h6>
                                                        @php
                                                            $pricesByType = $room->roomPrices->groupBy('type');
                                                        @endphp
                                                        @foreach ($pricesByType as $type => $prices)
                                                            <div class="price-type-group mb-2">
                                                                <div class="text-muted small mb-1">{{ ucfirst($type) }}
                                                                    Rates</div>
                                                                @foreach ($prices as $price)
                                                                    <div
                                                                        class="price-item d-flex justify-content-between align-items-center py-1">
                                                                        <div class="price-amount">
                                                                            @if ($price->discount_price)
                                                                                <del
                                                                                    class="text-muted small">£{{ number_format($price->fixed_price, 2) }}</del>
                                                                                <span
                                                                                    class="text-success">£{{ number_format($price->discount_price, 2) }}</span>
                                                                            @else
                                                                                <span>£{{ number_format($price->fixed_price, 2) }}</span>
                                                                            @endif
                                                                        </div>
                                                                        <small class="text-muted booking-fee">
                                                                            +£{{ number_format($price->booking_price, 2) }}
                                                                            booking fee
                                                                        </small>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Description Section -->
                        <div class="description-section mt-4">
                            <h4 class="h5 text-heading mb-3">Description</h4>
                            <div class="description-content">
                                @php
                                    $words = explode(' ', $package->details);
                                    $limitedWords = array_slice($words, 0, 50);
                                    $remainingWords = array_slice($words, 50);
                                @endphp
                                <p class="mb-0 text-break">
                                    {{ implode(' ', $viewMore ? $words : $limitedWords) }}
                                    @if (count($words) > 50)
                                        <span wire:click="toggleViewMore" class="text-primary cursor-pointer">
                                            {{ $viewMore ? 'View less' : 'View more' }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </section>

                    <style>
                        /* Custom CSS for enhanced mobile responsiveness */
                        @media (max-width: 575.98px) {
                            .package-details h2 {
                                font-size: 1.25rem;
                            }

                            .border-left-sm {
                                border-left: none !important;
                            }

                            .price-section {
                                margin-top: 1rem;
                            }

                            .dropdown-menu {
                                width: 100%;
                                min-width: 100% !important;
                                margin-top: 0.5rem;
                            }

                            .price-item {
                                flex-direction: column;
                                align-items: flex-start !important;
                            }

                            .booking-fee {
                                margin-top: 0.25rem;
                                margin-left: 0.5rem;
                            }
                        }

                        @media (min-width: 576px) {
                            .border-left-sm {
                                border-left: 1px solid #dee2e6;
                            }
                        }

                        /* General styling improvements */
                        .package-details {
                            background-color: #fff;
                        }

                        .cursor-pointer {
                            cursor: pointer;
                        }

                        .text-break {
                            word-break: break-word;
                        }

                        .description-content {
                            line-height: 1.6;
                        }

                        /* Dropdown improvements */
                        .dropdown-menu {
                            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                        }

                        .room-price-item:last-child {
                            margin-bottom: 0;
                        }

                        .price-type-group:last-child {
                            margin-bottom: 0;
                        }

                        #priceDropdown {
                            padding: 0.5rem 1rem;
                        }

                        #priceDropdown .h5 {
                            line-height: 1.2;
                            color: #333;
                        }

                        #priceDropdown .fa-chevron-down {
                            font-size: 0.875rem;
                            transition: transform 0.2s ease;
                        }

                        #priceDropdown[aria-expanded="true"] .fa-chevron-down {
                            transform: rotate(180deg);
                        }

                        @media (max-width: 767.98px) {
                            #priceDropdown {
                                text-align: left;
                            }
                        }
                    </style>


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


                            </div>
                        </div>
                    </section>
                </article>

                <aside class="col-lg-4 pl-xl-4 primary-sidebar sidebar-sticky" id="sidebar">
                    <div class="primary-sidebar-inner">
                        <div class="card border-0 widget-request-tour">
                            <div class="card-body px-sm-6 shadow-xxs-2 pb-5 pt-0">
                                <form wire:submit.prevent="submit">
                                    <div class="tab-content pt-1 pb-0 px-0 shadow-none">
                                        <div id="signupform" class="tab-pane fade show active" role="tabpanel">
                                            @if (session('error'))
                                                <div class="alert alert-warning">{{ session('error') }}</div>
                                            @endif

                                            @unless (Auth::check())
                                                <div class="text-center mb-4">
                                                    <p>Please <a class="text-primary fw-bold" href="#signInModal"
                                                            data-toggle="modal">Sign in</a>
                                                        if not <a class="text-primary fw-bold" href="#signUpModal"
                                                            data-toggle="modal">Sign up</a></p>
                                                </div>
                                            @endunless

                                            <div class="mb-4">
                                                <div class="position-relative">
                                                    <div class="dropdown">
                                                        <button
                                                            class="btn btn-primary btn-lg w-100 dropdown-toggle d-flex align-items-center justify-content-between"
                                                            type="button" id="roomSelectButton"
                                                            data-toggle="dropdown"
                                                            {{ !Auth::check() ? 'disabled' : '' }}>
                                                            <span>{{ $selectedRoom ? $package->rooms->find($selectedRoom)->name : 'Select Your Place' }}</span>
                                                            <i class="fas fa-chevron-down"></i>
                                                        </button>
                                                        <div class="dropdown-menu w-100"
                                                            aria-labelledby="roomSelectButton">
                                                            @foreach ($package->rooms as $room)
                                                                <a class="dropdown-item {{ $selectedRoom == $room->id ? 'active' : '' }}"
                                                                    wire:click="selectRoom({{ $room->id }})">
                                                                    {{ $room->name }} • {{ $room->number_of_beds }}
                                                                    Beds • {{ $room->number_of_bathrooms }} Baths
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    @if (!Auth::check())
                                                        <div class="overlay auth-overlay"
                                                            wire:click="showAuthMessage('room')"
                                                            style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;">
                                                        </div>
                                                    @endif
                                                </div>
                                                @if ($showAuthWarning === 'room')
                                                    <span class="text-danger">Please sign in or sign up first.</span>
                                                @endif

                                                @if ($selectedRoom)
                                                    <div class="selected-room-details mt-3 p-3 bg-light rounded">
                                                        @php $room = $package->rooms->find($selectedRoom) @endphp
                                                        <div class="d-flex align-items-center">
                                                            <span class="mr-3">{{ $room->name }}</span>
                                                            <span class="text-muted">
                                                                <i class="fas fa-bed"></i> {{ $room->number_of_beds }}
                                                                <i class="fas fa-bath ml-2"></i>
                                                                {{ $room->number_of_bathrooms }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            @if ($selectedRoom && $calendarView)
                                                <div x-data="datePickerComponent({{ json_encode($disabledDates) }})" wire:ignore.self class="mt-4">
                                                    <label class="mb-2">Select Check-in and Check-out Dates</label>
                                                    <input x-ref="dateRangePicker" type="text"
                                                        class="form-control" placeholder="Select dates" readonly
                                                        {{ !Auth::check() ? 'disabled' : '' }}>
                                                    @error('dateRange')
                                                        <span class="text-danger small">{{ $message }}</span>
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
                                                <input type="checkbox" class="form-check-input" id="exampleCheck1"
                                                    wire:model="terms" required
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
                                            <button type="submit" class="btn btn-primary btn-lg btn-block rounded"
                                                {{ !Auth::check() ? 'disabled' : '' }}>
                                                Proceed to Checkout
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <style>
                        .dropdown-toggle::after {
                            display: none;
                        }

                        .dropdown-menu {
                            margin-top: 0;
                            border: 1px solid rgba(0, 0, 0, .1);
                            max-height: 300px;
                            overflow-y: auto;
                        }

                        .dropdown-item {
                            padding: .75rem 1rem;
                            cursor: pointer;
                        }

                        .dropdown-item.active {
                            background-color: #f8f9fa;
                            color: #000;
                        }

                        .selected-room-details {
                            border: 1px solid #dee2e6;
                            background-color: #f8f9fa;
                        }

                        .btn-lg {
                            padding: 0.75rem 1.25rem;
                        }
                    </style>
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


    <style>
        .host-avatar img,
        .host-avatar div {
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .host-avatar img:hover,
        .host-avatar div:hover {
            transform: scale(1.05);
        }

        .dropdown-menu {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 0.5rem;
        }

        .badge {
            padding: 0.4em 0.8em;
            font-weight: 500;
        }

        .btn-outline-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .price-details {
            transition: all 0.3s ease;
        }

        .price-details:hover {
            background-color: #f8f9fa;
        }

        .btn-outline-secondary {
            transition: all 0.2s ease;
        }

        .btn-outline-secondary:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 0.5rem;
        }

        .border-left {
            border-left: 1px solid rgba(0, 0, 0, 0.1) !important;
        }

        .text-body {
            color: #4a5568 !important;
        }

        .shadow-sm {
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
        }
    </style>
</div>
