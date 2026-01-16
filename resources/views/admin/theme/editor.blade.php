@extends('admin.layout', ['title' => 'Visual Theme Editor'])

@section('content')
<div class="h-[calc(100vh-80px)]">
    <visual-editor 
        :initial-layout='@json($layout)'
        save-url="{{ route('admin.theme.update', ['tenant' => $tenant->slug]) }}"
    ></visual-editor>
</div>
@endsection
