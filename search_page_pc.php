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

                <!-- 検索ページ -->
                <?php require('search_form_pc.php'); ?>


                <!-- 以下別ファイルに記述＆submit後、header関数で別ファイルを表示させるようにする -->

                <!-- 検索してもヒットしない＆＆検索ボタンを押している -->
                <!-- <?php if ($artistData === NULL && $_POST['submit'] === ""): ?>

                <?php endif; ?> -->

                <!-- <?php var_dump($artistData); ?>
                <?php var_dump($_POST['submit']); ?> -->

                <?php if ($artistData !== NULL): ?>
                <div class="songsSearch__list">
                    <h1 class="songsSearch__title">あなたにおすすめ。</h1>
                    <p class="songsSearch__description"><?php echo $artistData["artist_name"]; ?>が好きなあなたへ。</p>

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
                            <el-switch v-model="value1" active-color="#13ce66">
                        </el-switch>
                    </div>

                    <!-- 入力したアーティストの関連アーティストの画像を表示  -->
                    <div class="songsSearch__artwork">
                        <?php for ($i = 0; $i <= $countNum - 1; $i++) : ?>
                        <?php if (empty($topTracksSelect[$i]['album_image'])): ?>
                        <div class="songsSearch__artworkBlock">
                            <el-image style="width: 350px; height: 350px;"></el-image>
                            <div class="songsSearch__artworkMask">
                                <h2 class="songsSearch__artworkError">
                                    該当なし
                                </h2>
                            </div>
                        </div>

                        <?php else: ?>
                        <div class="songsSearch__artworkBlock">
                            <el-image src="<?php echo $topTracksSelect[$i]['album_image']; ?>"></el-image>

                            <div class="songsSearch__artworkMask">
                                <h2 class="songsSearch__artworkTitle">
                                    <a href="<?php echo $topTracksSelect[$i]['album_url']; ?>"><?php echo $topTracksSelect[$i]['album_name']; ?></a>
                                </h2>
                                <h3 class="songsSearch__artworkArtist">
                                    <a href="<?php echo $topTracksSelect[$i]['artist_url']; ?>"><?php echo $topTracksSelect[$i]['artist_name']; ?></a>
                                </h3>
                                <div class="songsSearch__artworkListenNow">
                                    <a href="<?php echo $topTracksSelect[$i]['track_url']; ?>">今すぐ聞く</a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                </div>





                <!-- アーティストがヒットしている＆アーティストを検索している -->
                <?php if ($artistData !== NULL && $_POST['submit'] === ""): ?>

                <el-divider></el-divider>

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
                            <el-tab-pane label="みなさんへのコメント">
                                <p class="songsSearch__letsComment">
                                    <?php echo $artistData["artist_name"]; ?>について、最初にコメントしてみましょう。
                                </p>

                                <div class="songsSearch__chatSpace">
                                    <div class="songsSearch__chatPost">
                                        <el-image class="songsSearch__userImg"></el-image>
                                        <p class="songsSearch__userComment">
                                            hogehoge
                                            <?php echo $_POST['public_comment']; ?>
                                        </p>
                                        <div style="clear:left;"></div>
                                    </div>
                                </div>

                                <div class="songsSearch__reviewForm">
                                    <form action="index.php" method="post">
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

                            <el-tab-pane label="あなただけのコメント">
                                <p class="songsSearch__letsComment">
                                    <?php echo $artistData["artist_name"]; ?>について、自分だけのコメントを書き残してみましょう。
                                </p>

                                <div class="songsSearch__chatSpace">
                                    <div class="songsSearch__chatPost">
                                        <el-image class="songsSearch__userImg"></el-image>
                                        hugehuge
                                        <?php echo $_POST['private_comment']; ?>
                                    </div>
                                </div>

                                <div class="songsSearch__reviewForm">
                                    <form action="index.php" method="post">
                                        <el-input
                                        name="private_comment"
                                        type="textarea"
                                        placeholder="Please input"
                                        v-model="textarea"
                                        maxlength="140"
                                        show-word-limit
                                        >
                                        </el-input>

                                        <el-row class="">
                                            <el-button name="private_comment_submit" native-type="submit" type="success"
                                            icon="el-icon-check" circle plain></el-button>
                                        </el-row>
                                    </form>
                                </div>
                            </el-tab-pane>
                        </el-tabs>
                    </div>
                <?php endif;?>

                </div>
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
