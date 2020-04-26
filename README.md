# spotify-api-search-for-artist
https://spotify-api-search-artist-2.herokuapp.com/

## アプリケーション概要・機能
Spotify Web APIを利用して、適当なアーティストを入力するとその関連アーティストが表示されます。
加えて、ワンクリックでSpotifyへと遷移することができ、すぐに該当アーティストの楽曲を聞くことが可能です。

PC版ページのみお気に入り機能とレビュー機能を追加しました。

## アプリケーション技術一覧
- 使用言語：HTML, SCSS, Vue.js, PHP
- Spotify Web APIを使用
- Spotify Web APIのPHP用ライブラリを使用
- UIにはElement UIを使用
- ユーザーエージェントによるPC,SPページの振り分け
- githubとherokuを連携してデプロイ
- APIキーについてはherokuの環境変数を参照する形で取得
- トップページの画像はsquooshにより圧縮済み
- 検索欄が空欄の際にはエラーメッセージ表示

- PC版のみ、新規登録・ログイン・ログアウト・マイページ・お気に入り・レビュー機能を実装しています。
- ログイン時にはお気に入り機能とレビュー機能の操作が可能です。

- 随時、退会・マイページ編集・レビュー削除機能などを盛り込んでいきます。
