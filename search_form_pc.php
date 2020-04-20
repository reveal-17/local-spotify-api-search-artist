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
