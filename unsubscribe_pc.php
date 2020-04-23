<?php
// TODO: 公開前に0にする
ini_set('display_errors', 1);

// 関数読み込み
require('function.php');

// spotify web api 使用
require('spotify.php');

if (!empty($_POST['unsubscribe_submit'])) {
    try {
        $dbh = dbConnect();
        $sql1 = "UPDATE user SET delete_flg = 1 WHERE user_id = :user_id";
        $sql2 = "UPDATE favorite SET delete_flg = 1 WHERE user_id = :user_id";
        $data1 = array(':user_id' => $_SESSION['user_id']);
        $data2 = array(':user_id' => $_SESSION['user_id']);
        $stmt1 = queryPost($dbh, $sql1, $data1);
        $stmt2 = queryPost($dbh, $sql2, $data2);

        if ($stmt) {
            // debug('クエリ成功');
            // debug('セッション変数の中身：'.print_r($_SESSION,true));
            session_destroy();
            header('Location: index.php');
        } else {
            // debug('クエリ失敗');
            $error_msg['common'] = MSG8;
        }
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
        $error_msg['common'] = MSG8;
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

                <div class="songsSearch__unsubscribe">
                    <div class="songsSearch__unsubscribeLabel">
                        退会しますか？
                    </div>
                    <div class="area_msg">
                        <?php if (!empty($error_msg['common'])) {
                            echo $error_msg['common'];
                        } ?>
                    </div>
                    <form action="unsubscribe_pc.php" method="post">
                        <el-row>
                            <el-button type="success" plain class="songsSearch__logutInput"
                            native-type="submit" name="unsubscribe_submit">退会</el-button>
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

            },
        });
        </script>
    </body>
</html>