<?php
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
// var_dump($artistData);

// ユーザーID
$user_id = $_SESSION['user_id'];

// レビューをDBに登録
registerReview($_POST['public_comment'], $artistData['id'], $artistData['artist_name'], $artistData['artist_url'], $artistData['image'], $user_id);

// 今までに投稿されたレビューを表示
$reviewData = getReview($artistData['id']);

// レビューをいくつ表示するか
$reviewNum = count($reviewData);

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

$request_body = file_get_contents('php://input'); //送信されてきたbodyを取得(JSON形式）
$axiosData = json_decode($request_body, true); // デコード
// json形式
// var_dump($request_body);
// データ部分だけ抽出出来た！
// var_dump($axiosData['is_active']);

// お気に入り登録機能
registerGood($artistData['id'], $artistData['artist_name'], $artistData['artist_url'], $artistData['image'], $user_id, $axiosData['is_active']);

// お気に入り解除機能
deleteGood($artistData['id'], $axiosData['is_active']);

// お気に入り状況取得（外部化で不具合発生のためこのまま）
try {
    $dbh = dbConnect();
    $sql = "SELECT is_favorite FROM favorite WHERE musician_id = :musician_id";
    $data = array(":musician_id" => "${artistData['id']}");
    $stmt = queryPost($dbh, $sql, $data);
    // お気に入りあるかどうか判別（switchの初期値を動的表示）
    $countResult = $stmt->rowCount();
} catch (Exception $e) {
    echo $e->getMessage();
}
// var_dump($countResult);

?>

<html>
    <!-- ヘッドタグ -->
    <?php require('components/head_pc.php'); ?>

    <body>
        <div id="app">
            <div class="songsSearch">
                <!-- ヘッダー -->
                <?php require('components/header_pc.php'); ?>

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

                    <!-- お気に入り登録ボタン -->
                    <div class="songsSearch__favorite">
                        <p><?php echo $artistData["artist_name"]; ?>をお気に入りに登録する。</p>
                        <div class="js-favorite-switch" @click="isFavorite">
                            <el-switch v-model="value1" active-color="#13ce66"></el-switch>
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
                                    <?php for ($i = 0; $i <= $reviewNum - 1; $i++) :?>
                                    <div class="songsSearch__chatPost">
                                        <el-avatar :size="50" :src="circleUrlDefault"></el-avatar>
                                        <p class="songsSearch__userComment">
                                            <?php echo $reviewData[$i]['comment_contents']; ?>
                                        </p>
                                        <el-divider></el-divider>
                                    </div>
                                    <?php endfor; ?>
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
                    <div style="clear: left;"></div>
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
                input: '',
                <?php if ($countResult) : ?>
                value1: true,
                <?php else : ?>
                value1: false,
                <?php endif; ?>
                textarea: '',
                circleUrlDefault: "https://cube.elemecdn.com/3/7c/3ea6beec64369c2642b92c6726f1epng.png",
            },
            methods: {
                isFavorite() {
                    const favoriteSwitch = document.querySelector('.js-favorite-switch .el-switch');
                    console.log(favoriteSwitch);
                    const isActive = favoriteSwitch.className;
                    console.log(isActive);
                    if (isActive === "el-switch is-checked") {
                        axios.post('review_page_pc.php', {
                            is_active: true,
                        })
                        .then( (response) => {
                            console.log(response);
                        }).catch( (error) => {
                            console.log(error);
                        });
                        console.log('チェック入ってます');
                    } else if (isActive === "el-switch") {
                        axios.post('review_page_pc.php', {
                            is_active: false,
                        })
                        .then( (response) => {
                            console.log(response);
                        }).catch( (error) => {
                            console.log(error);
                        });
                        console.log('チェック入ってません');
                    }
                }
            },
        });
        </script>
    </body>
</html>
