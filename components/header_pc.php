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
                                    <?php if (empty($_SESSION['user_id'])) :?>
                                    <span><a href="signup_pc.php" class="songsSearch__navMenu">新規登録</a></span>
                                    <el-divider direction="vertical"></el-divider>
                                    <span><a href="login_pc.php" class="songsSearch__navMenu">ログイン</a></span>
                                    <?php else: ?>
                                    <!-- <a><a href="signup_pc.php" class="songsSearch__navMenu">新規登録</a></span>
                                    <el-divider direction="vertical"></el-divider>
                                    <span><a href="login_pc.php" class="songsSearch__navMenu">ログイン</a></span> -->
                                    <el-dropdown>
                                        <el-button type="success">
                                            メニュー<i class="el-icon-arrow-down el-icon--right"></i>
                                        </el-button>
                                        <el-dropdown-menu slot="dropdown">
                                            <el-dropdown-item class="songsSearch__dropdown"><a href="index_pc.php">トップページ</a></el-dropdown-item>
                                            <el-dropdown-item class="songsSearch__dropdown"><a href="mypage_pc.php">マイページ</a></el-dropdown-item>
                                            <el-dropdown-item class="songsSearch__dropdown"><a href="logout_pc.php">ログアウト</a></el-dropdown-item>
                                            <el-dropdown-item class="songsSearch__dropdown"><a href="unsubscribe_pc.php">退会</a></el-dropdown-item>
                                        </el-dropdown-menu>
                                    </el-dropdown>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
