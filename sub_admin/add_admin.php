<?php
include("../includes/common.php");
if (!($islogin == 1)) {
    exit('<script language=\'javascript\'>alert("您还没有登录，请先登录！");window.location.href=\'login.php\';</script>');
}

// 检查权限
if ($subconf['qx'] != 0) {
    exit('<script language=\'javascript\'>alert("您没有权限访问此页面！");history.back();</script>');
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新增管理员</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../assets/layui/css/layui.css" />
</head>
<body style="padding: 20px;">
    <form class="layui-form" lay-filter="adminForm">
        <div class="layui-form-item">
            <label class="layui-form-label">用户名</label>
            <div class="layui-input-block">
                <input type="text" name="username" required lay-verify="required" placeholder="请输入用户名" autocomplete="off" class="layui-input">
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">密码</label>
            <div class="layui-input-block">
                <input type="password" name="password" required lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">确认密码</label>
            <div class="layui-input-block">
                <input type="password" name="password2" required lay-verify="required" placeholder="请再次输入密码" autocomplete="off" class="layui-input">
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">真实姓名</label>
            <div class="layui-input-block">
                <input type="text" name="realname" placeholder="请输入真实姓名" autocomplete="off" class="layui-input">
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-block">
                <input type="radio" name="state" value="1" title="启用" checked>
                <input type="radio" name="state" value="0" title="禁用">
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">备注</label>
            <div class="layui-input-block">
                <textarea name="remark" placeholder="请输入备注" class="layui-textarea"></textarea>
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
                
                // 验证密码
                if (field.password !== field.password2) {
                    layer.msg('两次密码不一致', {icon: 2});
                    return false;
                }
                
                if (field.password.length < 6) {
                    layer.msg('密码长度不能少于6位', {icon: 2});
                    return false;
                }
                
                $.ajax({
                    url: 'ajax.php?act=addadmin',
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

