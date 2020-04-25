<?php
// TODO: 公開前に0にする
ini_set('display_errors', 0);

// 関数読み込み
require('function.php');

// spotify web api 使用
require('spotify.php');

require('auth_review_page_pc.php');
$is_login = isLogin();

// アーティスト情報取得
// TODO: 空欄はエラーなので空欄のときはアルベムといれておく
if ($_POST['artistName'] === "")  {
    $_POST['artistName'] = "アルベム";
}

// 入力されたものをサニタイズ
$artistName = htmlspecialchars($_POST['artistName'], ENT_QUOTES, "UTF-8");

$artistData = artistSearch($artistName);

// 関連アーティスト取得
$artistId = $artistData['id'];
$relatedArtistSelect = relatedArtistSearch($artistId);

// 関連アーティスト表示件数
$countNum = 6;
// 関連アーティストのトップトラック取得
$topTracksSelect = relatedArtistTopTracks($relatedArtistSelect);
// アーティストのアルバム取得
$relatedArtistAlbum = relatedArtistTopAlbum($artistId);

$request_body = file_get_contents('php://input'); //送信されてきたbodyを取得(JSON形式）
$axiosData = json_decode($request_body, true); // デコード

// セッションに格納されたアーティストIDを変数に格納
$musician_id = $_SESSION['artist_id'];

// ユーザーID
$user_id = $_SESSION['user_id'];

// お気に入り登録機能
registerGood($musician_id, $_SESSION['artist_name'], $_SESSION['artist_url'], $_SESSION['image'], $user_id, $axiosData['is_active']);

// お気に入り解除機能
deleteGood($musician_id, $axiosData['is_active']);

// お気に入り状況取得（外部化で不具合発生のためこのまま）
try {
    global $DB_NAME, $HOST_NAME, $USER_NAME, $PASSWORD;
    $dbh = dbConnect($DB_NAME, $HOST_NAME, $USER_NAME, $PASSWORD);
    $sql = "SELECT is_favorite FROM favorite WHERE musician_id = :musician_id";
    $data = array(":musician_id" => "${musician_id}");
    $stmt = queryPost($dbh, $sql, $data);
    // お気に入りあるかどうか判別（switchの初期値を動的表示）
    $countResult = $stmt->rowCount();
} catch (Exception $e) {
    echo $e->getMessage();
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

                <!-- 検索ページ -->
                <?php require('components/search_form_pc.php'); ?>

                <?php if ($_POST['submit'] === NULL) : ?>

                <!------------------------------------------------------------- 検索前のページ ------------------------------------------------------------->

                <el-row class="songsSearch__usage">
                    <el-col :span="8" v-for="(o, index) in 3" :key="o" :offset="index > 0 ? 1 : 0">
                        <el-card :body-style="{ padding: '0px' }">

                            <div class="songsSearch__card" v-if="index === 0">
                                <img src="img/introduction1.jpg" class="songsSearch__cardImage">
                                <div style="padding: 14px;">
                                    <span class="songsSearch__cardTitle">「好き」を増やそう</span>
                                    <div class="songsSearch__cardBottom songsSearch__cardClearfix">
                                        <time class="songsSearch__cardDescription">Songsを使えば、あなたの「好き」がもっと広がる。</time>
                                    </div>
                                </div>
                            </div>

                            <div class="songsSearch__card" v-if="index === 1">
                                <img src="img/introduction2.jpg" class="songsSearch__cardImage">
                                <div style="padding: 14px;">
                                    <span class="songsSearch__cardTitle">アーティスト名を入力するだけ。</span>
                                    <div class="songsSearch__cardBottom songsSearch__cardClearfix">
                                        <time class="songsSearch__cardDescription">関連するアーティストを自動で表示します。</time>
                                    </div>
                                </div>
                            </div>

                            <div class="songsSearch__card" v-if="index === 2">
                                <img src="img/introduction3.jpg" class="songsSearch__cardImage">
                                <div style="padding: 14px;">
                                    <span class="songsSearch__cardTitle">「好き」を見つけよう。</span>
                                    <div class="songsSearch__cardBottom songsSearch__cardClearfix">
                                        <time class="songsSearch__cardDescription">検索結果から、すぐにSpotifyの再生画面へ。</time>
                                    </div>
                                </div>
                            </div>
                        </el-card>
                    </el-col>
                </el-row>

                <?php else : ?>
                <!------------------------------------------------------------- 検索後のページ ------------------------------------------------------------->

                    <!-- 検索してもヒットしない＆＆検索ボタンを押している -->
                    <?php if ($artistData === NULL && $_POST['submit'] === ""): ?>
                    <el-alert
                        title="アーティストが見つかりませんでした。"
                        type="error"
                        center
                        description="別のアーティストを入力してみましょう。"
                        show-icon>
                    </el-alert>
                    <?php endif; ?>

                    <!-- 通常の状態 -->
                    <?php require('components/search_result_pc.php'); ?>
                <?php endif; ?>

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
            },
            methods: {
                isFavorite() {
                    const favoriteSwitch = document.querySelector('.js-favorite-switch .el-switch');
                    console.log(favoriteSwitch);
                    const isActive = favoriteSwitch.className;
                    console.log(isActive);
                    if (isActive === "el-switch is-checked") {
                        axios.post('index_pc.php', {
                            is_active: true,
                        })
                        .then( (response) => {
                            console.log(response);
                        }).catch( (error) => {
                            console.log(error);
                        });
                        console.log('チェック入ってます');
                    } else if (isActive === "el-switch") {
                        axios.post('index_pc.php', {
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
