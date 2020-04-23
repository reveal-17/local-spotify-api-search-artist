<?php
// TODO: 公開前に0にする
ini_set('display_errors', 1);

// 関数読み込み
require('function.php');

// spotify web api 使用
require('spotify.php');

// ユーザーID
$user_id = $_SESSION['user_id'];

// お気に入り状況取得（外部化で不具合発生のためこのまま）
try {
    $dbh = dbConnect();
    $sql = "SELECT is_favorite FROM favorite WHERE user_id = :user_id";
    $data = array(":user_id" => "${user_id}");
    $stmt = queryPost($dbh, $sql, $data);
    // お気に入りあるかどうか判別（switchの初期値を動的表示）
    $countGoodResult = $stmt->rowCount();
} catch (Exception $e) {
    echo $e->getMessage();
}

// レビュー状況取得
try {
    $dbh = dbConnect();
    $sql = "SELECT comment_id FROM public_comment WHERE user_id = :user_id";
    $data = array(":user_id" => "${user_id}");
    $stmt = queryPost($dbh, $sql, $data);
    // お気に入りあるかどうか判別（switchの初期値を動的表示）
    $countReviewResult = $stmt->rowCount();
} catch (Exception $e) {
    echo $e->getMessage();
}

// ユーザーのいいね一覧
$userGoodData = getUserGood($user_id);

// ユーザーのレビュー一覧
$userReviewData = getUserReview($user_id);

?>

<html>
    <!-- ヘッドタグ -->
    <?php require('components/head_pc.php'); ?>

    <body>
        <div id="app">
            <div class="songsSearch">
                <!-- ヘッダー -->
                <?php require('components/header_pc.php'); ?>

                <div class="songsSearch__mypage">
                    <el-card class="songsSearch__boxCard box-card">
                        <!-- TODO: マイページの画像変更する -->
                        <el-tooltip content="画像を変更する" placement="top">
                            <el-avatar :size="50" :src="circleUrlDefault"></el-avatar>
                        </el-tooltip>
                        <div class="sub-title">
                            user_name
                        </div>
                    </el-card>

                    <div class="songsSearch__tab">
                        <el-tabs type="border-card">
                            <el-tab-pane label="いいね一覧">
                                <?php if ($countGoodResult) :?>

                                <?php else : ?>
                                <p class="songsSearch__tabInfo">現在、いいねはありません。</p>
                                <?php endif; ?>
                            </el-tab-pane>
                            <el-tab-pane label="レビュー一覧">
                                <?php if ($countReviewResult) :?>

                                <?php else : ?>
                                <p class="songsSearch__tabInfo">現在、レビューはありません。</p>
                                <?php endif; ?>
                            </el-tab-pane>
                        </el-tabs>
                    </div>
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
                circleUrlDefault: "https://cube.elemecdn.com/3/7c/3ea6beec64369c2642b92c6726f1epng.png",
            },
        });
        </script>
    </body>
</html>
