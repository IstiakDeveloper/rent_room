<?php

namespace App\Livewire\Admin\Package;

use App\Models\Amenity;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\EntireProperty;
use App\Models\Maintain;
use App\Models\Package;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomPrice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditPackageComponent extends Component
{
    use WithFileUploads;

    public $packageId;
    public $countries;
    public $cities = [];
    public $areas = [];
    public $properties;
    public $maintains;
    public $amenities;
    public $country_id;
    public $city_id;
    public $area_id;
    public $property_id;
    public $name;
    public $address;
    public $map_link;
    public $number_of_kitchens;
    public $number_of_rooms;
    public $common_bathrooms;
    public $seating;
    public $details;
    public $video_link;
    public $rooms = [];
    public $freeMaintains = [];
    public $freeAmenities = [];
    public $paidMaintains = [];
    public $paidAmenities = [];
    public $storedPhotos = [];
    public $photos = [];
    public $expiration_date;
    public $selection;
    public $package;
    public $entireProperty = [
        'prices' => [
            ['type' => '', 'fixed_price' => 0, 'discount_price' => null, 'booking_price' => 0]
        ],
    ];

    protected $rules = [
        'country_id' => 'required',
        'city_id' => 'required',
        'area_id' => 'required',
        'property_id' => 'required',
        'name' => 'required|string',
        'address' => 'required|string',
        'map_link' => 'nullable|string',
        'number_of_kitchens' => 'required|integer',
        'number_of_rooms' => 'required|integer',
        'common_bathrooms' => 'required|integer',
        'seating' => 'required|integer',
        'details' => 'nullable|string',
        'rooms.*.name' => 'string',
        'rooms.*.number_of_beds' => 'integer',
        'rooms.*.number_of_bathrooms' => 'integer',
        'rooms.*.prices.*.type' => 'in:Day,Week,Month',
        'rooms.*.prices.*.fixed_price' => 'numeric',
        'rooms.*.prices.*.discount_price' => 'nullable|numeric',
        'rooms.*.prices.*.booking_price' => 'numeric',
        'paidMaintains.*.maintain_id' => 'required|exists:maintains,id',
        'paidMaintains.*.price' => 'required|numeric',
        'paidAmenities.*.amenity_id' => 'required|exists:amenities,id',
        'paidAmenities.*.price' => 'required|numeric',
        'photos.*' => 'nullable|image',
        'entireProperty.prices.*.type' => 'in:Day,Week,Month',
        'entireProperty.prices.*.fixed_price' => 'numeric',
        'entireProperty.prices.*.discount_price' => 'nullable|numeric',
        'entireProperty.prices.*.booking_price' => 'numeric',
        'video_link' => 'nullable|url',
        'expiration_date' => 'required|date|after:today',
    ];

    public function mount($packageId)
    {
        $this->packageId = $packageId;
        $package = Package::with('rooms.prices', 'maintains', 'amenities', 'entireProperty')->findOrFail($packageId);

        $this->country_id = $package->country_id;
        $this->city_id = $package->city_id;
        $this->area_id = $package->area_id;
        $this->property_id = $package->property_id;
        $this->name = $package->name;
        $this->address = $package->address;
        $this->map_link = $package->map_link;
        $this->number_of_kitchens = $package->number_of_kitchens;
        $this->number_of_rooms = $package->number_of_rooms;
        $this->common_bathrooms = $package->common_bathrooms;
        $this->seating = $package->seating;
        $this->details = $package->details;
        $this->video_link = $package->video_link;
        $this->expiration_date = $package->expiration_date;

        // Load and set relationships
        $this->loadRelatedData($package);
        $package = Package::with(['rooms.prices', 'maintains', 'amenities', 'entireProperty', 'photos'])
            ->findOrFail($packageId);
        $this->storedPhotos = $package->photos()->get()->map(function ($photo) {
            return [
                'id' => $photo->id,
                'url' => $photo->url
            ];
        })->toArray();
    }

    protected function loadRelatedData($package)
    {
        $this->countries = Country::all();
        $this->properties = Property::all();
        $this->maintains = Maintain::all();
        $this->amenities = Amenity::all();

        $this->rooms = $package->rooms->map(function ($room) {
            return [
                'id' => $room->id,
                'name' => $room->name,
                'number_of_beds' => $room->number_of_beds,
                'number_of_bathrooms' => $room->number_of_bathrooms,
                'prices' => $room->prices->map(function ($price) {
                    return [
                        'id' => $price->id,
                        'type' => $price->type,
                        'fixed_price' => $price->fixed_price,
                        'discount_price' => $price->discount_price,
                        'booking_price' => $price->booking_price,
                    ];
                })->toArray(),
            ];
        })->toArray();

        $this->freeMaintains = $package->maintains->where('pivot.is_paid', false)->pluck('id')->toArray();
        $this->freeAmenities = $package->amenities->where('pivot.is_paid', false)->pluck('id')->toArray();

        $this->paidMaintains = $package->maintains->where('pivot.is_paid', true)->map(function ($maintain) {
            return [
                'maintain_id' => $maintain->id,
                'price' => $maintain->pivot->price,
            ];
        })->toArray();

        $this->paidAmenities = $package->amenities->where('pivot.is_paid', true)->map(function ($amenity) {
            return [
                'amenity_id' => $amenity->id,
                'price' => $amenity->pivot->price,
            ];
        })->toArray();

        $entireProperty = $package->entireProperty;
        if ($entireProperty) {
            $this->selection = 'entire';
            $this->entireProperty['prices'] = $entireProperty->prices->toArray();
        } else {
            $this->selection = 'room';
        }
    }

    public function updatedCountryId($value)
    {
        $this->cities = City::where('country_id', $value)->get();
    }

    public function updatedCityId($value)
    {
        $this->areas = Area::where('city_id', $value)->get();
    }

    public function addRoom()
    {
        $this->rooms[] = [
            'id' => null,
            'name' => '',
            'number_of_beds' => 0,
            'number_of_bathrooms' => 0,
            'prices' => [
                ['type' => '', 'fixed_price' => 0, 'discount_price' => null, 'booking_price' => 0]
            ]
        ];
    }

    public function removeRoom($index)
    {
        $roomId = $this->rooms[$index]['id'];
        if ($roomId) {
            Room::find($roomId)->delete();
        }
        unset($this->rooms[$index]);
        $this->rooms = array_values($this->rooms);
    }

    public function addPriceOption($roomIndex)
    {
        $this->rooms[$roomIndex]['prices'][] = [
            'type' => '',
            'fixed_price' => 0,
            'discount_price' => null,
            'booking_price' => 0,
        ];
    }


    public function removePriceOption($roomIndex, $priceIndex)
    {
        $priceData = $this->rooms[$roomIndex]['prices'][$priceIndex];

        // If price has an ID, delete it from database
        if (isset($priceData['id'])) {
            RoomPrice::find($priceData['id'])->delete();
        }

        unset($this->rooms[$roomIndex]['prices'][$priceIndex]);
        $this->rooms[$roomIndex]['prices'] = array_values($this->rooms[$roomIndex]['prices']);
    }

    public function addEntirePropertyPrice()
    {
        if (count($this->entireProperty['prices']) < 3) {
            $this->entireProperty['prices'][] = [
                'type' => '',
                'fixed_price' => 0,
                'discount_price' => null,
                'booking_price' => 0,
            ];
        }
    }

    public function removeEntirePropertyPrice($index)
    {
        unset($this->entireProperty['prices'][$index]);
        $this->entireProperty['prices'] = array_values($this->entireProperty['prices']);
    }

    public function addPaidMaintain()
    {
        $this->paidMaintains[] = [
            'maintain_id' => '',
            'price' => 0,
        ];
    }

    public function removePaidMaintain($index)
    {
        unset($this->paidMaintains[$index]);
        $this->paidMaintains = array_values($this->paidMaintains);
    }

    public function addPaidAmenity()
    {
        $this->paidAmenities[] = [
            'amenity_id' => '',
            'price' => 0,
        ];
    }

    public function removePaidAmenity($index)
    {
        unset($this->paidAmenities[$index]);
        $this->paidAmenities = array_values($this->paidAmenities);
    }

    public function update()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $package = Package::findOrFail($this->packageId);

            // Update basic package info
            $package->update([
                'country_id' => $this->country_id,
                'city_id' => $this->city_id,
                'area_id' => $this->area_id,
                'property_id' => $this->property_id,
                'name' => $this->name,
                'address' => $this->address,
                'map_link' => $this->map_link,
                'number_of_kitchens' => $this->number_of_kitchens,
                'number_of_rooms' => $this->number_of_rooms,
                'common_bathrooms' => $this->common_bathrooms,
                'seating' => $this->seating,
                'details' => $this->details,
                'video_link' => $this->video_link,
                'expiration_date' => $this->expiration_date,
                'status' => strtotime($this->expiration_date) <= strtotime(now()) ? 'expired' : 'active',
            ]);

            $this->updateOrCreateRelatedData($package);

            DB::commit();

            session()->flash('message', 'Package updated successfully.');
            return redirect()->route('admin.packages');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error updating package: ' . $e->getMessage());
        }
    }

    protected function updateOrCreateRelatedData($package)
    {
        // Handle rooms and prices
        foreach ($this->rooms as $roomData) {
            // Update or create room
            $room = Room::updateOrCreate(
                ['id' => isset($roomData['id']) ? $roomData['id'] : null],
                [
                    'package_id' => $package->id,
                    'name' => $roomData['name'],
                    'number_of_beds' => $roomData['number_of_beds'],
                    'number_of_bathrooms' => $roomData['number_of_bathrooms'],
                    'user_id' => auth()->id(),
                ]
            );

            // Get existing price IDs for this room
            $existingPriceIds = $room->prices()->pluck('id')->toArray();
            $updatedPriceIds = [];

            // Update or create prices
            foreach ($roomData['prices'] as $priceData) {
                $price = RoomPrice::updateOrCreate(
                    [
                        'id' => isset($priceData['id']) ? $priceData['id'] : null,
                        'room_id' => $room->id,
                    ],
                    [
                        'type' => $priceData['type'],
                        'fixed_price' => $priceData['fixed_price'],
                        'discount_price' => $priceData['discount_price'],
                        'booking_price' => $priceData['booking_price'],
                        'user_id' => auth()->id(),
                    ]
                );

                $updatedPriceIds[] = $price->id;
            }

            // Delete prices that were not updated
            $room->prices()->whereNotIn('id', $updatedPriceIds)->delete();
        }

        // Delete rooms that are no longer present
        $currentRoomIds = collect($this->rooms)->pluck('id')->filter()->toArray();
        $package->rooms()->whereNotIn('id', $currentRoomIds)->delete();

        // Update entire property if applicable
        if ($this->selection === 'entire') {
            $entireProperty = EntireProperty::updateOrCreate(
                ['package_id' => $package->id],
                ['user_id' => auth()->id()]
            );

            $existingPriceIds = $entireProperty->prices()->pluck('id')->toArray();
            $updatedPriceIds = [];

            foreach ($this->entireProperty['prices'] as $priceData) {
                $price = $entireProperty->prices()->updateOrCreate(
                    [
                        'id' => isset($priceData['id']) ? $priceData['id'] : null,
                        'type' => $priceData['type'],
                    ],
                    [
                        'fixed_price' => $priceData['fixed_price'],
                        'discount_price' => $priceData['discount_price'],
                        'booking_price' => $priceData['booking_price'],
                        'user_id' => auth()->id(),
                    ]
                );

                $updatedPriceIds[] = $price->id;
            }

            // Delete prices that were not updated
            $entireProperty->prices()->whereNotIn('id', $updatedPriceIds)->delete();
        } else {
            // Delete entire property if it exists
            $package->entireProperty()->delete();
        }

        // Handle photos
        if ($this->photos) {
            foreach ($this->photos as $photo) {
                $path = $photo->store('photos', 'public');
                $package->photos()->create([
                    'url' => $path,
                    'user_id' => auth()->id(),
                ]);
            }
        }
    }

    public function removeStoredPhoto($photoId)
    {
        $photo = Package::find($this->packageId)->photos()->findOrFail($photoId);
        Storage::disk('public')->delete($photo->url);
        $photo->delete();

        $this->storedPhotos = array_values(array_filter($this->storedPhotos, function ($p) use ($photoId) {
            return $p['id'] !== $photoId;
        }));
    }



    // private function updatePaidMaintains()
    // {
    //     $maintainsToSync = collect($this->paidMaintains)
    //         ->mapWithKeys(fn($item) => [$item['maintain_id'] => ['is_paid' => true, 'price' => $item['price'], 'user_id' => auth()->id()]]);

    //     $validMaintainIds = Maintain::whereIn('id', $maintainsToSync->keys())->pluck('id')->toArray();
    //     $maintainsToSync = $maintainsToSync->only($validMaintainIds);

    //     // Sync paid maintains, which will replace the current pivot data
    //     $this->package->maintains()->sync($maintainsToSync);
    // }

    // private function updatePaidAmenities()
    // {
    //     $amenitiesToSync = collect($this->paidAmenities)
    //         ->mapWithKeys(fn($item) => [$item['amenity_id'] => ['is_paid' => true, 'price' => $item['price'], 'user_id' => auth()->id()]]);

    //     $validAmenityIds = Amenity::whereIn('id', $amenitiesToSync->keys())->pluck('id')->toArray();
    //     $amenitiesToSync = $amenitiesToSync->only($validAmenityIds);

    //     // Sync paid amenities, which will replace the current pivot data
    //     $this->package->amenities()->sync($amenitiesToSync);
    // }


    //     public function updateFreeMaintains()
    // {
    //     $freeMaintainsToSync = collect($this->freeMaintains)
    //         ->mapWithKeys(fn($id) => [$id => ['is_paid' => false, 'user_id' => Auth::id()]]);

    //     $validMaintainIds = Maintain::whereIn('id', $freeMaintainsToSync->keys())->pluck('id')->toArray();
    //     $freeMaintainsToSync = $freeMaintainsToSync->only($validMaintainIds);

    //     // Sync free maintains without detaching
    //     $this->package->maintains()->syncWithoutDetaching($freeMaintainsToSync);
    // }

    // public function updateFreeAmenities()
    // {
    //     $freeAmenitiesToSync = collect($this->freeAmenities)
    //         ->mapWithKeys(fn($id) => [$id => ['is_paid' => false, 'user_id' => Auth::id()]]);

    //     $validAmenityIds = Amenity::whereIn('id', $freeAmenitiesToSync->keys())->pluck('id')->toArray();
    //     $freeAmenitiesToSync = $freeAmenitiesToSync->only($validAmenityIds);

    //     // Sync free amenities without detaching
    //     $this->package->amenities()->syncWithoutDetaching($freeAmenitiesToSync);
    // }

    protected function getFreeMaintains()
    {
        return array_map(function ($id) {
            return ['maintain_id' => $id, 'is_paid' => false, 'price' => 0];
        }, $this->freeMaintains);
    }

    protected function getPaidMaintains()
    {
        return array_map(function ($maintain) {
            return ['maintain_id' => $maintain['maintain_id'], 'is_paid' => true, 'price' => $maintain['price']];
        }, $this->paidMaintains);
    }

    protected function getFreeAmenities()
    {
        return array_map(function ($id) {
            return ['amenity_id' => $id, 'is_paid' => false, 'price' => 0];
        }, $this->freeAmenities);
    }

    protected function getPaidAmenities()
    {
        return array_map(function ($amenity) {
            return ['amenity_id' => $amenity['amenity_id'], 'is_paid' => true, 'price' => $amenity['price']];
        }, $this->paidAmenities);
    }

    public function render()
    {
        return view('livewire.admin.package.edit-package-component', [
            'countries' => $this->countries,
            'cities' => $this->cities,
            'areas' => $this->areas,
            'properties' => $this->properties,
            'maintains' => $this->maintains,
            'amenities' => $this->amenities,
        ]);
    }
}
