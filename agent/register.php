<?php
include '../includes/common.php';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>代理注册 - 二次元风格</title>
    <link rel="stylesheet" href="../assets/layui/css/layui.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Microsoft YaHei', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .register-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }
        
        .register-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }
        
        .register-header h1 {
            color: #667eea;
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .register-header p {
            color: #666;
            font-size: 14px;
        }
        
        .register-tabs {
            display: flex;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
            border-bottom: 2px solid #eee;
        }
        
        .register-tab {
            flex: 1;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            color: #666;
            font-weight: bold;
            transition: all 0.3s;
            position: relative;
        }
        
        .register-tab.active {
            color: #667eea;
        }
        
        .register-tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #667eea;
        }
        
        .register-tab:hover {
            color: #667eea;
        }
        
        .layui-form-item {
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .layui-form-label {
            width: 80px;
            color: #666;
            font-weight: bold;
        }
        
        .layui-input-block {
            margin-left: 100px;
        }
        
        .layui-input, .layui-select {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .layui-input:focus, .layui-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.2);
        }
        
        .register-btn {
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            z-index: 1;
            overflow: hidden;
        }
        
        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .register-btn:active {
            transform: translateY(0);
        }
        
        .form-hint {
            color: #999;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .level-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            display: none;
        }
        
        .level-info.show {
            display: block;
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .level-info-title {
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .level-info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 13px;
        }
        
        .back-login {
            text-align: center;
            margin-top: 20px;
            position: relative;
            z-index: 1;
        }
        
        .back-login a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        
        .back-login a:hover {
            text-decoration: underline;
        }

        .captcha-container {
            display: flex;
            gap: 10px;
        }

        .captcha-container .layui-input {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>✨ 代理注册 ✨</h1>
            <p>加入我们，开启代理之旅</p>
        </div>

        <div class="register-tabs">
            <div class="register-tab active" data-type="password">账号密码注册</div>
            <div class="register-tab" data-type="kami">卡密注册</div>
        </div>

        <form class="layui-form" lay-filter="registerForm">
            <!-- 账号密码注册表单 -->
            <div id="passwordForm">
                <div class="layui-form-item">
                    <label class="layui-form-label">账号</label>
                    <div class="layui-input-block">
                        <input type="text" name="username" required lay-verify="required|username"
                               placeholder="请输入账号（3-20位字符）" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">密码</label>
                    <div class="layui-input-block">
                        <input type="password" name="password" required lay-verify="required|password"
                               placeholder="请输入密码（6-20位字符）" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">确认密码</label>
                    <div class="layui-input-block">
                        <input type="password" name="confirm_password" required lay-verify="required|confirm_password"
                               placeholder="请再次输入密码" class="layui-input">
                    </div>
                </div>
            </div>

            <!-- 卡密注册表单 -->
            <div id="kamiForm" style="display: none;">
                <div class="layui-form-item">
                    <label class="layui-form-label">卡密</label>
                    <div class="layui-input-block">
                        <input type="text" name="kami_code" required lay-verify="required"
                               placeholder="请输入注册卡密" class="layui-input">
                    </div>
                    <div class="form-hint">使用卡密注册将自动设置等级和初始余额</div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">账号</label>
                    <div class="layui-input-block">
                        <input type="text" name="kami_username" required lay-verify="required|username"
                               placeholder="请输入账号" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">密码</label>
                    <div class="layui-input-block">
                        <input type="password" name="kami_password" required lay-verify="required|password"
                               placeholder="请输入密码" class="layui-input">
                    </div>
                </div>
            </div>

            <!-- 共同字段 -->
            <div class="layui-form-item">
                <label class="layui-form-label">代理名称</label>
                <div class="layui-input-block">
                    <input type="text" name="name" required lay-verify="required"
                           placeholder="请输入代理显示名称" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">联系方式</label>
                <div class="layui-input-block">
                    <input type="text" name="contact" lay-verify="phone"
                           placeholder="请输入手机号或QQ" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">申请等级</label>
                <div class="layui-input-block">
                    <select name="level_id" lay-filter="level_select">
                        <option value="">请选择代理等级</option>
                        <?php
                        $levels = $DB->select("SELECT * FROM agent_level WHERE state = 1 ORDER BY level_order DESC");
                        foreach ($levels as $level):
                        ?>
                        <option value="<?php echo $level['id']; ?>" 
                                data-discount="<?php echo $level['kami_discount']; ?>"
                                data-free="<?php echo $level['daily_free_kami']; ?>"
                                data-balance="<?php echo $level['initial_balance']; ?>"
                                data-max="<?php echo $level['max_sub_agents']; ?>">
                            <?php echo $level['level_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="level-info" id="levelInfo">
                    <div class="level-info-title">等级特权</div>
                    <div class="level-info-item">
                        <span>卡密折扣：</span>
                        <span id="discountText">-</span>
                    </div>
                    <div class="level-info-item">
                        <span>每日免费：</span>
                        <span id="freeText">-</span>
                    </div>
                    <div class="level-info-item">
                        <span>初始余额：</span>
                        <span id="balanceText">-</span>
                    </div>
                    <div class="level-info-item">
                        <span>下级数量：</span>
                        <span id="maxText">-</span>
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">申请备注</label>
                <div class="layui-input-block">
                    <textarea name="remark" placeholder="请输入申请说明（选填）" class="layui-textarea"></textarea>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button type="submit" class="register-btn">立即注册</button>
                </div>
            </div>

            <div class="back-login">
                已有账号？<a href="login.php">立即登录</a>
            </div>
        </form>
    </div>

    <script src="../assets/layui/layui.js"></script>
    <script>
        layui.use(['form', 'layer'], function() {
            var form = layui.form;
            var layer = layui.layer;
            var $ = layui.jquery;

            // 注册方式切换
            $('.register-tab').click(function() {
                $('.register-tab').removeClass('active');
                $(this).addClass('active');

                var type = $(this).data('type');
                if (type === 'password') {
                    $('#passwordForm').show();
                    $('#kamiForm').hide();
                    $('input[name="username"]').attr('required', 'required');
                } else {
                    $('#passwordForm').hide();
                    $('#kamiForm').show();
                    $('input[name="username"]').removeAttr('required');
                }
            });

            // 等级选择显示信息
            form.on('select(level_select)', function(data) {
                var option = $(data.elem).find('option:selected');
                var discount = option.data('discount');
                var free = option.data('free');
                var balance = option.data('balance');
                var max = option.data('max');

                if (discount !== undefined) {
                    $('#discountText').text((discount * 10).toFixed(1) + '折');
                    $('#freeText').text(free + '个/天');
                    $('#balanceText').text('¥' + parseFloat(balance).toFixed(2));
                    $('#maxText').text(max == 0 ? '无限制' : max + '个');
                    $('#levelInfo').addClass('show');
                } else {
                    $('#levelInfo').removeClass('show');
                }
            });

            // 表单验证规则
            form.verify({
                username: function(value) {
                    if (!/^[a-zA-Z0-9_]{3,20}$/.test(value)) {
                        return '账号必须为3-20位字母、数字或下划线';
                    }
                },
                password: function(value) {
                    if (!/^[a-zA-Z0-9_]{6,20}$/.test(value)) {
                        return '密码必须为6-20位字母、数字或下划线';
                    }
                },
                confirm_password: function(value) {
                    var password = $('input[name="password"]').val() || $('input[name="kami_password"]').val();
                    if (value !== password) {
                        return '两次密码输入不一致';
                    }
                },
                phone: function(value) {
                    if (value && !/^1[3-9]\d{9}$/.test(value) && !/^\d{5,12}$/.test(value)) {
                        return '请输入正确的手机号或QQ号';
                    }
                }
            });

            // 表单提交
            form.on('submit()', function(data) {
                var formData = data.field;
                var registerType = $('.register-tab.active').data('type');

                if (registerType === 'password') {
                    if (!formData.username || !formData.password || !formData.confirm_password) {
                        layer.msg('请填写完整信息', {icon: 5});
                        return false;
                    }
                    formData.register_type = 2;
                } else {
                    if (!formData.kami_code || !formData.kami_username || !formData.kami_password) {
                        layer.msg('请填写完整信息', {icon: 5});
                        return false;
                    }
                    formData.register_type = 1;
                    formData.username = formData.kami_username;
                    formData.password = formData.kami_password;
                }

                if (!formData.level_id) {
                    layer.msg('请选择申请等级', {icon: 5});
                    return false;
                }

                $.ajax({
                    url: '../agent/ajax.php?act=agentregister',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(res) {
                        if (res.code == '1') {
                            layer.msg(res.msg, {icon: 1, time: 2000}, function() {
                                window.location.href = 'login.php';
                            });
                        } else {
                            layer.msg(res.msg, {icon: 5});
                        }
                    },
                    error: function() {
                        layer.msg('注册失败，请重试', {icon: 5});
                    }
                });

                return false;
            });
        });
    </script>
</body>
</html>
