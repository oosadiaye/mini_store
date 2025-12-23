<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TenantPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'is_published' => true,
                'content' => '
                    <div class="space-y-16 py-8">
                        <!-- Hero Section -->
                        <div class="text-center max-w-3xl mx-auto px-4">
                            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 font-serif">Crafting Quality Since 2023</h1>
                            <p class="text-lg text-gray-600 leading-relaxed">
                                We believe in more than just selling products; we believe in curating experiences. 
                                Our journey began with a simple idea: to bring high-quality, sustainably sourced items to those who appreciate craftsmanship.
                            </p>
                        </div>

                        <!-- Image Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 container mx-auto px-4">
                            <div class="h-96 bg-gray-200 rounded-xl overflow-hidden relative group">
                                <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&q=80" alt="Our Workshop" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                            </div>
                            <div class="flex flex-col justify-center space-y-6 p-8 bg-gray-50 rounded-xl">
                                <h3 class="text-2xl font-bold text-gray-800">Our Mission</h3>
                                <p class="text-gray-600">To inspire and innovate, providing our customers with products that not only serve a purpose but also tell a story.</p>
                                <ul class="space-y-3">
                                    <li class="flex items-center text-gray-700"><span class="w-2 h-2 bg-primary rounded-full mr-3"></span>Sustainable Practices</li>
                                    <li class="flex items-center text-gray-700"><span class="w-2 h-2 bg-primary rounded-full mr-3"></span>Community Focused</li>
                                    <li class="flex items-center text-gray-700"><span class="w-2 h-2 bg-primary rounded-full mr-3"></span>Quality Guaranteed</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Team Section -->
                         <div class="container mx-auto px-4 text-center">
                            <h2 class="text-3xl font-bold mb-12">Meet the Team</h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div class="space-y-4">
                                    <div class="w-32 h-32 mx-auto rounded-full overflow-hidden bg-gray-200">
                                         <img src="https://ui-avatars.com/api/?name=John+Doe&background=random" alt="Founder" class="w-full h-full object-cover">
                                    </div>
                                    <h4 class="text-xl font-semibold">John Doe</h4>
                                    <p class="text-primary text-sm font-medium uppercase tracking-wide">Founder & CEO</p>
                                </div>
                                 <div class="space-y-4">
                                     <div class="w-32 h-32 mx-auto rounded-full overflow-hidden bg-gray-200">
                                         <img src="https://ui-avatars.com/api/?name=Jane+Smith&background=random" alt="Designer" class="w-full h-full object-cover">
                                    </div>
                                    <h4 class="text-xl font-semibold">Jane Smith</h4>
                                    <p class="text-primary text-sm font-medium uppercase tracking-wide">Head of Design</p>
                                </div>
                                 <div class="space-y-4">
                                     <div class="w-32 h-32 mx-auto rounded-full overflow-hidden bg-gray-200">
                                         <img src="https://ui-avatars.com/api/?name=Mike+Ross&background=random" alt="Operations" class="w-full h-full object-cover">
                                    </div>
                                    <h4 class="text-xl font-semibold">Mike Ross</h4>
                                    <p class="text-primary text-sm font-medium uppercase tracking-wide">Operations Lead</p>
                                </div>
                            </div>
                        </div>
                    </div>
                '
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact',
                'is_published' => true,
                'content' => '
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 py-8 container mx-auto px-4">
                        <div class="space-y-8">
                            <div>
                                <h1 class="text-4xl font-bold text-gray-900 mb-4 font-serif">Get in Touch</h1>
                                <p class="text-gray-600 text-lg">Have questions? We\'d love to hear from you. Send us a message and we\'ll respond as soon as possible.</p>
                            </div>
                            
                            <div class="space-y-6">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-medium text-gray-900">Office</h3>
                                        <p class="mt-1 text-gray-600">123 Commerce St, Market City, ST 12345</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-medium text-gray-900">Email</h3>
                                        <p class="mt-1 text-gray-600">support@example.com</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-8 rounded-xl shadow-sm border border-gray-100">
                            <!-- This could be replaced by a livewire component or form -->
                            <form action="/contact" method="POST" class="space-y-6">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary h-10 px-3" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary h-10 px-3" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Message</label>
                                    <textarea name="message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary p-3" required></textarea>
                                </div>
                                <button type="submit" class="w-full bg-primary text-white px-4 py-3 rounded-md font-semibold hover:bg-opacity-90 transition shadow-lg shadow-primary/30">Send Message</button>
                            </form>
                        </div>
                    </div>
                '
            ],
            [
                'title' => 'Shipping Policy',
                'slug' => 'shipping-policy',
                'is_published' => true,
                'content' => '
                    <div class="max-w-4xl mx-auto py-8 px-4 prose prose-indigo prose-lg">
                        <h1 class="text-4xl font-bold font-serif mb-8 text-center">Shipping Policy</h1>
                        
                        <div class="bg-indigo-50 border-l-4 border-primary p-4 mb-8">
                            <p class="m-0 text-indigo-700"><strong>Note:</strong> We offer free shipping on all orders over $100.</p>
                        </div>

                        <h3>Processing Time</h3>
                        <p>All orders are processed within 1-2 business days. Orders are not shipped or delivered on weekends or holidays.</p>

                        <h3>Shipping Rates & Delivery Estimates</h3>
                        <p>Shipping charges for your order will be calculated and displayed at checkout.</p>
                        <ul class="list-disc pl-5 space-y-2">
                            <li><strong>Standard Shipping:</strong> 3-5 business days</li>
                            <li><strong>Express Shipping:</strong> 1-2 business days</li>
                            <li><strong>International Shipping:</strong> 7-14 business days</li>
                        </ul>
                    </div>
                '
            ],
            [
                'title' => 'Returns & Refunds',
                'slug' => 'refund-policy',
                'is_published' => true,
                'content' => '
                    <div class="max-w-4xl mx-auto py-8 px-4 prose prose-indigo prose-lg">
                         <h1 class="text-4xl font-bold font-serif mb-8 text-center">Return Policy</h1>
                         <p>We want you to be completely satisfied with your purchase. If you are not satisfied, you may return your item within 30 days of the delivery date.</p>
                         
                         <div class="grid grid-cols-1 md:grid-cols-3 gap-6 my-8 not-prose">
                            <div class="text-center p-6 border rounded-xl hover:shadow-md transition">
                                <div class="text-3xl mb-2">📅</div>
                                <h3 class="font-bold">30 Days</h3>
                                <p class="text-sm text-gray-500">Return window</p>
                            </div>
                             <div class="text-center p-6 border rounded-xl hover:shadow-md transition">
                                <div class="text-3xl mb-2">🏷️</div>
                                <h3 class="font-bold">Original Tag</h3>
                                <p class="text-sm text-gray-500">Must be attached</p>
                            </div>
                             <div class="text-center p-6 border rounded-xl hover:shadow-md transition">
                                <div class="text-3xl mb-2">💸</div>
                                <h3 class="font-bold">Free Return</h3>
                                <p class="text-sm text-gray-500">Pre-paid label</p>
                            </div>
                         </div>

                         <h3>How to Return</h3>
                         <ol>
                            <li>Log in to your account and visit the "My Orders" section.</li>
                            <li>Select the item(s) you wish to return and click "Request Return".</li>
                            <li>Print the prepaid shipping label sent to your email.</li>
                            <li>Drop off the package at the nearest carrier location.</li>
                         </ol>
                    </div>
                '
            ],
            [
                'title' => 'Frequently Asked Questions',
                'slug' => 'faq',
                'is_published' => true,
                'content' => '
                    <div class="max-w-3xl mx-auto py-8 px-4">
                        <h1 class="text-4xl font-bold font-serif mb-12 text-center">FAQ</h1>
                        <div class="space-y-4">
                            <!-- Accordion Item 1 -->
                            <div class="border rounded-lg overflow-hidden">
                                <button class="w-full px-6 py-4 text-left font-semibold bg-gray-50 hover:bg-gray-100 focus:outline-none flex justify-between items-center">
                                    <span>What payment methods do you accept?</span>
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="px-6 py-4 bg-white border-t">
                                    <p class="text-gray-600">We accept major credit cards (Visa, MasterCard, American Express) and PayPal.</p>
                                </div>
                            </div>
                             <!-- Accordion Item 2 -->
                            <div class="border rounded-lg overflow-hidden">
                                <button class="w-full px-6 py-4 text-left font-semibold bg-gray-50 hover:bg-gray-100 focus:outline-none flex justify-between items-center">
                                    <span>Do you ship internationally?</span>
                                     <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="px-6 py-4 bg-white border-t">
                                    <p class="text-gray-600">Yes, we ship to most countries worldwide. Shipping costs will apply, and will be added at checkout.</p>
                                </div>
                            </div>
                            <!-- Accordion Item 3 -->
                            <div class="border rounded-lg overflow-hidden">
                                <button class="w-full px-6 py-4 text-left font-semibold bg-gray-50 hover:bg-gray-100 focus:outline-none flex justify-between items-center">
                                    <span>How can I track my order?</span>
                                     <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="px-6 py-4 bg-white border-t">
                                    <p class="text-gray-600">Once your order has shipped, you will receive an email containing a tracking number.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                '
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'is_published' => true,
                'content' => '
                    <div class="max-w-4xl mx-auto py-8 px-4 prose prose-indigo prose-lg">
                        <h1 class="text-4xl font-bold font-serif mb-8 text-center">Privacy Policy</h1>
                        <p>Your privacy is important to us. It is our policy to respect your privacy regarding any information we may collect from you across our website.</p>
                        <h3>Information We Collect</h3>
                        <p>We only ask for personal information when we truly need it to provide a service to you. We collect it by fair and lawful means, with your knowledge and consent.</p>
                        <h3>How We Use Info</h3>
                        <p>We use the data to process orders, improve our products, and communicate with you about promotions.</p>
                    </div>
                '
            ]
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
