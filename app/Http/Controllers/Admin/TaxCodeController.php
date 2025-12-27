<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaxCode;
use Illuminate\Http\Request;

class TaxCodeController extends Controller
{
    public function index()
    {
        $taxCodes = TaxCode::with(['salesTaxAccount', 'purchaseTaxAccount'])->latest()->get();
        return view('admin.tax-codes.index', compact('taxCodes'));
    }

    public function create()
    {
        return view('admin.tax-codes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:tax_codes,code',
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:sales,purchase,both',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        TaxCode::create($validated);

        return redirect()->route('admin.tax-codes.index')
            ->with('success', 'Tax code created successfully with auto-generated GL accounts!');
    }

    public function edit(TaxCode $taxCode)
    {
        return view('admin.tax-codes.edit', compact('taxCode'));
    }

    public function update(Request $request, TaxCode $taxCode)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:tax_codes,code,' . $taxCode->id,
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:sales,purchase,both',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $taxCode->update($validated);

        return redirect()->route('admin.tax-codes.index')
            ->with('success', 'Tax code updated successfully!');
    }


    public function destroy($id)
    {
        $taxCode = TaxCode::findOrFail($id);
        $taxCode->delete();

        return redirect()->route('admin.tax-codes.index')
            ->with('success', 'Tax code deleted successfully!');
    }
}
