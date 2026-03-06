<?php 
// Router / layout for each endpoint in the home page tabs. 
$URI = $_SERVER['REQUEST_URI'] ?? '';

$page=explode('&tab=', $URI)[1] ?? 'dashboard';

$allowedTabs = ['dashboard', 'login', 'register', 'setup_2fa', 'quick_reference', 'demo','logout'];

//Strip .php suffix if present to allow for cleaner URLs in public mode
$page = str_replace('.php', '', $page);

// Switch case  to set which endpoint to include within the main content area
$pageContent = '';
switch ($page) {
    case 'dashboard':
        $pageContent = include __DIR__ . '/dashboard.php';
        break;
    case 'login':
        $pageContent = include __DIR__ . '/login.php';
        break;
    case 'register':
        $pageContent = include __DIR__ . '/register.php';
        break;
    case 'setup_2fa':
        $pageContent = include __DIR__ . '/setup_2fa.php';
        break;
    case 'demo':
        $pageContent = include __DIR__ . '/demo.php';
        break;
    case 'logout':
        $pageContent = include __DIR__ . '/logout.php';
        break;
    default:
        $pageContent = include __DIR__ . '/dashboard.php';
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Styles CSS-->
    <link rel="stylesheet" href="/assets/css/reset.css">
    <link rel="stylesheet" href="/assets/css/typography.css">
    <link rel="stylesheet" href="/assets/css/styles.css">

    <!-- Link HubotSans -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hubot+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap">
    <!-- JetBrains Mono -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap">
    <!-- FontAwesome  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Primer Style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/primer/28.0.0/primer.min.css" />    

    <script src="/assets/js/index.js" defer> 
        // Debug: Log Loaded 
        console.log('index.js loaded');
    </script>

    <title> Shadow Auth</title>
</head>
<body>
    <main class="content-wrapper" id=<?= htmlspecialchars($page, ENT_QUOTES, 'UTF-8')."_page" ?> >
        <?php echo $pageContent; ?>

    </main>

</body>
<!-- footer -->
 <?php include __DIR__ . '/../partials/home_footer.php' ?>

</html>