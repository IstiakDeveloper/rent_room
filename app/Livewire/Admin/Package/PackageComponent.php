<?php

namespace App\Livewire\Admin\Package;

use App\Models\Amenity;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\Maintain;
use App\Models\Package;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class PackageComponent extends Component
{
    use WithFileUploads;

    public $countries, $cities = [], $areas = [], $properties = [];
    public $maintains, $amenities, $packages;
    public $selectedPackageId;
    public $selectedUserId;
    public $showAssignModal = false;

    public function mount()
    {
        $user = Auth::user();

        if ($user->roles->pluck('name')->contains('Super Admin')) {
            // If the user is a Super Admin, they can see all data
            $this->countries = Country::all();
            $this->maintains = Maintain::all();
            $this->amenities = Amenity::all();
            $this->packages = Package::with(['country', 'city', 'area', 'property'])->get();
        } else {
            // If the user is not a Super Admin, show only the data they created
            $this->countries = Country::all();
            $this->maintains = Maintain::where('user_id', $user->id)->get();
            $this->amenities = Amenity::where('user_id', $user->id)->get();
            $this->packages = Package::with(['country', 'city', 'area', 'property'])
                                    ->where('user_id', $user->id)
                                    ->get();
        }


    }


    public function openAssignModal($packageId)
    {
        $this->selectedPackageId = $packageId;
        $this->showAssignModal = true;
    }


    public function assignUser()
    {
        $package = Package::find($this->selectedPackageId);
        $package->user_id = $this->selectedUserId;
        $package->save();

        $this->showAssignModal = false;
        session()->flash('message', 'User assigned successfully.');
    }

    public function delete($id)
    {
        Package::find($id)->delete();
        session()->flash('message', 'Package Deleted Successfully.');
    }

    public function render()
    {
        $packages = Package::with('users')->get();

        return view('livewire.admin.package.package-component', [
            'packages' => $packages,
            'users' => User::role('Partner')->get(),
        ]);
    }

}
