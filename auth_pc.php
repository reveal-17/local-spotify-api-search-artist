<?php
//ログインユーザー
if (!empty($_SESSION['login_date'])) {
    // debug('ログイン経験あり');
    if (($_SESSION['login_date'] + $_SESSION['login_limit']) < time() ) {
        // debug('ログイン有効期限が切れています');
        session_destroy();
        if (basename($_SERVER['PHP_SELF'] !== '/login_pc.php')) {
        header('Location:login_pc.php');
        }
    } else {
        // debug('ログイン有効期限内です');
        $_SESSION['login_date'] = time();
        header('Location:index.php');
    }
} else {
    // debug('ログイン経験なし');
    if (basename($_SERVER['PHP_SELF'] !== '/login_pc.php')) {
        header('Location:login_pc.php');
    }
}
//未ログインユーザー
?>
