<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Logo extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $size = '6'
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return <<<'blade'
<div>
    <img src="https://coolify.io/coolify-transparent.svg" alt="coolLabs" class="size-{{ $size }}">
</div>
blade;
    }
}
