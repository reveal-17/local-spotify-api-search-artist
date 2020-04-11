<?php
function artistSearch($artistName) {
    global $api;
    try {
        if ($artistName === "") {
            throw new Exception('エラーが発生しました。');
        }
        $artistInfo = $api->search($artistName, 'artist', array('limit' => 1));
        if (isset($artistInfo->artists->items)) {
            foreach ($artistInfo->artists->items as $data) {
                $artistData = array(
                    'id' => $data->id,
                    'artist_name' => $data->name,
                    // TODO: イメージが複数あるならランダムで表示されるようにしてもいいかも
                    'image' => $data->images[0]->url,
                    'artist_url' => $data->artist[0]->external_urls,
                );
            }
            return $artistData;
        } else {
            // $artistInfo->artists->items がないとき
            return false;
            // TODO: エラーメッセージ出す？
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function relatedArtistSearch($artistId) {
    global $api;
    try {
        if ($artistName === "") {
            throw new Exception('エラーが発生しました。');
        }

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
        echo $e->getMessage();
    }
}

function relatedArtistTopTracks($relatedArtistSelect) {
    global $api;
    try {
        if ($artistName === "" || $relatedArtistSelect === NULL) {
            throw new Exception('エラーが発生しました。');
        }

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
        echo $e->getMessage();
    }
}

function relatedArtistTopAlbum($artistId) {
    global $api;
    try {
        if ($artistName === "") {
            throw new Exception('エラーが発生しました。');
        }

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
        echo $e->getMessage();
    }
}
