<x-mail::message>
# Products Expiring Soon

The following products in your inventory are scheduled to expire within the next 6 months (180 days). Please take necessary actions for clearance or restocking.

<x-mail::table>
| Product | SKU | Expiry Date | Days Left |
| :--- | :--- | :--- | :--- |
@foreach($products as $product)
| {{ $product->name }} | {{ $product->sku }} | {{ $product->expiry_date->format('M d, Y') }} | {{ now()->diffInDays($product->expiry_date) }} |
@endforeach
</x-mail::table>

<x-mail::button :url="route('admin.products.index')">
Manage Products
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
