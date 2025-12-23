@props(['stats' => []])

@if(!empty($stats) && is_array($stats))
<div class="bg-black text-white py-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-gray-800">
            @foreach($stats as $stat)
                <div class="px-4">
                    <div class="text-4xl md:text-5xl font-serif font-bold mb-2">{{ $stat['value'] }}</div>
                    <div class="text-xs uppercase tracking-widest text-gray-400">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
