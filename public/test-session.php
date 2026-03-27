<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->bootstrapWith([
    \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
    \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
    \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
    \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
    \Illuminate\Foundation\Bootstrap\BootProviders::class,
]);

echo '<h3>Cookies received:</h3><pre>';
print_r($_COOKIE);
echo '</pre>';

$sessionConfig = config('session');
echo '<h3>Session config:</h3>';
echo 'Cookie name: ' . ($sessionConfig['cookie'] ?? 'not set') . '<br>';
echo 'Driver: ' . ($sessionConfig['driver'] ?? 'not set') . '<br>';
echo 'Domain: ' . ($sessionConfig['domain'] ?? 'null') . '<br>';
echo 'Path: ' . ($sessionConfig['path'] ?? 'not set') . '<br>';

$cookieName = $sessionConfig['cookie'] ?? 'laravel_session';
$sessionId = $_COOKIE[$cookieName] ?? null;
echo '<h3>Looking for cookie: ' . $cookieName . '</h3>';
echo 'Found: ' . ($sessionId ? 'YES (' . substr($sessionId, 0, 10) . '...)' : 'NO') . '<br>';

if ($sessionId) {
    $session = \Illuminate\Support\Facades\DB::table('sessions')->where('id', $sessionId)->first();
    echo '<h3>Session in DB:</h3>';
    if ($session) {
        echo 'user_id: ' . ($session->user_id ?? 'null') . '<br>';
        echo 'ip: ' . ($session->ip_address ?? 'null') . '<br>';
    } else {
        echo 'Not found in DB<br>';
    }
}
