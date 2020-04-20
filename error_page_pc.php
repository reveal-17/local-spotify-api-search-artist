<?php
// TODO: 公開前に0にする
ini_set('display_errors', 1);

// spotify web api 使用
require('spotify.php');

// 関数読み込み
require('function.php');

// TODO: 後に外部関数化
if ($_POST['submit'] === "") {
    if ($artistData === NULL) {
        // エラーページへ飛ばす
        header('Location:error_page_pc.php');
    } else {
        // 検索結果ページに飛ばす
        header('Location:search_page_pc.php');
    }
}

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

?>

<html>
    <!-- ヘッドタグ -->
    <?php require('head_pc.php'); ?>

    <body>
        <div id="app">
            <div class="songsSearch">
                <!-- ヘッダー -->
                <?php require('header_pc.php'); ?>

                <!-- 検索画面 -->
                <?php require('search_form_pc.php'); ?>

                <el-alert
                    title="アーティストが見つかりませんでした。"
                    type="error"
                    center
                    description="別のアーティストを入力してみましょう。"
                    show-icon>
                </el-alert>

                <!-- フッター -->
                <?php require('footer_pc.php'); ?>
            </div>
        </div>

        <!-- import Vue before Element -->
        <script src="https://unpkg.com/vue/dist/vue.js"></script>
        <!-- import JavaScript -->
        <script src="https://unpkg.com/element-ui/lib/index.js"></script>
        <script>
        new Vue({
            el: "#app",
            data: {
                input: '',
                value1: false,
                textarea: '',
            },
            methods: {

            }
        });
        </script>
    </body>
</html>
