<?php if(Base::app()->user->isLoggedIn): ?>
    <h1>Giriş yaptınız:  <?php echo Base::app()->user->id; ?></h1>
<?php else: ?>
<form method="post">
    <input type="text" name="username">
    <input type="password" name="password">
    <input type="submit">
</form>

<?php endif; ?>