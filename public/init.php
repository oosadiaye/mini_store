<?php
// init.php - Dependency Bootstrapper

$action = $_GET['action'] ?? null;

if ($action === 'install') {
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
    $cmd = "$composer install --no-dev --optimize-autoloader 2>&1";
    $handle = popen($cmd, 'r');
    
    while (!feof($handle)) {
        $buffer = fgets($handle);
        echo htmlspecialchars($buffer);
        flush();
    }
    pclose($handle);
    
    echo '</pre>';
    echo '<h3 style="color: white">Installation Complete. <a href="/" style="color:#60A5FA">Click here to continue &rarr;</a></h3>';
    echo '<script>window.scrollTo(0,document.body.scrollHeight);</script>';
    echo '</body>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Bootstrapper</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow-2xl overflow-hidden text-center">
        <div class="bg-red-600 p-6">
            <h1 class="text-2xl font-bold text-white">Dependencies Missing</h1>
        </div>
        <div class="p-8">
            <div class="mb-6">
                 <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <p class="text-gray-600 mb-6">
                The application dependencies (vendor folder) are not installed. 
                This usually happens on a fresh deployment.
            </p>

            <a href="init.php?action=install" class="inline-block w-full px-6 py-4 bg-gray-900 text-white rounded-lg font-bold hover:bg-gray-800 transition transform hover:scale-105 shadow-lg">
                RUN COMPOSER INSTALL
            </a>
            
            <p class="text-xs text-gray-400 mt-4">
                This process may take a minute. Please do not close the window.
            </p>
        </div>
    </div>
</body>
</html>
