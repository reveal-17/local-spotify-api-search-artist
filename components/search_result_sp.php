            <?php if ($artistData !== NULL): ?>
                <div class="songsSearch__list">
                    <h1 class="songsSearch__title">あなたにおすすめ。</h1>
                    <p class="songsSearch__description"><?php echo $artistData["artist_name"]; ?>が好きなあなたへ。</p>

                    <!-- 入力したアーティストの名前表示 -->
                    <div class="songsSearch__inputImage">
                        <div class="songsSearch__inputImageBlock">
                            <?php if (empty($artistData["image"])): ?>
                            <el-image></el-image>
                            <?php else: ?>
                            <el-image src="<?php echo $artistData["image"]; ?>"></el-image>
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
                            <a href="<?php echo $topTracksSelect[$i]['album_url']; ?>">
                                <el-image src="<?php echo $topTracksSelect[$i]['album_image']; ?>"></el-image>
                            </a>
                        </div>
                        <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                </div>