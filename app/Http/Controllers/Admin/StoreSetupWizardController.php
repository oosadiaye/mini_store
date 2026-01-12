<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreConfig;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\ThemeService;
use App\Services\SecureFileUploader;

class StoreSetupWizardController extends Controller
{
    /**
     * Display the wizard.
     */

    protected $themeService;
    protected $uploader;

    public function __construct(ThemeService $themeService, SecureFileUploader $uploader)
    {
        $this->themeService = $themeService;
        $this->uploader = $uploader;
    }

    /**
     * Display the wizard.
     */
    public function index()
    {
        $tenant = app('tenant');
        $storeConfig = StoreConfig::firstOrNew(['id' => 1]); // Singleton config per tenant
        
        // Auto-fill defaults if empty
        if (!$storeConfig->exists) {
            $storeConfig->store_name = $tenant->name;
        }

        $categories = Category::active()->get();
        // Prepare Catalog Curator Data (Wizard Step 3)
        $curatorCategories = $categories->map(function ($cat) {
            // Smart Default Logic: Check keywords in name or products (mocked logic for now on name)
            // If checking products: $cat->products()->where('type', 'raw_material')->exists()
            $isRawMaterial = preg_match('/(raw|material|asset|internal|component)/i', $cat->name);
            
            // If already set in DB, use it. If null (new migration), intelligent default.
            $isVisible = $cat->is_visible_online ?? !$isRawMaterial;
            
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug, // Added slug
                'public_display_name' => $cat->public_display_name ?? $cat->name,
                'is_visible_online' => (bool) $isVisible,
                'parent_id' => $cat->parent_id,
                'children' => [], // To be nested in frontend or recursively here
            ];
        });
        
        // Nesting Logic for Frontend Tree
        $tree = $this->buildCategoryTree($curatorCategories);

        return view('admin.wizard.index', compact('storeConfig', 'categories', 'tree'));
    }

    private function buildCategoryTree($categories, $parentId = null)
    {
        $branch = [];
        foreach ($categories as $cat) {
            if ($cat['parent_id'] == $parentId) {
                $children = $this->buildCategoryTree($categories, $cat['id']);
                if ($children) {
                    $cat['children'] = $children;
                }
                $branch[] = $cat;
            }
        }
        return $branch;
    }



    /**
     * Finish the wizard and generate theme settings.
     */
    public function finish(Request $request)
    {
        $config = StoreConfig::firstOrFail();
        $config->is_completed = true;
        $config->save();

        // Generate Theme Settings JSON
        $settings = $this->themeService->generateThemeSettings($config);

        // Inject All Categories from Wizard State if present
        if ($request->has('navigation_categories')) {
            $allCats = json_decode($request->navigation_categories, true);
            if (is_array($allCats)) {
                // Ensure navigation section exists
                if (!isset($settings['navigation'])) {
                    $settings['navigation'] = [];
                }
                $settings['navigation']['all_categories'] = $allCats;
                // Also update menu_items to match if desired, or keep separate. 
                // User asked specifically for navigation.all_categories
            }
        }
        
        // Save to Storage
        Storage::disk('tenant')->put('theme_settings.json', json_encode($settings, JSON_PRETTY_PRINT));
        // Consistent schema file
        Storage::disk('tenant')->put('generated_theme_schema.json', json_encode($settings, JSON_PRETTY_PRINT));

        // Redirect to dashboard with success
        return response()->json(['success' => true, 'redirect' => route('admin.dashboard')]);
    }

    /**
     * Process wizard step updates.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'step' => 'required|string',
            'store_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'brand_color' => 'nullable|string|max:7',
            'industry' => 'nullable|in:fashion,electronics,grocery,hardware',
            'selected_categories' => 'nullable|array',
            'layout_preference' => 'nullable|in:minimal,showcase,catalog,high_volume,brand_showcase,quick_order',
        ]);

        $config = StoreConfig::firstOrNew(['id' => 1]);

        // Handle File Upload
        if ($request->hasFile('logo')) {
            $path = $this->uploader->upload($request->file('logo'), 'branding', 'tenant');
            $config->logo_path = $path;
        }

        // Update fields if present
        if ($request->has('store_name')) $config->store_name = $request->store_name;
        if ($request->has('brand_color')) $config->brand_color = $request->brand_color;
        if ($request->has('industry')) $config->industry = $request->industry;
        if ($request->has('selected_categories')) $config->selected_categories = $request->selected_categories;
        if ($request->has('layout_preference')) $config->layout_preference = $request->layout_preference;

        // CATALOG CURATOR SAVE LOGIC
        if ($request->has('catalog_curation')) {
            $curationData = json_decode($request->catalog_curation, true);
            if (is_array($curationData)) {
                $this->updateCategoryRecursively($curationData);
            }
        }

        $config->save();
        
        // Regenerate Theme Schema on every update to keep it in sync
        $settings = $this->themeService->generateThemeSettings($config);
        Storage::disk('tenant')->put('generated_theme_schema.json', json_encode($settings, JSON_PRETTY_PRINT));

        return response()->json(['success' => true, 'config' => $config]);
    }

    private function updateCategoryRecursively($items) {
        foreach ($items as $index => $item) {
             $cat = Category::find($item['id']);
             if ($cat) {
                 $cat->update([
                     'is_visible_online' => $item['is_visible_online'],
                     'public_display_name' => $item['public_display_name'],
                     'sort_order' => $index + 1
                 ]);
                 if (!empty($item['children'])) {
                     $this->updateCategoryRecursively($item['children']);
                 }
             }
        }
    }
}
