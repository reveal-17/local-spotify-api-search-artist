<?php
// TODO: 公開前に0にする
ini_set('display_errors', 0);

// 関数読み込み
require('function.php');

// spotify web api 使用
require('spotify.php');

// ユーザーID
$user_id = $_SESSION['user_id'];

// お気に入り状況取得（外部化で不具合発生のためこのまま）
try {
    global $DB_NAME, $HOST_NAME, $USER_NAME, $PASSWORD;
    $dbh = dbConnect($DB_NAME, $HOST_NAME, $USER_NAME, $PASSWORD);
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
    global $DB_NAME, $HOST_NAME, $USER_NAME, $PASSWORD;
    $dbh = dbConnect($DB_NAME, $HOST_NAME, $USER_NAME, $PASSWORD);
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

// ユーザー名取得
$userNameData = getUserName($user_id);

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
                        <p>
                            <?php echo $userNameData[0]['user_name']; ?>
                        </p>
                    </el-card>

                    <div class="songsSearch__tab">
                        <el-tabs type="border-card">
                            <el-tab-pane label="いいね一覧">
                                <?php if ($countGoodResult) :?>
                                    <div class="songsSearch__goodList">
                                        <?php for ($i = 0; $i <= $countGoodResult - 1; $i ++) :?>
                                        <div class="songsSearch__goodImg">
                                            <img src="<?php echo $userGoodData[$i]['img_url']; ?>" alt="">
                                        </div>
                                        <p class="songsSearch__userGoodList">
                                            <a href="<?php echo $userGoodData[$i]['musician_url']; ?>">
                                                <?php echo $userGoodData[$i]['musician_name']; ?>
                                            </a>
                                        </p>
                                        <el-divider></el-divider>
                                        <?php endfor; ?>
                                    </div>
                                <?php else : ?>
                                <p class="songsSearch__tabInfo">現在、いいねはありません。</p>
                                <?php endif; ?>
                            </el-tab-pane>

                            <el-tab-pane label="レビュー一覧">
                                <?php if ($countReviewResult) :?>
                                <div class="songsSearch__reviewList">
                                    <?php for ($i = 0; $i <= $countReviewResult - 1; $i ++) :?>
                                    <div class="songsSearch__reviewImg">
                                        <img src="<?php echo $userReviewData[$i]['img_url']; ?>" alt="">
                                    </div>
                                    <p class="songsSearch__userReviewList">
                                        <a href="<?php echo $userReviewData[$i]['musician_url']; ?>">
                                            <?php echo $userReviewData[$i]['musician_name']; ?>
                                        </a>
                                    </p>
                                    <p class="songsSearch__userReviewList">
                                        <?php echo $userReviewData[$i]['comment_contents']; ?>
                                    </p>
                                    <el-divider></el-divider>
                                    <?php endfor; ?>
                                </div>
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
