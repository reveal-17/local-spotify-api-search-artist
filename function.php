<?php

// -----------------------------------------------------------DB接続-----------------------------------------------------------

function dbConnect() {
    //DBへの接続準備
    $dsn = 'mysql:dbname=mysql_database;host=mysql;charset=utf8';
    $user = 'mysql_user';
    $password = 'mysql_pw';
    $options = array(
    // SQL実行失敗時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
    // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    // PDOオブジェクト生成（DBへ接続）
    $dbh = new PDO($dsn, $user, $password, $options);
    return $dbh;
}

function queryPost($dbh,$sql,$data) {
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    //プレースホルダに値をセットし、SQL文を実行
    return $stmt;
}

// -----------------------------------------------------------spotify web api-----------------------------------------------------------

function artistSearch($artistName) {
    global $api;
    try {
        $artistInfo = $api->search($artistName, 'artist', array('limit' => 1));
        if (isset($artistInfo->artists->items)) {
            foreach ($artistInfo->artists->items as $data) {
                $artistData = array(
                    'id' => $data->id,
                    'artist_name' => $data->name,
                    // TODO: イメージが複数あるならランダムで表示されるようにしてもいいかも
                    'image' => $data->images[0]->url,
                    'artist_url' => $data->external_urls->spotify,
                );
            }
            // レビューページで使用するためにセッションに格納
            $_SESSION['artist_id'] = $artistData['id'];
            $_SESSION['artist_name'] = $artistData['artist_name'];
            $_SESSION['image'] = $artistData['image'];
            $_SESSION['artist_url'] = $artistData['artist_url'];
            return $artistData;
        } else {
            // $artistInfo->artists->items がないとき
            return false;
            // TODO: エラーメッセージ出す？
        }
    } catch (Exception $e) {
        return false;
    }
}

function relatedArtistSearch($artistId) {
    global $api;
    try {
        $relatedArtist = $api->getArtistRelatedArtists($artistId)->artists;
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
        return $relatedArtistSelect;
    } catch (Exception $e) {
        return false;
    }
}

function relatedArtistTopTracks($relatedArtistSelect) {
    global $api;
    try {
        $topTracksSelect = array();
        foreach ($relatedArtistSelect as $data) {
            $topTracks = $api->getArtistTopTracks($data['id'], array('country' => 'JP'))->tracks;
            $topTracksData = array(
                'track_id' => $topTracks[0]->id,
                'track_url' => $topTracks[0]->external_urls->spotify,
                'artist_name' => $topTracks[0]->artists[0]->name,
                'artist_url' => $topTracks[0]->artists[0]->external_urls->spotify,
                'album_name' => $topTracks[0]->album->name,
                'album_image' => $topTracks[0]->album->images[0]->url,
                'album_url' => $topTracks[0]->album->external_urls->spotify,
            );
            if (isset($topTracks)) {
                // 関連アーティスト各々に対してトップトラックを取得
                array_push($topTracksSelect, $topTracksData);
            }
        }
        return $topTracksSelect;
    } catch (Exception $e) {
        return false;
    }
}

function relatedArtistTopAlbum($artistId) {
    global $api;
    try {
        $relatedArtistAlbum = $api->getArtistAlbums($artistId, array('country' => 'JP'))->items;
        // 取得するアルバムは人気があるものとは限らない
        $relatedArtistTopAlbum = array(
            'artist_url' => $relatedArtistAlbum[0]->artists[0]->external_urls->spotify,
            'artist_name' => $relatedArtistAlbum[0]->artists[0]->external_urls->name,
            'album_image' => $relatedArtistAlbum[0]->images[0]->url,
            'album_name' => $relatedArtistAlbum[0]->name
        );
        return $relatedArtistTopAlbum;
    } catch (Exception $e) {
        return false;
    }
}

function registerReview($comment, $id, $name) {
    if ($_POST['public_comment_submit'] === "" && $_POST['public_comment'] !== NULL) {
        try {
            $dbh = dbConnect();
            $sql = "INSERT INTO public_comment (comment_contents, musician_id, musician_name, create_time) VALUES (:comment_contents, :musician_id, :musician_name, :create_time)";
            $data = array(':comment_contents' => $comment, ':musician_id' => $id, ':musician_name' => $name, ':create_time' => date("Y/m/d H:i:s"));
            $stmt = queryPost($dbh, $sql, $data);
            if ($stmt) {
                // 成功したらsuccessメッセージ
                return true;
            } else {
                // 失敗したらerrorメッセージ
                return false;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
