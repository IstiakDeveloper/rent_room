<div class="container mt-5">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="update">
        <div class="group-2 form-grid mb-4 room-section">
            <div class="form-group">
                <label for="country_id">Country</label>
                <select wire:model.live.prevent="country_id" id="country_id" class="form-control">
                    <option value="">Select Country</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
                @error('country_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="city_id">City</label>
                <select wire:model.live="city_id" id="city_id" class="form-control">
                    <option value="">Select City</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>
                @error('city_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="area_id">Area</label>
                <select wire:model.live="area_id" id="area_id" class="form-control">
                    <option value="">Select Area</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->name }}</option>
                    @endforeach
                </select>
                @error('area_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="property_id">Property</label>
                <select wire:model.live="property_id" id="property_id" class="form-control">
                    <option value="">Select Property</option>
                    @foreach ($properties as $property)
                        <option value="{{ $property->id }}">{{ $property->name }}</option>
                    @endforeach
                </select>
                @error('property_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="expiration_date">Expiration Date</label>
                <input type="date" id="expiration_date" class="form-control" wire:model="expiration_date" required>
                @error('expiration_date')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="name">Package Name</label>
                <input type="text" wire:model="name" id="name" class="form-control">
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" wire:model="address" id="address" class="form-control">
                @error('address')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="map_link">Map Link</label>
                <input type="text" wire:model="map_link" id="map_link" class="form-control">
                @error('map_link')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="number_of_rooms">Rooms</label>
                <input type="number" wire:model="number_of_rooms" id="number_of_rooms" class="form-control">
                @error('number_of_rooms')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>


            <div class="form-group">
                <label for="number_of_kitchens">Kitchens</label>
                <input type="number" wire:model="number_of_kitchens" id="number_of_kitchens" class="form-control">
                @error('number_of_kitchens')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="common_bathrooms">Common Bathrooms</label>
                <input type="number" wire:model="common_bathrooms" id="common_bathrooms" class="form-control">
                @error('common_bathrooms')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="seating">Seating</label>
                <input type="number" wire:model="seating" id="seating" class="form-control">
                @error('seating')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>


        <div>
            <!-- Selection for Entire Property or Room Wise -->
            <div class="form-group">
                <label for="selection">Select Type</label>
                <input type="text" id="selection" class="form-control"
                    value="{{ $selection === 'entire' ? 'Entire Property' : 'Room Wise' }}" readonly>
            </div>

            <div class="alert alert-info mt-3">
                <strong>Note:</strong> You cannot change the type of this package. If you need a different type, please
                <a href="{{ route('admin.packages.create') }}" class="btn btn-link">create a new package</a>.
            </div>

            <!-- Entire Property Fields -->
            @if ($selection == 'entire')
                <div class="form-group">
                    <h2>Entire Property</h2>
                    <div class="pricing-options form-grid">
                        @foreach ($entireProperty['prices'] as $priceIndex => $price)
                            <div class="pricing-option">
                                <div class="form-group">
                                    <label for="entireProperty-prices-{{ $priceIndex }}-type">Price Type</label>
                                    <select wire:model.live="entireProperty.prices.{{ $priceIndex }}.type"
                                        id="entireProperty-prices-{{ $priceIndex }}-type" class="form-control">
                                        <option value="">Select Option</option>
                                        <option value="Day">Day</option>
                                        <option value="Week">Week</option>
                                        <option value="Month">Month</option>
                                    </select>
                                    @error('entireProperty.prices.' . $priceIndex . '.type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                @if ($price['type'] === 'Day')
                                    <div class="form-group">
                                        <label for="entireProperty-prices-{{ $priceIndex }}-fixed_price">Day Fixed
                                            Price</label>
                                        <input type="number"
                                            wire:model="entireProperty.prices.{{ $priceIndex }}.fixed_price"
                                            id="entireProperty-prices-{{ $priceIndex }}-fixed_price"
                                            class="form-control">
                                        @error('entireProperty.prices.' . $priceIndex . '.fixed_price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="entireProperty-prices-{{ $priceIndex }}-discount_price">Day
                                            Discount Price</label>
                                        <input type="number"
                                            wire:model="entireProperty.prices.{{ $priceIndex }}.discount_price"
                                            id="entireProperty-prices-{{ $priceIndex }}-discount_price"
                                            class="form-control">
                                        @error('entireProperty.prices.' . $priceIndex . '.discount_price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="entireProperty-prices-{{ $priceIndex }}-booking_price">Day
                                            Booking Price</label>
                                        <input type="number"
                                            wire:model="entireProperty.prices.{{ $priceIndex }}.booking_price"
                                            id="entireProperty-prices-{{ $priceIndex }}-booking_price"
                                            class="form-control">
                                        @error('entireProperty.prices.' . $priceIndex . '.booking_price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @elseif($price['type'] === 'Week')
                                    <div class="form-group">
                                        <label for="entireProperty-prices-{{ $priceIndex }}-fixed_price">Week Fixed
                                            Price</label>
                                        <input type="number"
                                            wire:model="entireProperty.prices.{{ $priceIndex }}.fixed_price"
                                            id="entireProperty-prices-{{ $priceIndex }}-fixed_price"
                                            class="form-control">
                                        @error('entireProperty.prices.' . $priceIndex . '.fixed_price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="entireProperty-prices-{{ $priceIndex }}-discount_price">Week
                                            Discount Price</label>
                                        <input type="number"
                                            wire:model="entireProperty.prices.{{ $priceIndex }}.discount_price"
                                            id="entireProperty-prices-{{ $priceIndex }}-discount_price"
                                            class="form-control">
                                        @error('entireProperty.prices.' . $priceIndex . '.discount_price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="entireProperty-prices-{{ $priceIndex }}-booking_price">Week
                                            Booking Price</label>
                                        <input type="number"
                                            wire:model="entireProperty.prices.{{ $priceIndex }}.booking_price"
                                            id="entireProperty-prices-{{ $priceIndex }}-booking_price"
                                            class="form-control">
                                        @error('entireProperty.prices.' . $priceIndex . '.booking_price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @elseif($price['type'] === 'Month')
                                    <div class="form-group">
                                        <label for="entireProperty-prices-{{ $priceIndex }}-fixed_price">Month Fixed
                                            Price</label>
                                        <input type="number"
                                            wire:model="entireProperty.prices.{{ $priceIndex }}.fixed_price"
                                            id="entireProperty-prices-{{ $priceIndex }}-fixed_price"
                                            class="form-control">
                                        @error('entireProperty.prices.' . $priceIndex . '.fixed_price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="entireProperty-prices-{{ $priceIndex }}-discount_price">Month
                                            Discount Price</label>
                                        <input type="number"
                                            wire:model="entireProperty.prices.{{ $priceIndex }}.discount_price"
                                            id="entireProperty-prices-{{ $priceIndex }}-discount_price"
                                            class="form-control">
                                        @error('entireProperty.prices.' . $priceIndex . '.discount_price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="entireProperty-prices-{{ $priceIndex }}-booking_price">Month
                                            Booking Price</label>
                                        <input type="number"
                                            wire:model="entireProperty.prices.{{ $priceIndex }}.booking_price"
                                            id="entireProperty-prices-{{ $priceIndex }}-booking_price"
                                            class="form-control">
                                        @error('entireProperty.prices.' . $priceIndex . '.booking_price')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if (count($entireProperty['prices']) < 3)
                        <button type="button" class="btn btn-secondary" wire:click="addEntirePropertyPrice"><i
                                class="fas fa-plus"></i></button>
                    @endif
                    @if (count($entireProperty['prices']) > 1)
                        <button type="button" class="btn btn-danger"
                            wire:click="removeEntirePropertyPrice({{ $priceIndex }})"><i
                                class="fas fa-times"></i></button>
                    @endif
                </div>
            @endif


            <!-- Room Wise Fields -->
            @if ($selection == 'room')
                <div class="form-group group-2">
                    <h2>Rooms</h2>
                    <div class="mb-4 group-2">
                        @foreach ($rooms as $roomIndex => $room)
                            <div class="room-section mb-4 form-grid-2">
                                <div class="form-group">
                                    <label for="rooms-{{ $roomIndex }}-name">Room Name</label>
                                    <input type="text" wire:model="rooms.{{ $roomIndex }}.name"
                                        id="rooms-{{ $roomIndex }}-name" class="form-control">
                                    @error('rooms.' . $roomIndex . '.name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="rooms-{{ $roomIndex }}-number_of_beds">Beds</label>
                                    <input type="number" wire:model="rooms.{{ $roomIndex }}.number_of_beds"
                                        id="rooms-{{ $roomIndex }}-number_of_beds" class="form-control">
                                    @error('rooms.' . $roomIndex . '.number_of_beds')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="rooms-{{ $roomIndex }}-number_of_bathrooms">Attach
                                        Bathrooms</label>
                                    <input type="number" wire:model="rooms.{{ $roomIndex }}.number_of_bathrooms"
                                        id="rooms-{{ $roomIndex }}-number_of_bathrooms" class="form-control">
                                    @error('rooms.' . $roomIndex . '.number_of_bathrooms')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Pricing Options -->
                            <div class="pricing-options form-grid">
                                @foreach ($room['prices'] as $priceIndex => $price)
                                    <div class="pricing-option">
                                        <div class="form-group">
                                            <label
                                                for="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-type">Price
                                                Type</label>
                                            <select
                                                wire:model.live="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.type"
                                                id="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-type"
                                                class="form-control">
                                                <option value="">Select Option</option>
                                                <option value="Day">Day</option>
                                                <option value="Week">Week</option>
                                                <option value="Month">Month</option>
                                            </select>
                                            @error('rooms.' . $roomIndex . '.prices.' . $priceIndex . '.type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        @if ($price['type'] === 'Day')
                                            <div class="form-group">
                                                <label
                                                    for="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-fixed_price">Day
                                                    Fixed Price</label>
                                                <input type="number"
                                                    wire:model="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.fixed_price"
                                                    id="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-fixed_price"
                                                    class="form-control">
                                                @error('rooms.' . $roomIndex . '.prices.' . $priceIndex .
                                                    '.fixed_price')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    for="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-discount_price">Day
                                                    Discount Price</label>
                                                <input type="number"
                                                    wire:model="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.discount_price"
                                                    id="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-discount_price"
                                                    class="form-control">
                                                @error('rooms.' . $roomIndex . '.prices.' . $priceIndex .
                                                    '.discount_price')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    for="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-booking_price">Day
                                                    Booking Price</label>
                                                <input type="number"
                                                    wire:model="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.booking_price"
                                                    id="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-booking_price"
                                                    class="form-control">
                                                @error('rooms.' . $roomIndex . '.prices.' . $priceIndex .
                                                    '.booking_price')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @elseif($price['type'] === 'Week')
                                            <div class="form-group">
                                                <label
                                                    for="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-fixed_price">Week
                                                    Fixed Price</label>
                                                <input type="number"
                                                    wire:model="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.fixed_price"
                                                    id="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-fixed_price"
                                                    class="form-control">
                                                @error('rooms.' . $roomIndex . '.prices.' . $priceIndex .
                                                    '.fixed_price')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    for="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-discount_price">Week
                                                    Discount Price</label>
                                                <input type="number"
                                                    wire:model="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.discount_price"
                                                    id="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-discount_price"
                                                    class="form-control">
                                                @error('rooms.' . $roomIndex . '.prices.' . $priceIndex .
                                                    '.discount_price')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    for="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-booking_price">Week
                                                    Booking Price</label>
                                                <input type="number"
                                                    wire:model="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.booking_price"
                                                    id="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-booking_price"
                                                    class="form-control">
                                                @error('rooms.' . $roomIndex . '.prices.' . $priceIndex .
                                                    '.booking_price')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @elseif($price['type'] === 'Month')
                                            <div class="form-group">
                                                <label
                                                    for="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-fixed_price">Month
                                                    Fixed Price</label>
                                                <input type="number"
                                                    wire:model="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.fixed_price"
                                                    id="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-fixed_price"
                                                    class="form-control">
                                                @error('rooms.' . $roomIndex . '.prices.' . $priceIndex .
                                                    '.fixed_price')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    for="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-discount_price">Month
                                                    Discount Price</label>
                                                <input type="number"
                                                    wire:model="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.discount_price"
                                                    id="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-discount_price"
                                                    class="form-control">
                                                @error('rooms.' . $roomIndex . '.prices.' . $priceIndex .
                                                    '.discount_price')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    for="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-booking_price">Month
                                                    Booking Price</label>
                                                <input type="number"
                                                    wire:model="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.booking_price"
                                                    id="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-booking_price"
                                                    class="form-control">
                                                @error('rooms.' . $roomIndex . '.prices.' . $priceIndex .
                                                    '.booking_price')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <!-- Add/Remove Pricing Options -->
                            <div>
                                <button type="button" class="btn btn-secondary"
                                    wire:click="addPriceOption({{ $roomIndex }})"><i
                                        class="fas fa-plus"></i></button>
                                @if (count($room['prices']) > 1)
                                    <button type="button" class="btn btn-danger"
                                        wire:click="removePriceOption({{ $roomIndex }}, {{ $priceIndex }})"><i
                                            class="fas fa-times"></i></button>
                                @endif
                            </div>

                            <!-- Remove Room -->
                            <button type="button" class="btn btn-danger"
                                wire:click="removeRoom({{ $roomIndex }})"><i class="fas fa-times"></i></button>
                        @endforeach
                    </div>

                    <!-- Add Room -->
                    <button type="button" class="btn btn-secondary" wire:click="addRoom"><i
                            class="fas fa-plus"></i> Add Room</button>
                </div>
            @endif
        </div>



        {{-- <!-- Container for the form -->
            <div class="container my-4">
                <div class="row">
                    <!-- Column for Free Maintains -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="freeMaintains" class="form-label">Select Free Maintains</label>
                            @foreach ($maintains as $maintain)
                                <div class="form-check">
                                    <input type="checkbox" wire:model="freeMaintains" value="{{ $maintain->id }}" id="maintain{{ $maintain->id }}" class="form-check-input">
                                    <label for="maintain{{ $maintain->id }}" class="form-check-label">{{ $maintain->name }}</label>
                                </div>
                            @endforeach
                            @error('freeMaintains') <div class="text-danger mt-2">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Column for Free Amenities -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="freeAmenities" class="form-label">Select Free Amenities</label>
                            @foreach ($amenities as $amenity)
                                <div class="form-check">
                                    <input type="checkbox" wire:model="freeAmenities" value="{{ $amenity->id }}" id="amenity{{ $amenity->id }}" class="form-check-input">
                                    <label for="amenity{{ $amenity->id }}" class="form-check-label">{{ $amenity->name }}</label>
                                </div>
                            @endforeach
                            @error('freeAmenities') <div class="text-danger mt-2">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div> --}}


        {{-- <div class="form-group group-2">
                <h2>Paid Maintains</h2>
                <div class="form-grid">
                    @foreach ($paidMaintains as $index => $maintain)
                    <div class="room-section mb-4">
                        <div class="form-group">
                            <label for="paidMaintains-{{ $index }}-maintain_id">Select Maintain</label>
                            <select wire:model="paidMaintains.{{ $index }}.maintain_id" id="paidMaintains-{{ $index }}-maintain_id" class="form-control">
                                <option value="">Select Maintain</option>
                                @foreach ($maintains as $maintainOption)
                                    <option value="{{ $maintainOption->id }}">{{ $maintainOption->name }}</option>
                                @endforeach
                            </select>
                            @error('paidMaintains.' . $index . '.maintain_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="paidMaintains-{{ $index }}-price">Price</label>
                            <input type="number" wire:model="paidMaintains.{{ $index }}.price" id="paidMaintains-{{ $index }}-price" class="form-control">
                            @error('paidMaintains.' . $index . '.price') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    @endforeach
                </div>
                @if (count($paidMaintains) > 1)
                    <button type="button" class="btn btn-lg btn-danger mb-3" wire:click="removePaidMaintain({{ $index }})">Remove Maintain</button>
                @endif
                <button type="button" class="btn btn-lg btn-primary mb-3" wire:click="addPaidMaintain">Add Paid Maintain</button>
            </div>

            <div class="form-group group-2">
                <h2>Paid Amenities</h2>
                <div class="form-grid">
                    @foreach ($paidAmenities as $index => $amenity)
                    <div class="room-section mb-4">
                        <div class="form-group">
                            <label for="paidAmenities-{{ $index }}-amenity_id">Select Amenity</label>
                            <select wire:model="paidAmenities.{{ $index }}.amenity_id" id="paidAmenities-{{ $index }}-amenity_id" class="form-control">
                                <option value="">Select Amenity</option>
                                @foreach ($amenities as $amenityOption)
                                    <option value="{{ $amenityOption->id }}">{{ $amenityOption->name }}</option>
                                @endforeach
                            </select>
                            @error('paidAmenities.' . $index . '.amenity_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="paidAmenities-{{ $index }}-price">Price</label>
                            <input type="number" wire:model="paidAmenities.{{ $index }}.price" id="paidAmenities-{{ $index }}-price" class="form-control">
                            @error('paidAmenities.' . $index . '.price') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    @endforeach
                </div>
                @if (count($paidAmenities) > 1)
                    <button type="button" class="btn btn-lg btn-danger mb-3" wire:click="removePaidAmenity({{ $index }})">Remove Amenity</button>
                @endif
                <button type="button" class="btn btn-lg btn-primary mb-3" wire:click="addPaidAmenity">Add Paid Amenity</button>
            </div> --}}

            <div class="form-group group-2">
                <h2 class="mb-4">Package Photos</h2>

                {{-- Existing Photos --}}
                <div class="row mb-4">
                    @foreach ($storedPhotos as $photo)
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card h-100 position-relative">
                                <img src="{{ Storage::url($photo['url']) }}"
                                     class="card-img-top img-fluid"
                                     style="height: 200px; object-fit: cover;"
                                     alt="Package Photo">
                                <button type="button"
                                        wire:click="removeStoredPhoto({{ $photo['id'] }})"
                                        class="btn btn-danger btn-sm position-absolute"
                                        style="top: 10px; right: 10px;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Upload New Photos --}}
                <div class="card mb-4">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                        <h5>Upload New Photos</h5>
                        <input type="file"
                               wire:model="photos"
                               multiple
                               id="photo-upload"
                               class="form-control">
                        @error('photos')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- New Photos Preview --}}
                @if ($photos)
                    <div class="row">
                        @foreach ($photos as $photo)
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="card h-100">
                                    <img src="{{ $photo->temporaryUrl() }}"
                                         class="card-img-top img-fluid"
                                         style="height: 200px; object-fit: cover;"
                                         alt="Photo Preview">
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
             </div>

        <div class="form-group group-2">
            <label for="video_link">Video Link</label>
            <input type="text" id="video_link" wire:model="video_link" class="form-control">
            @error('video_link')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group group-2">
            <div class="form-group">
                <label for="details">Package Details</label>
                <textarea wire:model="details" id="details" class="form-control"></textarea>
                @error('details')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-success">Update</button>
    </form>


    <style>
        .card {
           transition: transform 0.2s;
           box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .card:hover {
           transform: translateY(-5px);
           box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-danger {
           opacity: 0.9;
           border-radius: 50%;
           width: 30px;
           height: 30px;
           padding: 0;
           display: flex;
           align-items: center;
           justify-content: center;
        }

        #photo-upload {
           max-width: 300px;
           margin: 0 auto;
        }
        </style>
</div>
