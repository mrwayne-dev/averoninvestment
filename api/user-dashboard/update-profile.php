<?php
/* =====================================================================
   api/user-dashboard/update-profile.php
   Updates first name, last name, country (region), and language
   for the authenticated user.

   Method: POST
   Auth:   Session required (user_id)
   Body:   { first_name, last_name, country, language }
   ===================================================================== */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once '../../config/database.php';
require_once '../../config/constants.php';
require_once '../utilities/helper.php';

// ── Auth guard ────────────────────────────────────────────────────────
session_start();

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (isset($_SESSION['last_active']) && (time() - $_SESSION['last_active']) > SESSION_TIMEOUT) {
    session_unset();
    session_destroy();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Session expired']);
    exit;
}
$_SESSION['last_active'] = time();

// ── Method + AJAX guards ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

// ── Parse + sanitise input ────────────────────────────────────────────
$input     = json_decode(file_get_contents('php://input'), true) ?? [];
$firstName = sanitize($input['first_name'] ?? '');
$lastName  = sanitize($input['last_name']  ?? '');
$country   = sanitize($input['country']    ?? '');
$language  = sanitize($input['language']   ?? '');

// ── Validation ────────────────────────────────────────────────────────
if (strlen($firstName) < 2 || strlen($firstName) > 100) {
    echo json_encode(['success' => false, 'message' => 'First name must be between 2 and 100 characters.']);
    exit;
}

if (strlen($lastName) < 2 || strlen($lastName) > 100) {
    echo json_encode(['success' => false, 'message' => 'Last name must be between 2 and 100 characters.']);
    exit;
}

$userId = (int) $_SESSION['user_id'];

try {
    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare(
        'UPDATE users
         SET    first_name = :fn,
                last_name  = :ln,
                region     = :region,
                language   = :lang
         WHERE  id = :uid'
    );
    $stmt->execute([
        ':fn'     => $firstName,
        ':ln'     => $lastName,
        ':region' => $country,
        ':lang'   => $language,
        ':uid'    => $userId,
    ]);

    // Refresh session display name
    $_SESSION['user_name'] = $firstName;

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully.',
        'data'    => [
            'first_name' => $firstName,
            'last_name'  => $lastName,
        ],
    ]);

} catch (Throwable $e) {
    error_log('[update-profile] User ' . $userId . ': ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update profile. Please try again.']);
}
