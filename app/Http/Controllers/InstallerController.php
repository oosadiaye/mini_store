<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class InstallerController extends Controller
{
    public function welcome()
    {
        return view('install.welcome');
    }

    public function requirements()
    {
        $requirements = [
            'PHP Version >= 8.1' => version_compare(phpversion(), '8.1.0', '>='),
            'BCMath Extension' => extension_loaded('bcmath'),
            'Ctype Extension' => extension_loaded('ctype'),
            'Fileinfo Extension' => extension_loaded('fileinfo'),
            'JSON Extension' => extension_loaded('json'),
            'Mbstring Extension' => extension_loaded('mbstring'),
            'OpenSSL Extension' => extension_loaded('openssl'),
            'PDO Extension' => extension_loaded('pdo'),
            'Tokenizer Extension' => extension_loaded('tokenizer'),
            'XML Extension' => extension_loaded('xml'),
            'Storage Directory Writable' => is_writable(storage_path()),
            'Bootstrap Cache Writable' => is_writable(base_path('bootstrap/cache')),
        ];

        $allMet = !in_array(false, $requirements);

        return view('install.requirements', compact('requirements', 'allMet'));
    }

    public function environment()
    {
        return view('install.environment');
    }

    public function saveEnvironment(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string',
            'app_url' => 'required|url',
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        $envContent = file_get_contents(base_path('.env.example'));
        
        $replacements = [
            'APP_NAME' => '"' . $validated['app_name'] . '"',
            'APP_URL' => $validated['app_url'],
            'DB_HOST' => $validated['db_host'],
            'DB_PORT' => $validated['db_port'],
            'DB_DATABASE' => $validated['db_database'],
            'DB_USERNAME' => $validated['db_username'],
            'DB_PASSWORD' => '"' . ($validated['db_password'] ?? '') . '"',
            'APP_KEY' => 'base64:' . base64_encode(random_bytes(32)), // Generate a key immediately
        ];

        foreach ($replacements as $key => $value) {
            $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
        }

        file_put_contents(base_path('.env'), $envContent);

        return redirect()->route('install.database');
    }

    public function database()
    {
        return view('install.database');
    }

    public function runMigration()
    {
        try {
            // Force using the new credentials by purging the db connection
            DB::purge('mysql');
            
            Artisan::call('migrate:fresh', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]); // Seed default plans/roles if any
            
            return redirect()->route('install.admin')->with('success', 'Database migrated and seeded successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Migration failed: ' . $e->getMessage());
        }
    }

    public function admin()
    {
        return view('install.admin');
    }

    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'super_admin', // Assuming you have a 'role' column or similar
            ]);

            // Assign Super Admin Role if using Spatie or specific logic
            // $user->assignRole('Super Admin');

            file_put_contents(storage_path('installed'), 'Installed on ' . now());

            return redirect()->route('install.finish');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create admin: ' . $e->getMessage());
        }
    }

    public function finish()
    {
        return view('install.finish');
    }
}
