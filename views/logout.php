<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use DevinciIT\ShadowAuth\Core\Flash;
use DevinciIT\ShadowAuth\Facade\Auth;

$isPublicMode = defined('SHADOW_AUTH_PUBLIC_MODE') && SHADOW_AUTH_PUBLIC_MODE;
$loginUrl = $isPublicMode ? shadow_auth_public_url('login') : '/views/login.php';

Auth::logout();
Flash::set('You have been successfully logged out.');

// Redirect timer to allow flash message to be seen before redirecting
?>
<p>You have been logged out. Redirecting to login page...</p>
<script>
    setTimeout(function() {
        window.location.href = <?= json_encode($loginUrl) ?>;
    }, 3000); // Redirect after 3 seconds   
</script>
<?php

header('Location: ' . $loginUrl);
exit;
