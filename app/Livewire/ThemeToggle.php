<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ThemeToggle extends Component
{
    public $darkMode;

    public function mount()
    {
        // Set the initial value of darkMode from localStorage or default to light mode
        $this->darkMode = session()->get('theme', 'light') === 'dark';
    }

    public function updatedDarkMode($value)
    {
        // Store the theme in the session or database (or even localStorage via JS if needed)
        session()->put('theme', $value ? 'dark' : 'light');
    }

    public function render()
    {
        return view('livewire.theme-toggle');
    }
}
