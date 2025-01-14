<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

class Projects extends Component
{
    #[Title('Projects')]
    public function render()
    {
        return view('livewire.projects');
    }
}
