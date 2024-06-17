<header>
    <nav>
        <h1><a href="main_page.php">TRINTED</a></h1>
        <a href="<?php echo $isLoggedIn ? 'logout.php' : 'login.php'; ?>">
            <?php echo $isLoggedIn ? 'Logout' : 'Login'; ?>
        </a>
        
        <?php if ($isLoggedIn) : ?>
            <?php if (basename($_SERVER['PHP_SELF']) !== 'search_products.php') : ?>
                <a href="search_products.php">Browse</a>
            <?php endif; ?>
            <?php if (basename($_SERVER['PHP_SELF']) !== 'message.php') : ?>
                <a href="message.php">Messages</a>
            <?php endif; ?>
            <?php if (basename($_SERVER['PHP_SELF']) !== 'sell.php') : ?>
                <a href="sell.php">Sell</a>
            <?php endif; ?>
            <?php if (basename($_SERVER['PHP_SELF']) !== 'wishlist.php') : ?>
                <a href="wishlist.php">Wishlist</a>
            <?php endif; ?>
            <?php if (basename($_SERVER['PHP_SELF']) !== 'profile_page.php') : ?>
                <a href="profile_page.php">Profile</a>
            <?php endif; ?>
            <?php if (isset($isAdmin) && $isAdmin) : ?>
                <?php if (basename($_SERVER['PHP_SELF']) !== 'admin.php') : ?>
                    <a href="admin.php">Admin</a>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </nav>
</header>
