<x-mail::message>
# Order Confirmation

Hi {{ $order->customer->name }},

Thank you for your order! We've received your order **{{ $order->order_number }}** and are currently processing it.

**Order Summary:**
@foreach($order->items as $item)
- {{ $item->quantity }}x {{ $item->product_name }} (@money($item->price)) = @money($item->total)
@endforeach

**Total:** @money($order->total)

<x-mail::button :url="$storefrontUrl . '/orders/' . $order->order_number">
View Order Details
</x-mail::button>

We will notify you once your order has been shipped.

Best regards,
The {{ $storeName }} Team
</x-mail::message>
