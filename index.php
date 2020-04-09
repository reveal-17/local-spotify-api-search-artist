<?php
require('spotify.php');
// TODO: 関数外部化できていない
require('function.php');

// アーティスト情報取得
// TODO: 空欄はエラーなのでバリデーションチェックする
if (!empty($_POST['artistName'])) {
    $artistName = $_POST['artistName'];
    // TODO: アーティストが複数該当したら選択できるようにしたい
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
} else {

}
?>

<html>
    <head>
        <meta charset="utf-8" />
        <!-- import CSS -->
        <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
        <link rel="stylesheet" href="css/style.css">
    </head>

    <body>
        <div id="app">
            <div class="songsSearch__background">
                <div class="songsSearch__form">
                    <div class="songsSearch__formContents">
                        <h1 class="songsSearch__title">好きな音楽を見つけよう。</h1>
                        <p class="songsSearch__description">好きなアーティスト名を入力すると、あなたにピッタリの楽曲が表示されます。</p>
                        <form class="songsSearch__formCentering" action="index.php" method="post">
                            <el-input class="songsSearch__formInput" type="text" name="artistName" placeholder="アーティスト名を入力" v-model="input"></el-input>
                            <el-row class="songsSearch__formSubmit">
                                <el-button native-type="submit" icon="el-icon-search" circle></el-button>
                            </el-row>

                        </form>
                    </div>
                </div>
            </div>

            <div class="songsSearch__list">
                <h1 class="songsSearch__title">あなたにおすすめ。</h1>
                <p class="songsSearch__description"><?php echo $artistData["name"]; ?>が好きなあなたへ。</p>
                <div class="songsSearch__inputImage" v-for="fit in fits" :key="fit">
                    <div class="songsSearch__inputImageBlock">
                        <el-image
                            style="width: 250px; height: 250px"
                            src="<?php echo $artistData["image"]; ?>"
                            :fit="fit"></el-image>
                    </div>
                </div>

                <div class="songsSearch__artwork" v-for="fit in fits" :key="fit">
                    <?php for ($i = 0; $i <= $countNum - 1; $i++) : ?>
                    <div class="songsSearch__artworkBlock">
                        <el-image
                            style=""
                            src="<?php echo $topTracksSelect[$i]['album_image']; ?>"
                            :fit="fit"></el-image>
                            <div class="songsSearch__artworkMask">
                                <h2 class="songsSearch__artworkTitle">
                                    <?php echo $topTracksSelect[$i]['album_name']; ?>
                                </h2>
                                <h3 class="songsSearch__artworkArtist">
                                    <?php echo $topTracksSelect[$i]['artist_name']; ?>
                                </h3>
                                <div class="songsSearch__artworkListenNow">
                                    今すぐ聞く
                                </div>
                            </div>

                        <!-- <el-image>
                            <div slot="error" class="image-slot">
                                <i class="el-icon-picture-outline"></i>
                            </div>
                        </el-image> -->
                    </div>
                    <?php endfor; ?>
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
                fits: ['cover'],
            },
            methods: {
                open4() {
                    this.$notify.error({
                    title: 'Error',
                    message: 'This is an error message'
                    });
                }
            },
            mounted(){
                //表示後にやりたいことはここに書ける
            }
        })
        </script>
    </body>
</html>
