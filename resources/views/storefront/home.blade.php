@extends('storefront.layout')

@section('content')
    {{-- Render sections dynamically from page builder --}}
    @foreach($sections as $section)
        @if($section['enabled'] ?? true)
            @php
                // Convert section array to object for template compatibility
                $sectionObj = (object) $section;
                $sectionObj->settings = $section['settings'] ?? [];
                $sectionObj->title = $section['title'] ?? '';
                $sectionObj->content = $section['content'] ?? '';
            @endphp
            
            {{-- Render the appropriate section template --}}
            @includeIf('storefront.sections.' . $section['type'], ['section' => $sectionObj])
        @endif
    @endforeach
@endsection
