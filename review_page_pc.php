<?php
// セッションでレビューページに変数渡す
session_start();

// TODO: 公開前に0にする
ini_set('display_errors', 1);

// spotify web api 使用
require('spotify.php');

// 関数読み込み
require('function.php');

// セッションを変数に格納
$artistData['id'] = $_SESSION['artist_id'];
$artistData['artist_name'] = $_SESSION['artist_name'];
$artistData['image'] = $_SESSION['image'];
$artistData['artist_url'] = $_SESSION['artist_url'];
var_dump($artistData);

// レビューをDBに登録
registerReview($_POST['public_comment'], $artistData['id'], $artistData['artist_name']);

// 今までに投稿されたレビューを表示
try {
    $dbh = dbConnect();
    $sql = "SELECT comment_contents FROM public_comment WHERE musician_id = '${artistData['id']}'";
    $data = array();
    $stmt = queryPost($dbh, $sql, $data);
    $reviewData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // レビューをいくつ表示するか
    $reviewNum = count($reviewData);
} catch (Exception $e) {
    echo $e->getMessage();
}

// TODO: コメントの多重投稿は、user_idとテキスト内容が一致するデータがある場合、投稿できないようにする。
// か、そのつど＄＿POST['public_comment']をなくす←こっちのほうがいい？

// TODO: レビューを新しい順に取得する

// TODO: レビューをページネーション的に動かすor「もっと見る」で表示させる

// TODO: レビュー投稿成功or失敗は以下のようにするのがよい？
// if ($isSuccess === true) {
//     echo '<div>サクセスメッセージ</div>'
// } else {
//     echo '<div>エラーメッセージ</div>'
// }

?>

<html>
    <!-- ヘッドタグ -->
    <?php require('head_pc.php'); ?>

    <body>
        <div id="app">
            <div class="songsSearch">
                <!-- ヘッダー -->
                <?php require('header_pc.php'); ?>

                <div class="songsSearch__review">
                    <p class="songsSearch__description"><?php echo $artistData["artist_name"]; ?>をみなさんに。</p>

                    <!-- 入力したアーティストの名前表示 -->
                    <div class="songsSearch__inputImage">
                        <div class="songsSearch__inputImageBlock">
                            <?php if (empty($artistData["image"])): ?>
                            <el-image style="width: 250px; height: 250px;"></el-image>
                            <?php else: ?>
                            <el-image style="width: 250px; height: 250px;" src="<?php echo $artistData["image"]; ?>"></el-image>
                            <?php endif; ?>

                            <?php if (empty($artistData["artist_name"])): ?>
                            <div class="songsSearch__inputImageMask">
                                <h2 class="songsSearch__artworkError--inputImage">
                                    該当なし
                                </h2>
                            </div>
                            <?php else: ?>
                            <div class="songsSearch__inputImageMask">
                                <h3 class="songsSearch__artworkArtist--inputImage"><a href="<?php echo $artistData["artist_url"]; ?>"><?php echo $artistData["artist_name"]; ?></a></h3>
                                <div class="songsSearch__artworkListenNow"><a href="<?php echo $artistData["artist_url"]; ?>">今すぐ聴く</a></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="songsSearch__tab">
                        <el-tabs type="border-card">
                            <el-tab-pane label="みなさんのコメント">
                                <p class="songsSearch__letsComment">
                                    <?php echo $artistData["artist_name"]; ?>について、コメントしてみましょう。
                                </p>

                                <div class="songsSearch__chatSpace">
                                    <!-- レビューが書かれていないとき -->
                                    <?php if (count($reviewData) === 0) :?>
                                    <div class="songsSearch__chatPost">
                                        <p class="songsSearch__letsFirstComment">
                                            あなたが最初のレビュワーとなりませんか？
                                        </p>
                                        <p class="songsSearch__letsFirstComment">
                                            おすすめの楽曲や関連アーティストについてもシェアしてみましょう。
                                        </p>
                                    </div>
                                    <?php else: ?>
                                    <!-- レビューが一件以上あるとき、レビューを一覧取得 -->
                                    <?php for ($i = 0; $i <= $reviewNum; $i++) :?>
                                    <div class="songsSearch__chatPost">
                                        <el-image class="songsSearch__userImg"></el-image>
                                        <p class="songsSearch__userComment">
                                            <?php echo $reviewData[$i]['comment_contents']; ?>
                                        </p>
                                        <el-divider></el-divider>
                                        <?php endfor; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="songsSearch__reviewForm">
                                    <form action="review_page_pc.php" method="post">
                                        <el-input
                                        name="public_comment"
                                        type="textarea"
                                        placeholder="Please input"
                                        v-model="textarea"
                                        maxlength="140"
                                        show-word-limit
                                        >
                                        </el-input>

                                        <el-row class="songsSearch__reviewSubmit">
                                            <el-button name="public_comment_submit" native-type="submit"
                                            type="success" icon="el-icon-check" circle plain></el-button>
                                        </el-row>
                                    </form>
                                </div>
                            </el-tab-pane>
                        </el-tabs>
                    </div>
                </div>
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
                input: '',
                value1: false,
                textarea: '',
            },
        });
        </script>
    </body>
</html>
