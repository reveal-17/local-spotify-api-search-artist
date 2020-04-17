<?php
// TODO: 公開前に0にする
ini_set('display_errors', 0);

// spotify web api 使用
require('spotify.php');

// 関数読み込み
require('function.php');

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
    <head>
        <meta charset="utf-8" />
        <!-- import CSS -->
        <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
        <link rel="stylesheet" href="css/style_pc.css">
        <!-- fontawesome -->
        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    </head>

    <body>
        <div id="app">
            <div class="songsSearch">
                <div class="songsSearch__header">
                    <div class="songsSearch__header--contents">
                        <div class="songsSearch__logo">
                            <div class="songsSearch__logo--contents">
                                <div class="songsSearch__logo--link">
                                    <a href="index.php">
                                        <i class="fas fa-clone songsSearch__logoIcon"></i>
                                        <div class="songsSearch__logoTheme">Songs</div>
                                    </a>
                                </div>

                                <div class="songsSearch__nav">
                                    テスト版
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="songsSearch__background">
                    <div class="songsSearch__form">
                        <div class="songsSearch__formContents">
                            <h1 class="songsSearch__title">好きな音楽を見つけよう。</h1>
                            <p class="songsSearch__description">好きなアーティスト名を入力すると、あなたにピッタリの楽曲が表示されます。</p>
                            <form class="songsSearch__formCentering" action="index.php" method="post">
                                <el-input class="songsSearch__formInput" type="text" name="artistName"
                                    placeholder="アーティスト名を入力" v-model="input"></el-input>
                                <el-row class="songsSearch__formSubmit">
                                    <el-button native-type="submit" icon="el-icon-search" circle name="submit"></el-button>
                                </el-row>
                            </form>
                        </div>
                    </div>
                </div>

                <?php if ($_POST['submit'] === NULL): ?>
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
                <?php else: ?>
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
                <?php endif; ?>

                <el-divider></el-divider>

                <div class="songsSearch__review">
                    <p class="songsSearch__description"><?php echo $artistData["artist_name"]; ?>がみなさんに。</p>

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
                                    <div>
                                        hogehoge
                                    </div>
                                </div>

                                <div class="songsSearch__textarea">
                                    <el-input
                                    type="textarea"
                                    placeholder="Please input"
                                    v-model="textarea"
                                    maxlength="30"
                                    show-word-limit
                                    >
                                    </el-input>
                                </div>
                            </el-tab-pane>

                            <el-tab-pane label="あなただけのコメント">
                                <p class="songsSearch__letsComment">
                                    <?php echo $artistData["artist_name"]; ?>について、自分だけのコメントを書き残してみましょう。
                                </p>

                                <div class="songsSearch__chatSpace">
                                    <div>
                                        hogehoge
                                    </div>
                                </div>

                                <div class="songsSearch__textarea">
                                    <el-input
                                    type="textarea"
                                    placeholder="Please input"
                                    v-model="textarea"
                                    maxlength="30"
                                    show-word-limit
                                    >
                                    </el-input>
                                </div>
                            </el-tab-pane>
                        </el-tabs>
                    </div>
                </div>


                <div class="songsSearch__footer">
                    <a href="https://open.spotify.com/artist/5SffLdCBw5A1pGMiMCCYeb">©Albem</a>
                </div>
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
        });
        </script>
    </body>
</html>
