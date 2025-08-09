@props(['variant' => 'default', 'position' => 'top-right'])

@php
$positions = [
    'top-left' => 'top-0 left-0',
    'top-right' => 'top-0 right-0',
    'bottom-left' => 'bottom-0 left-0',
    'bottom-right' => 'bottom-0 right-0',
    'center' => 'top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2',
];

$variants = [
    'default' => 'text-red-500/10',
    'primary' => 'text-red-600/20',
    'secondary' => 'text-gray-600/10',
    'glow' => 'text-red-400/15',
];
@endphp

<div class="absolute {{ $positions[$position] ?? $positions['top-right'] }} pointer-events-none select-none">
    @if($variant == 'geometric1')
        <svg width="400" height="400" viewBox="0 0 400 400" class="{{ $variants['glow'] }} animate-float">
            <defs>
                <linearGradient id="gradient1" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#ff2d20;stop-opacity:0.1" />
                    <stop offset="100%" style="stop-color:#ef4444;stop-opacity:0.05" />
                </linearGradient>
            </defs>
            <path d="M50,200 Q200,50 350,200 Q200,350 50,200 Z" fill="url(#gradient1)" stroke="currentColor" stroke-width="0.5"/>
            <circle cx="200" cy="100" r="3" fill="currentColor" opacity="0.3">
                <animate attributeName="opacity" values="0.3;0.7;0.3" dur="3s" repeatCount="indefinite"/>
            </circle>
            <circle cx="300" cy="200" r="2" fill="currentColor" opacity="0.4">
                <animate attributeName="opacity" values="0.4;0.8;0.4" dur="2s" repeatCount="indefinite"/>
            </circle>
            <circle cx="100" cy="300" r="4" fill="currentColor" opacity="0.2">
                <animate attributeName="opacity" values="0.2;0.6;0.2" dur="4s" repeatCount="indefinite"/>
            </circle>
        </svg>
    @elseif($variant == 'geometric2')
        <svg width="300" height="300" viewBox="0 0 300 300" class="{{ $variants['primary'] }}">
            <defs>
                <radialGradient id="gradient2" cx="50%" cy="50%" r="50%">
                    <stop offset="0%" style="stop-color:#ff2d20;stop-opacity:0.2" />
                    <stop offset="100%" style="stop-color:#ff2d20;stop-opacity:0" />
                </radialGradient>
            </defs>
            <polygon points="150,20 280,130 220,280 80,280 20,130" fill="url(#gradient2)" stroke="currentColor" stroke-width="0.5"/>
            <line x1="150" y1="20" x2="150" y2="280" stroke="currentColor" stroke-width="0.3" opacity="0.3"/>
            <line x1="20" y1="130" x2="280" y2="130" stroke="currentColor" stroke-width="0.3" opacity="0.3"/>
        </svg>
    @elseif($variant == 'organic')
        <svg width="500" height="500" viewBox="0 0 500 500" class="{{ $variants['default'] }} animate-pulse">
            <defs>
                <filter id="blur1">
                    <feGaussianBlur in="SourceGraphic" stdDeviation="2"/>
                </filter>
            </defs>
            <path d="M100,250 C100,150 150,100 250,100 C350,100 400,150 400,250 C400,350 350,400 250,400 C150,400 100,350 100,250 Z" 
                  fill="currentColor" opacity="0.1" filter="url(#blur1)"/>
            <path d="M150,250 C150,200 200,150 250,150 C300,150 350,200 350,250 C350,300 300,350 250,350 C200,350 150,300 150,250 Z" 
                  fill="currentColor" opacity="0.05"/>
        </svg>
    @elseif($variant == 'dots')
        <svg width="200" height="200" viewBox="0 0 200 200" class="{{ $variants['secondary'] }}">
            @for($i = 0; $i < 10; $i++)
                @for($j = 0; $j < 10; $j++)
                    <circle cx="{{ 20 + $i * 18 }}" cy="{{ 20 + $j * 18 }}" r="1" fill="currentColor" opacity="{{ rand(10, 30) / 100 }}">
                        <animate attributeName="opacity" 
                                 values="{{ rand(10, 30) / 100 }};{{ rand(40, 70) / 100 }};{{ rand(10, 30) / 100 }}" 
                                 dur="{{ rand(2, 6) }}s" 
                                 repeatCount="indefinite"/>
                    </circle>
                @endfor
            @endfor
        </svg>
    @else
        <!-- Default Abstract Pattern -->
        <svg width="300" height="300" viewBox="0 0 300 300" class="{{ $variants[$variant] ?? $variants['default'] }}">
            <defs>
                <pattern id="pattern1" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                    <circle cx="20" cy="20" r="2" fill="currentColor" opacity="0.3"/>
                </pattern>
            </defs>
            <rect width="300" height="300" fill="url(#pattern1)"/>
            <path d="M50,150 Q150,50 250,150 T450,150" stroke="currentColor" stroke-width="0.5" fill="none" opacity="0.4"/>
            <path d="M150,50 Q50,150 150,250 T150,450" stroke="currentColor" stroke-width="0.5" fill="none" opacity="0.4"/>
        </svg>
    @endif
</div>
