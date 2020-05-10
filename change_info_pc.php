<?php
// TODO: 公開前に0にする
ini_set('display_errors', 0);

// 関数読み込み
require('function.php');

// spotify web api 使用
require('spotify.php');

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
                        ユーザー情報
                    </div>
                    <div class="area_msg">
                        <?php if (!empty($error_msg['common'])) {
                            echo $error_msg['common'];
                        } ?>
                    </div>
                    <form action="login_pc.php" method="post">
                        <!-- user_name -->
                        <el-input
                            name="user_name"
                            type="text"
                            placeholder="ユーザーネーム"
                            v-model="user_name"
                            show-word-limit
                            class="songsSearch__loginInput"
                            >
                        </el-input>
                        <div class="area_msg">
                        <?php if (!empty($error_msg['user_name'])) {
                            echo $error_msg['user_name'];
                        } ?>
                        </div>

                        <!-- 画像アップロード -->
                        <form action="upload-output.php" method="post" enctype="multipart/form-data">
                            <input type="file" name="file">
                        </form>

                        <el-row>
                            <el-button type="success" plain class="songsSearch__loginInput"
                                native-type="submit" name="change_submit">変更</el-button>
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
            },
        });
        </script>
    </body>
</html>
