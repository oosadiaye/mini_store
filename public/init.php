<?php
$action = $_GET['action'] ?? null;
$baseDir = dirname(__DIR__);

// Helper to find PHP binary
function getPhpBinary() {
    return defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : 'php';
}

if ($action === 'setup_env') {
    if (!file_exists($baseDir . '/.env')) {
        if (file_exists($baseDir . '/.env.example')) {
            copy($baseDir . '/.env.example', $baseDir . '/.env');
        } else {
            die(".env.example not found.");
        }
    }
    
    // Generate Key
    $php = getPhpBinary();
    $artisan = $baseDir . '/artisan';
    $cmd = "$php $artisan key:generate --ansi";
    shell_exec($cmd);
    
    // Redirect to home
    header("Location: ./");
    exit;
}

if ($action === 'install') {
    // ... (existing install logic)
    // Disable time limit for long running process
    set_time_limit(0);
    
    // Check if composer is available
    $composer = 'composer';
    $check = shell_exec("$composer --version");
    
    if (!$check) {
        // Try looking in common paths or phar
        if (file_exists('composer.phar')) {
            $composer = 'php composer.phar';
        } else {
            die("Composer not found. Please install Composer globally or download composer.phar to this directory.");
        }
    }

    echo '<body style="background:#111827; color:#10B981; font-family:monospace; padding:20px;">';
    echo '<h3>Running Composer Install...</h3>';
    echo '<pre>';
    
    // Stream output
    $cmd = "$composer install --working-dir=.. --no-dev --optimize-autoloader 2>&1";
    $handle = popen($cmd, 'r');
    
    while (!feof($handle)) {
        $buffer = fgets($handle);
        echo htmlspecialchars($buffer);
        flush();
    }
    pclose($handle);
    
    // After install, we should check if .env needs setup
    echo '</pre>';
    echo '<h3 style="color: white">Dependencies Installed. Setting up Environment...</h3>';
    
    // Auto-trigger env setup if needed
    if (!file_exists($baseDir . '/.env')) {
         echo '<script>window.location.href = "?action=setup_env";</script>';
    } else {
         echo '<h3 style="color: white">Complete. <a href="./" style="color:#60A5FA">Click here to continue &rarr;</a></h3>';
    }
    
    echo '<script>window.scrollTo(0,document.body.scrollHeight);</script>';
    echo '</body>';
    exit;
}

// Default View Logic
$vendorMissing = !file_exists($baseDir . '/vendor/autoload.php');
$envMissing = !file_exists($baseDir . '/.env');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Bootstrapper</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow-2xl overflow-hidden text-center">
        <div class="bg-red-600 p-6">
            <h1 class="text-2xl font-bold text-white">System Setup Required</h1>
        </div>
        <div class="p-8">
            <div class="mb-6">
                 <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            
            <?php if ($vendorMissing): ?>
                <p class="text-gray-600 mb-6">Application dependencies (vendor) are missing.</p>
                <a href="init.php?action=install" class="inline-block w-full px-6 py-4 bg-gray-900 text-white rounded-lg font-bold hover:bg-gray-800 transition shadow-lg">RUN COMPOSER INSTALL</a>
            <?php elseif ($envMissing): ?>
                <p class="text-gray-600 mb-6">Environment file (.env) and Application Key are missing.</p>
                <a href="init.php?action=setup_env" class="inline-block w-full px-6 py-4 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition shadow-lg">GENERATE APP KEY</a>
            <?php else: ?>
                <p class="text-green-600 mb-6">System appears ready!</p>
                <a href="./" class="inline-block w-full px-6 py-4 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition shadow-lg">CONTINUE TO APP</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
