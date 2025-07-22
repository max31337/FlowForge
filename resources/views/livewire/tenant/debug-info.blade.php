<div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mb-4">
    <h4 class="font-bold mb-2">🐛 Debug Information</h4>
    <div class="text-sm space-y-1">
        @foreach($debug as $key => $value)
            <div><strong>{{ $key }}:</strong> {{ $value }}</div>
        @endforeach
    </div>
</div>
