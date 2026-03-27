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

$cookieName = config('session.cookie', 'laravel_session');
$encrypted = $_COOKIE[$cookieName] ?? null;

echo 'Cookie name: ' . $cookieName . '<br>';
echo 'Cookie found: ' . ($encrypted ? 'YES' : 'NO') . '<br>';

if ($encrypted) {
    // Try decrypt
    try {
        $decrypted = \Illuminate\Support\Facades\Crypt::decryptString($encrypted);
        echo 'Decrypted session ID: ' . $decrypted . '<br>';

        $session = \Illuminate\Support\Facades\DB::table('sessions')->where('id', $decrypted)->first();
        echo 'Session in DB: ' . ($session ? 'YES, user_id=' . ($session->user_id ?? 'null') : 'NOT FOUND') . '<br>';
    } catch (\Throwable $e) {
        echo 'DecryptString failed: ' . $e->getMessage() . '<br>';
    }

    // Try decrypt (non-string)
    try {
        $decrypted2 = \Illuminate\Support\Facades\Crypt::decrypt($encrypted, false);
        echo 'Decrypt (non-string): ' . $decrypted2 . '<br>';

        $session2 = \Illuminate\Support\Facades\DB::table('sessions')->where('id', $decrypted2)->first();
        echo 'Session in DB: ' . ($session2 ? 'YES, user_id=' . ($session2->user_id ?? 'null') : 'NOT FOUND') . '<br>';
    } catch (\Throwable $e) {
        echo 'Decrypt failed: ' . $e->getMessage() . '<br>';
    }

    // Show all sessions in DB
    echo '<br>All sessions in DB:<br>';
    $sessions = \Illuminate\Support\Facades\DB::table('sessions')->get();
    foreach ($sessions as $s) {
        echo '- id=' . substr($s->id, 0, 20) . '... user_id=' . ($s->user_id ?? 'null') . '<br>';
    }
}
