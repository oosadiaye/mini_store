<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTenants = Tenant::count();
        // Placeholder for revenue logic
        
        return view('superadmin.dashboard', compact('totalTenants'));
    }
}
