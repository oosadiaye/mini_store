<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageMatchingService;
use Illuminate\Http\Request;

class BulkImageUploadController extends Controller
{
    protected $imageService;
    
    public function __construct(ImageMatchingService $imageService)
    {
        $this->imageService = $imageService;
    }
    
    public function index()
    {
        return view('admin.products.bulk-upload');
    }
    
    public function upload(Request $request)
    {
        $request->validate([
            'images' => 'required|array|max:50',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);
        
        $results = $this->imageService->processBulkUpload($request->file('images'));
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'summary' => [
                'matched' => count($results['matched']),
                'unmatched' => count($results['unmatched']),
                'errors' => count($results['errors']),
            ],
        ]);
    }
}
