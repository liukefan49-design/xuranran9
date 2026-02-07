<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>添加代理</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../assets/layui/css/layui.css" />
    <style>
        body {
            padding: 20px;
        }
    </style>
</head>

<body class="layui-form form">
    <div class="layui-form-item">
        <label class="layui-form-label">
            代理账号
            <span class="layui-must">*</span>
        </label>
        <div class="layui-input-block">
            <input type="text" name="username" required lay-verify="required" class="layui-input" placeholder="请输入代理账号">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">
            代理密码
            <span class="layui-must">*</span>
        </label>
        <div class="layui-input-block">
            <input type="password" name="password" required lay-verify="required" class="layui-input" placeholder="请输入代理密码">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">
            代理名称
            <span class="layui-must">*</span>
        </label>
        <div class="layui-input-block">
            <input type="text" name="name" required lay-verify="required" class="layui-input" placeholder="请输入代理名称">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">
            初始余额
        </label>
        <div class="layui-input-block">
            <input type="number" name="balance" class="layui-input" placeholder="请输入初始余额，默认为0" value="0" step="0.01">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">
            代理级别
        </label>
        <div class="layui-input-block">
            <select name="level_id" lay-verify="required">
                <option value="">请选择代理级别</option>
            </select>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">
            上级代理
        </label>
        <div class="layui-input-block">
            <select name="parent_id" lay-search="">
                <option value="0">总后台直属</option>
            </select>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">
            权限设置
        </label>
        <div class="layui-input-block">
            <input type="checkbox" name="can_del_kami" title="允许删除卡密" checked lay-skin="primary">
            <input type="checkbox" name="can_del_user" title="允许删除用户" checked lay-skin="primary">
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="submit">立即添加</button>
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>

    <script src="../assets/layui/layui.js"></script>
    <script>
        layui.use(['form', 'layer'], function() {
            var form = layui.form;
            var layer = layui.layer;
            var $ = layui.jquery;

            // 加载代理级别列表
            $.ajax({
                url: 'ajax_agent.php?act=getagentlevel',
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.code == '1' && res.msg) {
                        var html = '<option value="">请选择代理级别</option>';
                        for (var i = 0; i < res.msg.length; i++) {
                            var level = res.msg[i];
                            html += '<option value="' + level.id + '">' + level.level_name + '</option>';
                        }
                        $('select[name="level_id"]').html(html);
                        form.render('select');
                    }
                },
                error: function() {
                    layer.msg('加载代理级别失败', {icon: 5});
                }
            });

            // 加载上级代理列表
            $.ajax({
                url: 'ajax.php?act=getagent',
                type: 'POST',
                dataType: 'json',
                data: {page: 1, limit: 1000},
                success: function(res) {
                    if (res.code == 0 && res.data) {
                        var html = '<option value="0">总后台直属</option>';
                        for (var i = 0; i < res.data.length; i++) {
                            var agent = res.data[i];
                            html += '<option value="' + agent.id + '">' + agent.name + ' (ID:' + agent.id + ')</option>';
                        }
                        $('select[name="parent_id"]').html(html);
                        form.render('select');
                    }
                },
                error: function() {
                    layer.msg('加载上级代理失败', {icon: 5});
                }
            });

            // 提交表单
            form.on('submit(submit)', function(data) {
                $.ajax({
                    url: 'ajax.php?act=newagent',
                    type: 'POST',
                    dataType: 'json',
                    data: data.field,
                    beforeSend: function() {
                        layer.msg('正在提交', {
                            icon: 16,
                            shade: 0.05,
                            time: false
                        });
                    },
                    success: function(res) {
                        if (res.code == '1') {
                            parent.layer.msg(res.msg, {icon: 1});
                            parent.location.reload();
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                        } else {
                            layer.msg(res.msg, {icon: 5});
                        }
                    },
                    error: function(xhr, status, error) {
                        layer.msg('请求失败：' + (xhr.responseJSON ? xhr.responseJSON.msg : error), {icon: 5});
                    }
                });
                return false;
            });
        });
    </script>
</body>
</html>
