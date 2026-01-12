@extends('admin.layout')

@section('content')
    <product-form
        :categories='@json($categories)'
        :brands='@json($brands)'
        :old-input='@json(session()->getOldInput())'
        :errors='@json($errors->getMessages())'
        action-url="{{ route('admin.products.store') }}"
        csrf-token="{{ csrf_token() }}"
        :routes='@json([
            "index" => route("admin.products.index"),
            "storeCategory" => route("admin.categories.store"),
            "storeBrand" => route("admin.brands.store")
        ])'
    ></product-form>
@endsection
