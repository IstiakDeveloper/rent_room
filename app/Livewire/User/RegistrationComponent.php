<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class RegistrationComponent extends Component
{
    public $name;
    public $email;
    public $phone;
    public $password;
    public $role;
    public $agreeUserTerms;
    public $agreePartnerTerms;

    public function register()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:User,Partner',
            'agreeUserTerms' => 'required_if:role,User',
            'agreePartnerTerms' => 'required_if:role,Partner',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => Hash::make($this->password),
        ]);
        $user->assignRole($this->role);

        Auth::login($user);

        session()->flash('message', 'Registration successful.');
        return redirect()->intended('/');
    }

    public function render()
    {
        return view('livewire.user.registration-component');
    }
}
