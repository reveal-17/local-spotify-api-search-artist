<?php
require('spotify.php');

    // アーティスト情報取得
    // TODO: 空欄はエラーなのでバリデーションチェックする
    if (isset($_POST['artistName'])) {

        $artistName = $_POST['artistName'];
        // TODO: アーティストが複数該当したら選択できるようにしたい
        $artistInfo = $api->search($artistName, 'artist', array('limit' => 1));
        if (isset($artistInfo->artists->items)) {
            foreach ($artistInfo->artists->items as $data) {
                $artistData = array(
                    'id' => $data->id,
                    'name' => $data->name,
                    // TODO: イメージが複数あるならランダムで表示されるようにしてもいいかも
                    'image' => $data->images[0]->url,
                    'artist_url' => $data->artist[0]->external_urls,
                );
            }
        } else {
            // $artistInfo->artists->items がないとき
            return false;
            // TODO: エラーメッセージ出す？
        }

        // 関連アーティスト取得
        $relatedArtist = $api->getArtistRelatedArtists($artistData['id'])->artists;
        // var_dump($relatedArtist);
        $relatedArtistSelect = array();
        $countNum = 6;
        if (count($relatedArtist) >= $countNum) {
            $selectionNum = $countNum;
        } else {
            $selectionNum = count($relatedArtist);
        }
        for ($i = 0; $i <= $selectionNum - 1; $i++) {
            $relatedArtistData = array(
                'id' => $relatedArtist[$i]->id,
                'name' => $relatedArtist[$i]->name,
                'images' => $relatedArtist[$i]->images[0]->url
            );
            // 関連アーティストを最大9つ取得
            array_push($relatedArtistSelect, $relatedArtistData);
        }
        // var_dump($relatedArtistSelect);


        // 関連アーティストのトップトラック取得
        $topTracksSelect = array();
        foreach ($relatedArtistSelect as $data) {
            $topTracks = $api->getArtistTopTracks($data['id'], array('country' => 'JP'))->tracks;
            $topTracksData = array(
                'track_id' => $topTracks[0]->id,
                'artist_name' => $topTracks[0]->artists[0]->name,
                'album_name' => $topTracks[0]->album->name,
                'album_image' => $topTracks[0]->album->images[0]->url
            );
            if (isset($topTracks)) {
                // 関連アーティスト各々に対してトップトラックを取得
                array_push($topTracksSelect, $topTracksData);
            }
        }
        // var_dump($topTracksData);
        // var_dump($topTracksSelect);

        // アーティストのアルバム取得
        $relatedArtistAlbum = $api->getArtistAlbums($artistData['id'], array('country' => 'JP'))->items;
        // 取得するアルバムは人気があるものとは限らない
        $relatedArtistTopAlbum = array(
            'artist_url' => $relatedArtistAlbum[0]->artists[0]->external_urls->spotify,
            'artist_name' => $relatedArtistAlbum[0]->artists[0]->external_urls->name,
            'album_image' => $relatedArtistAlbum[0]->images[0]->url,
            'album_name' => $relatedArtistAlbum[0]->name
        );
        // var_dump($relatedArtistTopAlbum);
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
                            <el-input class="hoge" type="text" name="artistName" placeholder="アーティスト名を入力" v-model="input"></el-input>
                            <el-row class="huga">
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
                    <div class="songsSearch__inputImageBlock" style="width: 250px; display:inline-block;">
                        <el-image
                            style="width: 250px; height: 250px"
                            src="<?php echo $artistData["image"]; ?>"
                            :fit="fit"></el-image>
                    </div>
                </div>

                <div class="songsSearch__artwork" v-for="fit in fits" :key="fit">
                    <?php for ($i = 0; $i <= $countNum - 1; $i++) : ?>
                    <div class="songsSearch__artworkBlock" style="width: 370px; height: 370px; display: inline-block;">
                        <el-image
                            style="width: 370px; height: 370px"
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
                sayHello(){
                this.msg = "Hello!"
                }
            },
            mounted(){
                //表示後にやりたいことはここに書ける
            }
        })
        </script>
    </body>
</html>
