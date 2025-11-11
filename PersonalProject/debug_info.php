<?php
// debug_info.php - one-off diagnostic page
// Drop into project root, open in browser while logged in

// include config (which starts session)
include 'config.php';

// Small helper to safely print arrays
function p($v){ echo '<pre style="color:#fff;background:#111;padding:12px;border-radius:8px;">'.htmlspecialchars(print_r($v, true)).'</pre>'; }

// Header
echo '<div style="font-family:Segoe UI,Roboto,Arial;color:#fff;background:#0b0f13;padding:18px;border-radius:8px;margin:18px">';

echo "<h2 style='margin:0 0 12px;color:#fff'>DEBUG INFO</h2>";

// Session info
echo "<h3 style='color:#ddd;margin-bottom:6px'>Session</h3>";
p($_SESSION);

// Current user helper values
echo "<h3 style='color:#ddd;margin-bottom:6px'>getCurrentUserId() and role</h3>";
echo "<div style='margin-bottom:10px;color:#fff'>getCurrentUserId() = " . var_export(function_exists('getCurrentUserId') ? getCurrentUserId() : null, true) . "</div>";
echo "<div style='margin-bottom:18px;color:#fff'>\$_SESSION['role'] = " . var_export($_SESSION['role'] ?? null, true) . "</div>";

// Users table (first 20)
echo "<h3 style='color:#ddd;margin-bottom:6px'>Users (first 20)</h3>";
try {
    $u = $pdo->query("SELECT id, username, email, role FROM users ORDER BY id ASC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
    p($u);
} catch (Exception $e) {
    echo "<div style='color:#ffc0c0'>Error reading users table: ".htmlspecialchars($e->getMessage())."</div>";
}

// Tasks table (last 50)
echo "<h3 style='color:#ddd;margin-bottom:6px'>Tasks (last 50 rows)</h3>";
try {
    $t = $pdo->query("SELECT id, title, user_id, status, created_at FROM tasks ORDER BY id DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
    p($t);
} catch (Exception $e) {
    echo "<div style='color:#ffc0c0'>Error reading tasks table: ".htmlspecialchars($e->getMessage())."</div>";
}

// Count tasks per user_id (quick sanity)
echo "<h3 style='color:#ddd;margin-bottom:6px'>Task counts by user_id</h3>";
try {
    $s = $pdo->query("SELECT IFNULL(user_id,'NULL') as user_id, COUNT(*) as cnt FROM tasks GROUP BY user_id ORDER BY cnt DESC");
    p($s->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "<div style='color:#ffc0c0'>Error counting tasks: ".htmlspecialchars($e->getMessage())."</div>";
}

// Show last 20 logs from PHP error log if readable (optional)
echo "<h3 style='color:#ddd;margin-bottom:6px'>PHP error_log (last 40 lines) - optional</h3>";
$logpath = ini_get('error_log');
if ($logpath && file_exists($logpath)) {
    $lines = array_slice(file($logpath, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES), -40);
    echo "<pre style='color:#ddd;background:#111;padding:10px;border-radius:6px;'>" . htmlspecialchars(implode("\n", $lines)) . "</pre>";
} else {
    echo "<div style='color:#aaa'>No readable PHP error_log found (path: " . htmlspecialchars($logpath) . ").</div>";
}

echo "</div>";

// Reminder
echo "<div style='font-family:Segoe UI,Arial;color:#fff;margin:12px'>When you're done debugging, delete <code>debug_info.php</code> for security.</div>";
