<?php
// TODO: 公開前に0にする
ini_set('display_errors', 0);

// 関数読み込み
require('function.php');

// spotify web api 使用
require('spotify.php');

// ユーザーID
$user_id = $_SESSION['user_id'];

if ($_POST["change_name_submit"] === "") {
    if (!empty($_POST["user_name"])) {
        $user_name = $_POST["user_name"];
        try {
            $dbh = dbConnect();
            $sql = "UPDATE user SET user_name = :user_name WHERE user_id = :user_id";
            $data = array("user_name" => $user_name, ":user_id" => $user_id);
            $stmt = queryPost($dbh, $sql, $data);
            $error_msg['user_name'] = "ユーザー名を変更しました";
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

if (isset($_FILES)&& isset($_FILES['upfile']) && is_uploaded_file($_FILES['upfile']['tmp_name'])) {
    if (!file_exists('upload')) {
        mkdir('upload');
    }
    $a = 'upload/' . basename($_FILES['upfile']['name']);
    if (move_uploaded_file($_FILES['upfile']['tmp_name'], $a)) {
        $error_msg['image'] = $a. 'のアップロードに成功しました';

        try {
            $dbh = dbConnect();
            $sql = "UPDATE user SET image_url = :image_url WHERE user_id = :user_id";
            $data = array(":image_url" => $a, ":user_id" => $user_id);
            $stmt = queryPost($dbh, $sql, $data);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

    } else {
        $error_msg['image'] = 'アップロードに失敗しました';
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
                        ユーザー情報
                    </div>
                    <div class="area_msg">
                        <?php if (!empty($error_msg['common'])) {
                            echo $error_msg['common'];
                        } ?>
                    </div>
                    <form action="change_info_pc.php" method="post">
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

                        <el-row>
                            <el-button type="success" plain class="songsSearch__loginInput"
                                native-type="submit" name="change_name_submit">ユーザー名変更</el-button>
                        </el-row>
                    </form>

                    <!-- 画像アップロード -->
                    <form action="change_info_pc.php" method="post" enctype="multipart/form-data">
                        <input type="file" name="upfile">

                        <div class="area_msg">
                        <?php if (!empty($error_msg['image'])) {
                            echo $error_msg['image'];
                        } ?>
                        </div>

                        <el-row>
                            <el-button type="success" plain class="songsSearch__loginInput"
                                native-type="submit" name="change_image_submit">ユーザー画像変更</el-button>
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
