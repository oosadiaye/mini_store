<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;

class RobotsController extends Controller
{
    public function index()
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin\n";
        $content .= "Disallow: /cart\n";
        $content .= "Disallow: /checkout\n";
        $content .= "Disallow: /my-account\n";
        $content .= "Sitemap: " . route('storefront.sitemap') . "\n";

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
