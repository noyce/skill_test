<?php
require_once __DIR__ . '/../services/SessionManager.php';

use App\Services\SessionManager;

// Start session before any output
SessionManager::ensureSessionStarted();
?>

<h1>Dashboard!</h1>

<?php
if (SessionManager::has('logged_in_user_id')) {
    echo "Employee ID: " . SessionManager::getLoggedInUserId() . " is logged in!";
}