<?php

namespace App\Livewire\User;

use App\Models\TermsCondition;
use Livewire\Component;

class TermsConditionComponent extends Component
{
    public $tcTitle;
    public $tcContent;

    public function mount()
    {
        // Load data from model or repository if needed
        $tc = TermsCondition::first(); // Example assuming one privacy policy entry
        if ($tc) {
            $this->tcTitle = $tc->title;
            $this->tcContent = $tc->content;
        }
    }
    public function render()
    {
        return view('livewire.user.terms-condition-component')->layout('layouts.guest');
    }
}
