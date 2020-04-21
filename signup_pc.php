<html>
    <!-- ヘッドタグ -->
    <?php require('components/head_pc.php'); ?>

    <body>
        <div id="app">
            <div class="songsSearch">
                <!-- ヘッダー -->
                <?php require('components/header_pc.php'); ?>

                <div class="songsSearch__signup">
                    <div class="songsSearch__signupLabel">
                        新規登録
                    </div>
                    <!-- email -->
                    <el-form :model="dynamicValidateForm" status-icon ref="dynamicValidateForm" label-width="120px" class="demo-dynamic">
                    <el-form-item
                            prop="email"
                            label="メールアドレス"
                            :rules="[
                            { required: true, message: '登録するメールアドレスを入力してください。', trigger: 'blur' },
                            { type: 'email', message: '正しいメールアドレスを入力してください。', trigger: ['blur', 'change'] }
                            ]"
                        >
                        <el-input v-model="dynamicValidateForm.email"></el-input>
                    </el-form-item>

                    <!-- password -->
                    <el-form :model="ruleForm" status-icon :rules="rules" ref="ruleForm" label-width="120px" class="demo-ruleForm">
                    <el-form-item label="パスワード" prop="pass" required>
                        <el-input type="password" v-model="ruleForm.pass" autocomplete="off"></el-input>
                    </el-form-item>

                    <!-- password(retype) -->
                    <el-form-item label="パスワード（再入力）" prop="checkPass" required>
                        <el-input type="password" v-model="ruleForm.checkPass" autocomplete="off"></el-input>
                    </el-form-item>

                    <!-- submit -->
                    <el-form-item class="songsSearch__signupSubmit">
                        <el-button name="signup_submit" native-type="submit" type="primary" @click="submitForm('ruleForm'); submitForm('dynamicValidateForm');">登録</el-button>
                    </el-form-item>
                    </el-form>
                </div>


                <!-- フッター -->
                <?php require('components/footer_pc.php'); ?>
            </div>
        </div>

        <!-- import Vue before Element -->
        <script src="https://unpkg.com/vue/dist/vue.js"></script>
        <!-- import JavaScript -->
        <script src="https://unpkg.com/element-ui/lib/index.js"></script>
        <!-- import axios before Element -->
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

        <script>
        new Vue({
            el: "#app",
            data() {
                var validatePass = (rule, value, callback) => {
                    if (value === '') {
                    callback(new Error('登録するパスワードを入力してください。'));
                    } else {
                    if (this.ruleForm.checkPass !== '') {
                        this.$refs.ruleForm.validateField('checkPass');
                    }
                    callback();
                    }
                };
                var validatePass2 = (rule, value, callback) => {
                    if (value === '') {
                    callback(new Error('登録するパスワードをもう一度入力してください。'));
                    } else if (value !== this.ruleForm.pass) {
                    callback(new Error('同じパスワードを入力してください。'));
                    } else {
                    callback();
                    }
                };
                return {
                    // email
                    dynamicValidateForm: {
                    email: ''
                    },
                    // password
                    ruleForm: {
                        pass: '',
                        checkPass: '',
                    },
                    rules: {
                        pass: [
                            { validator: validatePass, trigger: 'blur' }
                        ],
                        checkPass: [
                            { validator: validatePass2, trigger: 'blur' }
                        ],
                    }
                };
            },
            methods: {
                submitForm(formName) {
                    this.$refs[formName].validate((valid) => {
                        if (valid) {
                            alert('submit!');
                        } else {
                            console.log('error submit!!');
                            return false;
                        }
                    });
                },
            },
        });
        </script>
    </body>
</html>
