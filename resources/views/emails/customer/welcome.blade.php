<x-mail::message>
# Welcome to {{ $storeName }}!

Hi {{ $customerName }},

We're thrilled to have you join our community! Your account has been successfully created.

Explore our collection and discover the best products we have for you.

<x-mail::button :url="$storefrontUrl">
Shop Now
</x-mail::button>

If you have any questions, feel free to contact our support team.

Best regards,
The {{ $storeName }} Team
</x-mail::message>
