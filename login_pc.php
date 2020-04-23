<?php
// TODO: 公開前に0にする
ini_set('display_errors', 1);

// 関数読み込み
require('function.php');

// spotify web api 使用
require('spotify.php');

// 認証
require('auth_pc.php');

if (!empty($_POST)) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $pass_save =(!empty($_POST['pass_save'])) ? true : false;

    // 未入力チェック
    validRequired($email, 'email');
    validRequired($password, 'password');

    if (empty($error_msg)) {
//        email最大文字数、形式チェック
        validMaxLen($email, 'email');
        validEmail($email, 'email');

//        password最小文字数、最大文字数、半角英数字チェック
        validMinLen($password, 'password');
        validMaxLen($password, 'password');
        validHalf($password, 'password');

        if (empty($error_msg)) {
            try {
//            DB接続
                $dbh = dbConnect();
                $sql = 'SELECT password, user_id  FROM user WHERE email = :email';
                $data = array(':email' => $email);
                $stmt = queryPost($dbh, $sql, $data);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!empty($stmt) && password_verify($password, array_shift($result))) {
                    // 成功
                    $sesLimit = 60*60;
                    $_SESSION['login_date'] = time();
                    $_SESSION['user_id'] = $result['user_id'];
                    if ($pass_save) {
                        // 自動ログインチェック
                        $_SESSION['login_limit'] = $sesLimit*24*30;
                    } else {
                        // 自動ログインチェックなし
                        $_SESSION['login_limit'] = $sesLimit;
                    }
                    header('Location:index.php');
                } else {
                    // クエリ失敗orパスワードなし
                    $error_msg['common'] = MSG8;
                }
            } catch(Exception $e) {
                echo $e->getMessage();
                $error_msg['common'] = MSG8;
            }
        }
    }
}

?>

<html>
    <!-- ヘッドタグ -->
    <?php require('components/head_pc.php'); ?>

    <body>
        <div id="app">
            <div class="songsSearch">
                <!-- ヘッダー -->
                <?php require('components/header_pc.php'); ?>

                <div class="songsSearch__login">
                    <div class="songsSearch__loginLabel">
                        ログイン
                    </div>
                    <div class="area_msg">
                        <?php if (!empty($error_msg['common'])) {
                            echo $error_msg['common'];
                        } ?>
                    </div>
                    <form action="login_pc.php" method="post">
                        <!-- email -->
                        <el-input
                            name="email"
                            type="text"
                            placeholder="メールアドレス"
                            v-model="email"
                            show-word-limit
                            class="songsSearch__loginInput"
                            >
                        </el-input>
                        <div class="area_msg">
                        <?php if (!empty($error_msg['email'])) {
                            echo $error_msg['email'];
                        } ?>
                        </div>

                        <!-- password -->
                        <el-input
                            name="password" placeholder="パスワード" v-model="password" show-password
                            class="songsSearch__loginInput"></el-input>
                        <div class="area_msg">
                        <?php if (!empty($error_msg['password'])) {
                            echo $error_msg['password'];
                        } ?>
                        </div>

                        <!-- checkbox -->
                        <div>
                            <el-checkbox v-model="checked1" label="次回から自動ログインする" border
                                class="songsSearch__loginInput" name="pass_save"></el-checkbox>
                        </div>

                        <el-row>
                            <el-button type="success" plain class="songsSearch__loginInput"
                                native-type="submit" name="login_submit">ログイン</el-button>
                        </el-row>
                    </form>
                </div>
                <!-- フッター -->
                <?php require('components/footer_pc.php'); ?>
            </div>
        </div>

        <!-- import Vue before Element -->
        <script src="https://unpkg.com/vue/dist/vue.js"></script>
        <!-- import JavaScript -->
        <script src="https://unpkg.com/element-ui/lib/index.js"></script>
        <!-- import axios before Element -->
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

        <script>
        new Vue({
            el: "#app",
            data: {
                email: "<?php if(!empty($_POST['email'])) {echo $_POST['email'];} ?>",
                password: "",
                checked1: false,
            },
        });
        </script>
    </body>
</html>
