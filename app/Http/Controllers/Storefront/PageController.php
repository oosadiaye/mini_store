<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\StoreConfig;

use App\Models\StoreCollection;

class PageController extends Controller
{
    private function getPolicyContent($key)
    {
        if (Storage::disk('tenant')->exists('generated_theme_schema.json')) {
            $schema = json_decode(Storage::disk('tenant')->get('generated_theme_schema.json'), true);
            return $schema['policies'][$key] ?? '';
        }
        return '';
    }

    private function getCommonData()
    {
        return [
            'config' => StoreConfig::firstOrNew(['id' => 1]),
            'menuCategories' => StoreCollection::take(5)->get(),
        ];
    }

    public function faq()
    {
        $content = $this->getPolicyContent('faq');
        return view('storefront.pages.policy', array_merge($this->getCommonData(), [
            'page_title' => 'Frequently Asked Questions',
            'content' => $content
        ]));
    }

    public function shipping()
    {
        $content = $this->getPolicyContent('shipping');
        return view('storefront.pages.policy', array_merge($this->getCommonData(), [
            'page_title' => 'Shipping Policy',
            'content' => $content
        ]));
    }

    public function returns()
    {
        $content = $this->getPolicyContent('returns');
        return view('storefront.pages.policy', array_merge($this->getCommonData(), [
            'page_title' => 'Returns & Refunds',
            'content' => $content
        ]));
    }
}
