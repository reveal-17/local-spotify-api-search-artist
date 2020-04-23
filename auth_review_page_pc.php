<?php
function isLogin() {
    //ログインユーザー
    if (!empty($_SESSION['login_date'])) {
        // debug('ログイン経験あり');
        if (($_SESSION['login_date'] + $_SESSION['login_limit']) < time() ) {
            // debug('ログイン有効期限が切れています');
            return $is_login = false;
        } else {
            // debug('ログイン有効期限内です');
            $_SESSION['login_date'] = time();
            return $is_login = true;
        }
    } else {
        // debug('ログイン経験なし');
        if (basename($_SERVER['PHP_SELF'] !== '/index_pc.php')) {
            return $is_login = false;
        }
    }
    //未ログインユーザー
    return false;
}
?>
