<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WeeklyFinancialReport;
use App\Mail\SuperAdminStaffInvitation;
use App\Models\User;
use Illuminate\Http\Request;

class EmailPreviewController extends Controller
{
    public function weeklyFinancialReport()
    {
        // Dummy data for preview
        $tenantName = 'Dplux Technologies';
        $startDate = now()->startOfWeek()->format('M d, Y');
        $endDate = now()->endOfWeek()->format('M d, Y');
        $totalRevenue = '₦1,250,500.00';
        $totalExpenses = '₦450,200.00';
        $netProfit = '₦800,300.00';
        $topProduct = [
            'name' => 'Premium Leather Bag',
            'sold_count' => 125,
            'revenue' => '₦625,000.00'
        ];

        return (new WeeklyFinancialReport(
            $tenantName, 'demo-tenant', $startDate, $endDate, $totalRevenue, $totalExpenses, $netProfit, $topProduct
        ))->render();
    }

    public function staffInvitation()
    {
        // Dummy user
        $user = new User([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ]);
        $password = 'secret123';
        $roleName = 'Support Agent';

        return (new SuperAdminStaffInvitation($user, $password, $roleName))->render();
    }
}
