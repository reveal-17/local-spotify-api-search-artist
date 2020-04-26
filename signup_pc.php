<?php
// TODO: 公開前に0にする
ini_set('display_errors', 0);

// 関数読み込み
require('function.php');

// spotify web api 使用
require('spotify.php');

// フォーム送信後

if (!empty($_POST)) {

    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_retype = $_POST['password_retype'];

    //未入力チェック
    validRequired($user_name, 'user_name');
    validRequired($email, 'email');
    validRequired($password, 'password');
    validRequired($password_retype, 'password_retype');

    if (empty($error_msg)) {
    //        name最大文字数
        validMaxLen($user_name, 'user_name');
    //        email最大文字数、形式チェック、重複チェック
        validMaxLen($email, 'email');
        validEmail($email, 'email');
        validEmailDup($email);
    //        password最小文字数、最大文字数、半角英数字チェック
        validMinLen($password, 'password');
        validMaxLen($password, 'password');
        validHalf($password,'password');

    //            passとpass_reが一致しているかどうか
        validSame($password, $password_retype, 'password_retype');

        if (empty($error_msg)) {
            //            DB接続→インサート
            try {
                $dbh = dbConnect();
                $sql = 'INSERT INTO user (user_name, email, password, login_time, create_time) VALUES (:user_name, :email,:password, :login_time, :create_time)';
                $data = array(':user_name' => $user_name, ':email' => $email, ':password' => password_hash($password, PASSWORD_DEFAULT), ':login_time' => date("Y/m/d H:i:s"), ':create_time' => date("Y/m/d H:i:s"));
                $stmt = queryPost($dbh, $sql, $data);
                if ($stmt) {
                    $sesLimit = 60*60;
                    $_SESSION['login_date'] = time();
                    $_SESSION['login_limit'] = $sesLimit;
                    $_SESSION['user_id'] = $dbh->lastInsertId();
                    header('Location:index.php');
                } else {
                    return false;
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

                <div class="songsSearch__signup">
                    <div class="songsSearch__signupLabel">
                        新規登録
                    </div>
                    <div class="area_msg">
                        <?php if (!empty($error_msg['common'])) {
                            echo $error_msg['common'];
                        } ?>
                    </div>
                    <form action="signup_pc.php" method="post">
                        <!-- user_name -->
                        <el-input
                            name="user_name"
                            type="text"
                            placeholder="ユーザー名"
                            v-model="user_name"
                            maxlength="255"
                            show-word-limit
                            class="songsSearch__signupInput"
                            >
                        </el-input>
                        <div class="area_msg">
                        <?php if (!empty($error_msg['user_name'])) {
                            echo $error_msg['user_name'];
                        } ?>
                        </div>

                        <!-- email -->
                        <el-input
                            name="email"
                            type="text"
                            placeholder="メールアドレス"
                            v-model="email"
                            maxlength="255"
                            show-word-limit
                            class="songsSearch__signupInput"
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
                            class="songsSearch__signupInput"></el-input>
                        <div class="area_msg">
                        <?php if (!empty($error_msg['password'])) {
                            echo $error_msg['password'];
                        } ?>
                        </div>

                        <!-- password_retype -->
                        <el-input
                            name="password_retype" placeholder="パスワード（再入力）" v-model="password_retype" show-password
                            class="songsSearch__signupInput"></el-input>
                        <div class="area_msg">
                        <?php if (!empty($error_msg['password_retype'])) {
                            echo $error_msg['password_retype'];
                        } ?>
                        </div>
                        <el-row>
                            <el-button type="success" plain class="songsSearch__signupInput" native-type="submit" name="signup_submit">登録</el-button>
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
                user_name: "<?php if(!empty($_POST['user_name'])) {echo $_POST['user_name'];} ?>",
                email: "<?php if(!empty($_POST['email'])) {echo $_POST['email'];} ?>",
                password: "",
                password_retype: "",
            },
        });
        </script>
    </body>
</html>
