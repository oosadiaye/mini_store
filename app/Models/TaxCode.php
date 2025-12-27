<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'rate',
        'type',
        'sales_tax_gl_account',
        'purchase_tax_gl_account',
        'description',
        'is_active',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Boot method to auto-generate GL accounts
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($taxCode) {
            // Auto-generate GL accounts if not provided
            if (empty($taxCode->sales_tax_gl_account) && in_array($taxCode->type, ['sales', 'both'])) {
                $taxCode->sales_tax_gl_account = self::generateSalesTaxGLAccount();
            }

            if (empty($taxCode->purchase_tax_gl_account) && in_array($taxCode->type, ['purchase', 'both'])) {
                $taxCode->purchase_tax_gl_account = self::generatePurchaseTaxGLAccount();
            }
        });
    }

    /**
     * Generate next available Sales Tax GL Account (2100 series)
     */
    protected static function generateSalesTaxGLAccount()
    {
        // Start from 2100 (Sales Tax Payable)
        $lastAccount = Account::where('account_code', 'LIKE', '21%')
            ->orderBy('account_code', 'desc')
            ->first();

        $nextCode = $lastAccount ? (int)$lastAccount->account_code + 1 : 2101;

        // Create the GL account
        Account::create([
            'account_code' => (string)$nextCode,
            'account_name' => 'Sales Tax Payable - ' . request('name', 'Tax'),
            'account_type' => 'liability',
            'description' => 'Sales tax collected to be remitted',
            'is_active' => true,
        ]);

        return (string)$nextCode;
    }

    /**
     * Generate next available Purchase Tax GL Account (1300 series)
     */
    protected static function generatePurchaseTaxGLAccount()
    {
        // Start from 1300 (Input Tax Receivable)
        $lastAccount = Account::where('account_code', 'LIKE', '13%')
            ->orderBy('account_code', 'desc')
            ->first();

        $nextCode = $lastAccount ? (int)$lastAccount->account_code + 1 : 1301;

        // Create the GL account
        Account::create([
            'account_code' => (string)$nextCode,
            'account_name' => 'Input Tax Receivable - ' . request('name', 'Tax'),
            'account_type' => 'asset',
            'description' => 'Tax paid on purchases to be claimed',
            'is_active' => true,
        ]);

        return (string)$nextCode;
    }

    /**
     * Scope to get only active tax codes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get sales tax GL account details
     */
    public function salesTaxAccount()
    {
        return $this->belongsTo(Account::class, 'sales_tax_gl_account', 'account_code');
    }

    /**
     * Get purchase tax GL account details
     */
    public function purchaseTaxAccount()
    {
        return $this->belongsTo(Account::class, 'purchase_tax_gl_account', 'account_code');
    }
}

