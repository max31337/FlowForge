<?php

namespace App\Livewire\Test;

use Livewire\Component;

class SimpleTest extends Component
{
    public $message = 'Livewire is working!';
    public $counter = 0;

    public function increment()
    {
        $this->counter++;
    }

    public function render()
    {
        return view('livewire.test.simple-test');
    }
}
