<?php
// -----------------------------------------------------------SESSION-----------------------------------------------------------
session_save_path('/var/tmp');
ini_set('session.gc_maxlifetime',60*60*24*30);
ini_set('session.cookie_ifetime',60*60*24*30);
// セッションでレビューページに変数渡す
session_start();
session_regenerate_id();
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

function registerReview($comment, $id, $name, $musician_url, $img_url, $user_id) {
    if ($_POST['public_comment_submit'] === "" && $_POST['public_comment'] !== NULL) {
        try {
            $dbh = dbConnect();
            $sql = "INSERT INTO public_comment (comment_contents, musician_id, musician_name, musician_url, img_url, user_id, create_time) VALUES (:comment_contents, :musician_id, :musician_name, :musician_url, :img_url, :user_id, :create_time)";
            $data = array(':comment_contents' => $comment, ':musician_id' => $id, ':musician_name' => $name, ':musician_url' => $musician_url, ':img_url' => $img_url, ':user_id' => $user_id, ':create_time' => date("Y/m/d H:i:s"));
            $stmt = queryPost($dbh, $sql, $data);
            if ($stmt) {
                // 成功したらsuccessメッセージ
            } else {
                // 失敗したらerrorメッセージ
                return false;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

function getReview($musician_id) {
    try {
        $dbh = dbConnect();
        $sql = "SELECT comment_contents FROM public_comment WHERE musician_id = '${musician_id}'";
        $data = array();
        $stmt = queryPost($dbh, $sql, $data);
        $reviewData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $reviewData;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function registerGood($musician_id, $musician_name, $musician_url, $img_url, $user_id, $is_active) {
    if ($is_active === true) {
        try {
            $dbh = dbConnect();
            $sql = "SELECT * FROM favorite WHERE musician_id = :musician_id";
            $data = array(":musician_id" => "${musician_id}");
            $stmt = queryPost($dbh, $sql, $data);
            $selectResult = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($selectResult === false) {
                // DBにお気に入りデータがなかったとき（新たにDBにお気に入りとして登録）
                $sql = "INSERT INTO favorite (musician_id, musician_name, musician_url, img_url, user_id, is_favorite) VALUE (:musician_id, :musician_name, :musician_url, :img_url, :user_id, true)";
                $data = array(':musician_id' => $musician_id, ':musician_name' => $musician_name, ':musician_url' => $musician_url, ':img_url' => $img_url, ':user_id' => $user_id);
                $stmt = queryPost($dbh, $sql, $data);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

function deleteGood($musician_id, $is_active) {
    if ($is_active === false) {
        try {
            $dbh = dbConnect();
            $sql = "SELECT * FROM favorite WHERE musician_id = :musician_id";
            $data = array(":musician_id" => "${musician_id}");
            $stmt = queryPost($dbh, $sql, $data);
            $selectResult = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($selectResult !== false) {
                // お気に入り登録をDBから削除
                $sql = "DELETE FROM favorite WHERE musician_id = '${musician_id}' AND is_favorite = true";
                $stmt = queryPost($dbh, $sql, $data);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

function getCountGood($musician_id) {
    // TODO: limitで表示件数制御
    try {
        $dbh = dbConnect();
        $sql = "SELECT is_favorite FROM favorite WHERE musician_id = :musician_id";
        $data = array(":musician_id" => "${musician_id}");
        $stmt = queryPost($dbh, $sql, $data);
        $countResult = $stmt->rowCount();
        return $countResult;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function getUserGood($user_id) {
    try {
        $dbh = dbConnect();
        $sql = "SELECT musician_id, musician_name, musician_url, img_url FROM favorite WHERE user_id= :user_id AND is_favorite = 1";
        $data = array(":user_id" => "${user_id}");
        $stmt = queryPost($dbh, $sql, $data);
        $userGoodData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $userGoodData;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function getUserReview($user_id) {
    try {
        $dbh = dbConnect();
        $sql = "SELECT comment_contents, musician_id, musician_name, musician_url, img_url FROM public_comment WHERE user_id= :user_id";
        $data = array(":user_id" => "${user_id}");
        $stmt = queryPost($dbh, $sql, $data);
        $userReviewData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $userReviewData;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function getUserName($user_id) {
    try {
        $dbh = dbConnect();
        $sql = "SELECT user_name FROM user WHERE user_id = :user_id";
        $data = array(":user_id" => "${user_id}");
        $stmt = queryPost($dbh, $sql, $data);
        $userNameData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $userNameData;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

//    =================================バリデーションチェック==================================
$error_msg = array();

const MSG1 = "必須項目です";
const MSG2 = "文字数が長すぎます。";
const MSG3 = "メールアドレスを正しく入力してください。";
const MSG4 = "すでに登録されているメールアドレスです。";
const MSG5 = "半角英数字で入力してください。";
const MSG6 = "パスワードが一致しません。";
const MSG7 = "文字数が短すぎます。";
const MSG8 = "エラーが発生しました。時間を置いてから再度お試しください。";

function validRequired($str, $key) {
    if (empty($str)) {
        global $error_msg;
        $error_msg[$key] = MSG1;
    }
}

function validMaxLen($str, $key, $max = 255) {
    if (mb_strlen($str) > $max) {
        global $error_msg;
        $error_msg[$key] = MSG2;
    }
}

function validEmail($str, $key) {
    if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$str)) {
        global $error_msg;
        $error_msg[$key] = MSG3;
    }
}

function validEmailDup($email) {
    try {
        $dbh = dbConnect();
        $sql = 'SELECT count(*) FROM user WHERE email = :email';
        $data = array(':email' => $email);
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!empty(array_shift($result))) {
            global $error_msg;
            $error_msg['email'] = MSG4;
        }
    } catch (Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
        $error_msg['common'] = MSG8;
    }
}

function validHalf($str, $key) {
    if (!preg_match("/^[a-zA-Z0-9]+$/", $str)) {
        global $error_msg;
        $error_msg[$key] = MSG5;
    }
}

function validSame($str1, $str2, $key) {
    if ($str1 !== $str2) {
        global $error_msg;
        $error_msg[$key] = MSG6;
    }
}

function validMinLen($str, $key, $min = 3) {
    if (mb_strlen($str) < $min) {
        global $error_msg;
        $error_msg[$key] = MSG7;
    }
}

function validPass($str,$key) {
    validMaxLen($str,$key);
    validMinLen($str,$key);
    validHalf($str,$key);
}

function validDiff($str1,$str2,$key) {
    if ($str1 === $str2) {
        global $error_msg;
        $error_msg[$key] = MSG10;
    }
}
