@php
    $settings = $section->settings ?? [];
    $limit = $settings['limit'] ?? 6;
    
    // Sample team members - in real implementation, these would come from database
    $members = $settings['members'] ?? [
        [
            'name' => 'John Smith',
            'role' => 'CEO & Founder',
            'image' => 'https://i.pravatar.cc/400?img=12',
            'bio' => 'Visionary leader with 15+ years of experience in e-commerce.',
            'social' => [
                'linkedin' => '#',
                'twitter' => '#'
            ]
        ],
        [
            'name' => 'Jane Doe',
            'role' => 'Chief Technology Officer',
            'image' => 'https://i.pravatar.cc/400?img=45',
            'bio' => 'Tech innovator passionate about creating seamless user experiences.',
            'social' => [
                'linkedin' => '#',
                'github' => '#'
            ]
        ],
        [
            'name' => 'Mike Johnson',
            'role' => 'Head of Marketing',
            'image' => 'https://i.pravatar.cc/400?img=33',
            'bio' => 'Creative strategist driving brand growth and customer engagement.',
            'social' => [
                'linkedin' => '#',
                'twitter' => '#'
            ]
        ],
        [
            'name' => 'Sarah Williams',
            'role' => 'Customer Success Manager',
            'image' => 'https://i.pravatar.cc/400?img=47',
            'bio' => 'Dedicated to ensuring every customer has an amazing experience.',
            'social' => [
                'linkedin' => '#'
            ]
        ],
        [
            'name' => 'Tom Brown',
            'role' => 'Lead Designer',
            'image' => 'https://i.pravatar.cc/400?img=15',
            'bio' => 'Award-winning designer creating beautiful and functional interfaces.',
            'social' => [
                'dribbble' => '#',
                'behance' => '#'
            ]
        ],
        [
            'name' => 'Emma Davis',
            'role' => 'Operations Director',
            'image' => 'https://i.pravatar.cc/400?img=48',
            'bio' => 'Efficiency expert optimizing processes for maximum productivity.',
            'social' => [
                'linkedin' => '#'
            ]
        ],
    ];
    
    $members = array_slice($members, 0, $limit);
@endphp

<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $section->title ?? 'Meet Our Team' }}</h2>
            <p class="text-gray-600">{{ $section->content ?? 'The talented people behind our success' }}</p>
        </div>
        
        <!-- Team Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
            @foreach($members as $member)
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition group">
                <!-- Member Image -->
                <div class="aspect-square overflow-hidden bg-gray-200">
                    <img src="{{ $member['image'] }}" 
                         alt="{{ $member['name'] }}"
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                </div>
                
                <!-- Member Info -->
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $member['name'] }}</h3>
                    <p class="text-indigo-600 font-semibold mb-3">{{ $member['role'] }}</p>
                    <p class="text-gray-600 text-sm mb-4">{{ $member['bio'] }}</p>
                    
                    <!-- Social Links -->
                    <div class="flex gap-3">
                        @if(isset($member['social']['linkedin']))
                        <a href="{{ $member['social']['linkedin'] }}" 
                           class="text-gray-400 hover:text-blue-600 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                            </svg>
                        </a>
                        @endif
                        @if(isset($member['social']['twitter']))
                        <a href="{{ $member['social']['twitter'] }}" 
                           class="text-gray-400 hover:text-blue-400 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
