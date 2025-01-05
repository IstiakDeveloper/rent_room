<?php

namespace App\Livewire\User;

use Livewire\Component;

class TermsConditionComponent extends Component
{
    public function render()
    {
        return view('livewire.user.terms-condition-component')
            ->layout('layouts.guest');
    }
}
