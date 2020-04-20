<?php
// switch切り替えパラメータ
$hoge = true;

// セッションでレビューページに変数渡す
session_start();

// TODO: 公開前に0にする
ini_set('display_errors', 1);

// 関数読み込み
require('function.php');

// spotify web api 使用
require('spotify.php');

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

                <!-- 検索ページ -->
                <?php require('search_form_pc.php'); ?>

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
                    <?php require('search_result_pc.php'); ?>
                <?php endif; ?>

                <!-- フッター -->
                <?php require('footer_pc.php'); ?>
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
                <?php if ($hoge) : ?>
                value1: false,
                <?php else : ?>
                value1: true,
                <?php endif; ?>
                textarea: '',
            },
        });
        </script>
    </body>
</html>
