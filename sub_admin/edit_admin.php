<?php
include("../includes/common.php");
if (!($islogin == 1)) {
    exit('<script language=\'javascript\'>alert("您还没有登录，请先登录！");window.location.href=\'login.php\';</script>');
}

// 检查权限
if ($subconf['qx'] != 0) {
    exit('<script language=\'javascript\'>alert("您没有权限访问此页面！");history.back();</script>');
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    exit('<script language=\'javascript\'>alert("参数错误！");history.back();</script>');
}

$admin = $DB->selectRow("SELECT * FROM admin WHERE id=" . $id);
if (!$admin) {
    exit('<script language=\'javascript\'>alert("管理员不存在！");history.back();</script>');
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑管理员</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../assets/layui/css/layui.css" />
</head>
<body style="padding: 20px;">
    <form class="layui-form" lay-filter="adminForm">
        <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
        
        <div class="layui-form-item">
            <label class="layui-form-label">用户名</label>
            <div class="layui-input-block">
                <input type="text" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required lay-verify="required" placeholder="请输入用户名" autocomplete="off" class="layui-input" readonly>
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">新密码</label>
            <div class="layui-input-block">
                <input type="password" name="password" placeholder="不修改请留空" autocomplete="off" class="layui-input">
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">确认密码</label>
            <div class="layui-input-block">
                <input type="password" name="password2" placeholder="不修改请留空" autocomplete="off" class="layui-input">
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">真实姓名</label>
            <div class="layui-input-block">
                <input type="text" name="realname" value="<?php echo htmlspecialchars($admin['realname']); ?>" placeholder="请输入真实姓名" autocomplete="off" class="layui-input">
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-block">
                <input type="radio" name="state" value="1" title="启用" <?php echo $admin['state'] == 1 ? 'checked' : ''; ?>>
                <input type="radio" name="state" value="0" title="禁用" <?php echo $admin['state'] == 0 ? 'checked' : ''; ?>>
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">备注</label>
            <div class="layui-input-block">
                <textarea name="remark" placeholder="请输入备注" class="layui-textarea"><?php echo htmlspecialchars($admin['remark']); ?></textarea>
            </div>
        </div>
        
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="submit">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>

    <script src="../assets/layui/layui.js"></script>
    <script>
        layui.use(['form', 'layer'], function() {
            var form = layui.form;
            var layer = layui.layer;
            var $ = layui.$;

            form.on('submit(submit)', function(data) {
                var field = data.field;
                
                // 如果修改密码，验证密码
                if (field.password || field.password2) {
                    if (field.password !== field.password2) {
                        layer.msg('两次密码不一致', {icon: 2});
                        return false;
                    }
                    
                    if (field.password.length < 6) {
                        layer.msg('密码长度不能少于6位', {icon: 2});
                        return false;
                    }
                }
                
                $.ajax({
                    url: 'ajax.php?act=editadmin',
                    type: 'POST',
                    dataType: 'json',
                    data: field,
                    beforeSend: function() {
                        layer.msg('提交中...', {icon: 16, shade: 0.05, time: false});
                    },
                    success: function(res) {
                        layer.closeAll();
                        if (res.code == '1') {
                            layer.msg(res.msg, {icon: 1}, function() {
                                var index = parent.layer.getFrameIndex(window.name);
                                parent.layer.close(index);
                            });
                        } else {
                            layer.msg(res.msg, {icon: 2});
                        }
                    },
                    error: function() {
                        layer.closeAll();
                        layer.msg('提交失败', {icon: 2});
                    }
                });
                
                return false;
            });
        });
    </script>
</body>
</html>

