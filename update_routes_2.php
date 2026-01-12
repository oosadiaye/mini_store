<?php
$path = 'c:\Users\Jac\Documents\Antigravity\mini_store\routes\tenant.php';
$content = file_get_contents($path);

// 1. Wrap Store Setup Wizard
$wizardTarget = "// Store Setup Wizard\n        Route::get('/wizard', [Admin\StoreSetupWizardController::class, 'index'])->name('wizard.index');\n        Route::post('/wizard/update', [Admin\StoreSetupWizardController::class, 'update'])->name('wizard.update');\n        Route::post('/wizard/finish', [Admin\StoreSetupWizardController::class, 'finish'])->name('wizard.finish');";
$wizardReplacement = "// Store Setup Wizard\n        Route::middleware(['storefront_active'])->group(function () {\n            Route::get('/wizard', [Admin\StoreSetupWizardController::class, 'index'])->name('wizard.index');\n            Route::post('/wizard/update', [Admin\StoreSetupWizardController::class, 'update'])->name('wizard.update');\n            Route::post('/wizard/finish', [Admin\StoreSetupWizardController::class, 'finish'])->name('wizard.finish');\n        });";

// 2. Wrap CMS Visual Editor
$cmsTarget = "// CMS Visual Editor\n        Route::post('/cms/save', [\App\Http\Controllers\Admin\CMSController::class, 'save'])->name('cms.save');";
$cmsReplacement = "// CMS Visual Editor\n        Route::middleware(['storefront_active'])->group(function () {\n            Route::post('/cms/save', [\App\Http\Controllers\Admin\CMSController::class, 'save'])->name('cms.save');\n        });";

// 3. Add to Pages/Posts group (already has feature:online_store)
$pagesTarget = "Route::middleware(['feature:online_store'])->group(function () {";
$pagesReplacement = "Route::middleware(['feature:online_store', 'storefront_active'])->group(function () {";

$content = str_replace($wizardTarget, $wizardReplacement, $content);
$content = str_replace($cmsTarget, $cmsReplacement, $content);
$content = str_replace($pagesTarget, $pagesReplacement, $content);

file_put_contents($path, $content);
echo "Successfully updated routes/tenant.php with storefront_active middleware";
