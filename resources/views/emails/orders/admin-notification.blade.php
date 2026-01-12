<x-mail::message>
# New Order Received

Hello Admin,

A new order has been placed on **{{ $storeName }}**.

**Order Details:**
- **Order Number:** {{ $order->order_number }}
- **Customer:** {{ $order->customer->name }} ({{ $order->customer->email }})
- **Total Amount:** @money($order->total)
- **Payment Method:** {{ $order->payment_method }}

**Order Items:**
@foreach($order->items as $item)
- {{ $item->quantity }}x {{ $item->product_name }} (@money($item->price)) = @money($item->total)
@endforeach

<x-mail::button :url="$adminUrl . '/orders/' . $order->id">
Manage Order in Admin Panel
</x-mail::button>

Best regards,
The System
</x-mail::message>
