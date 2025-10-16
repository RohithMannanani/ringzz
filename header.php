<?php
require_once __DIR__ . '/config.php';
?>
<style>
	.header{position:sticky;top:0;z-index:1000;background:#222;color:#fff;padding:12px 16px;display:flex;align-items:center;justify-content:space-between}
	.header a{color:#fff;text-decoration:none;margin-right:12px}
	.header .right a.btn{background:#dc3545;padding:6px 10px;border-radius:4px}
	.header .username{opacity:.85;margin-right:12px}
</style>
<div class="header">
	<div class="left">
        <a class="brand" href="<?= BASE_URL ?>index.php">Ringzz</a>
        <?php if (empty($_SESSION['user_id'])) : ?>
            <a href="<?= BASE_URL ?>index.php">Home</a>
            <a href="<?= BASE_URL ?>classes/register.php">Register</a>
            <a href="<?= BASE_URL ?>classes/login.php">Login</a>
        <?php else : ?>
            <a href="<?= BASE_URL ?>index.php">Home</a>
        <?php endif; ?>
    </div>
    <div class="right">
        <?php if (!empty($_SESSION['user_id'])) : ?>
            <span class="username">Hi, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
            <a class="btn" href="<?= BASE_URL ?>classes/logout.php">Logout</a>
        <?php endif; ?>
    </div>
</div>
