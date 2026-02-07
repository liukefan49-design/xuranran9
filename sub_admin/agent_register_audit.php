<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>代理注册审核</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../assets/layui/css/layui.css" />
    <style>
        .audit-pending { color: #ff9800; font-weight: bold; }
        .audit-approved { color: #009688; }
        .audit-rejected { color: #ff5722; }
    </style>
</head>
<body>
    <div class="layui-card">
        <div class="layui-card-header">
            <span>代理注册审核</span>
            <div style="float: right;">
                <button class="layui-btn layui-btn-sm layui-btn-primary" style="margin-right: 10px;" onclick="showCardView()">
                    <i class="layui-icon layui-icon-template"></i>卡片视图
                </button>
                <button class="layui-btn layui-btn-sm" onclick="reload()">
                    <i class="layui-icon layui-icon-refresh"></i>刷新
                </button>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="register_list" lay-filter="register_list"></table>
        </div>
    </div>

    <script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-xs" lay-event="view">查看详情</a>
        <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="approve">通过</a>
        <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="reject">拒绝</a>
    </script>

    <script src="../assets/layui/layui.js"></script>
    <script>
        layui.use(['table', 'form', 'layer'], function() {
            var table = layui.table;
            var form = layui.form;
            var layer = layui.layer;
            var $ = layui.jquery;

            // 渲染表格
            table.render({
                elem: "#register_list",
                height: "full-200",
                url: "ajax_agent.php?act=getagentregister",
                page: true,
                limit: 20,
                limits: [10, 20, 50, 100],
                title: "注册申请列表",
                defaultToolbar: ['filter', 'print'],
                cols: [
                    [{
                        type: "checkbox"
                    }, {
                        field: "id",
                        title: "ID",
                        width: 80,
                        sort: true,
                        align: "center"
                    }, {
                        field: "username",
                        title: "注册账号",
                        width: 150,
                        align: "center"
                    }, {
                        field: "name",
                        title: "代理名称",
                        width: 150,
                        align: "center"
                    }, {
                        field: "level_name",
                        title: "申请等级",
                        width: 120,
                        align: "center"
                    }, {
                        field: "register_type",
                        title: "注册方式",
                        width: 100,
                        align: "center",
                        templet: function(d) {
                            return d.register_type == 1 ? '卡密注册' : '账号密码';
                        }
                    }, {
                        field: "contact",
                        title: "联系方式",
                        width: 150,
                        align: "center"
                    }, {
                        field: "register_ip",
                        title: "注册IP",
                        width: 120,
                        align: "center"
                    }, {
                        field: "register_time",
                        title: "注册时间",
                        width: 160,
                        align: "center"
                    }, {
                        fixed: "right",
                        title: "操作",
                        toolbar: "#barDemo",
                        width: 200,
                        align: "center"
                    }]
                ]
            });

            // 卡片视图
            window.showCardView = function() {
                window.location.href = 'agent_register_audit_view.php';
            };

            // 刷新
            window.reload = function() {
                table.reload('register_list');
            };
                var data = obj.data;

                if (obj.event === 'view') {
                    layer.open({
                        type: 1,
                        title: '注册详情',
                        area: ['600px', '500px'],
                        content: '<div style="padding: 20px;">' +
                                 '<div class="layui-form-item"><label class="layui-form-label">注册账号：</label><div class="layui-input-block">' + data.username + '</div></div>' +
                                 '<div class="layui-form-item"><label class="layui-form-label">代理名称：</label><div class="layui-input-block">' + data.name + '</div></div>' +
                                 '<div class="layui-form-item"><label class="layui-form-label">申请等级：</label><div class="layui-input-block">' + (data.level_name || '未知') + '</div></div>' +
                                 '<div class="layui-form-item"><label class="layui-form-label">联系方式：</label><div class="layui-input-block">' + (data.contact || '无') + '</div></div>' +
                                 '<div class="layui-form-item"><label class="layui-form-label">注册方式：</label><div class="layui-input-block">' + (data.register_type == 1 ? '卡密注册' : '账号密码') + '</div></div>' +
                                 '<div class="layui-form-item"><label class="layui-form-label">使用卡密：</label><div class="layui-input-block">' + (data.kami_code || '无') + '</div></div>' +
                                 '<div class="layui-form-item"><label class="layui-form-label">上级代理：</label><div class="layui-input-block">' + (data.parent_id > 0 ? 'ID:' + data.parent_id : '总后台直属') + '</div></div>' +
                                 '<div class="layui-form-item"><label class="layui-form-label">注册IP：</label><div class="layui-input-block">' + (data.register_ip || '未知') + '</div></div>' +
                                 '<div class="layui-form-item"><label class="layui-form-label">注册时间：</label><div class="layui-input-block">' + data.register_time + '</div></div>' +
                                 '<div class="layui-form-item"><label class="layui-form-label">申请备注：</label><div class="layui-input-block">' + (data.remark || '无') + '</div></div>' +
                                 '</div>'
                    });
                } else if (obj.event === 'approve') {
                    layer.confirm('确定通过该注册申请吗？<br><span style="color: red;">通过后将自动创建代理账号</span>', function(index) {
                        $.ajax({
                            url: 'ajax_agent.php?act=auditagentregister',
                            type: 'POST',
                            data: {id: data.id, status: 1},
                            dataType: 'json',
                            success: function(res) {
                                if (res.code == '1') {
                                    layer.msg(res.msg, {icon: 1});
                                    table.reload('register_list');
                                } else {
                                    layer.msg(res.msg, {icon: 5});
                                }
                            }
                        });
                        layer.close(index);
                    });
                } else if (obj.event === 'reject') {
                    layer.prompt({
                        title: '拒绝原因',
                        formType: 2,
                        area: ['400px', '200px']
                    }, function(value, index) {
                        if (!value) {
                            layer.msg('请输入拒绝原因', {icon: 5});
                            return;
                        }
                        $.ajax({
                            url: 'ajax_agent.php?act=auditagentregister',
                            type: 'POST',
                            data: {id: data.id, status: 2, reason: value},
                            dataType: 'json',
                            success: function(res) {
                                if (res.code == '1') {
                                    layer.msg(res.msg, {icon: 1});
                                    table.reload('register_list');
                                } else {
                                    layer.msg(res.msg, {icon: 5});
                                }
                            }
                        });
                        layer.close(index);
                    });
                }
            });
        });
    </script>
</body>
</html>
