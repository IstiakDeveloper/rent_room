<?php

namespace App\Livewire\User;

use App\Models\PartnerTermsCondition;
use Livewire\Component;

class PartnerTermsConditionComponent extends Component
{
    public $tcTitle;
    public $tcContent;

    public function mount()
    {
        // Load data from model or repository if needed
        $tc = PartnerTermsCondition::first(); // Example assuming one privacy policy entry
        if ($tc) {
            $this->tcTitle = $tc->title;
            $this->tcContent = $tc->content;
        }
    }
    public function render()
    {
        return view('livewire.user.partner-terms-condition-component')->layout('layouts.guest');
    }
}
