<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>代理系统公告</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../assets/layui/css/layui.css" />
    <style>
        .notice-type-1 { color: #1e9fff; }
        .notice-type-2 { color: #ff5722; }
        .notice-type-3 { color: #ff9800; }
        .sticky-icon { color: #ff5722; font-size: 16px; }
    </style>
</head>
<body>
    <div class="layui-card">
        <div class="layui-card-header">
            <span>代理系统公告</span>
            <div style="float: right;">
                <button class="layui-btn layui-btn-sm layui-btn-primary" style="margin-right: 10px;" onclick="showCardView()">
                    <i class="layui-icon layui-icon-template"></i>卡片视图
                </button>
                <button class="layui-btn layui-btn-sm layui-btn-normal" onclick="addNotice()">
                    <i class="layui-icon layui-icon-add-1"></i>发布公告
                </button>
                <button class="layui-btn layui-btn-sm" style="margin-left: 10px;" onclick="reload()">
                    <i class="layui-icon layui-icon-refresh"></i>刷新
                </button>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="notice_list" lay-filter="notice_list"></table>
        </div>
    </div>

    <script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-xs" lay-event="view">查看</a>
        <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="edit">编辑</a>
        {{# if(d.state == 1) { }}
        <a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="disable">禁用</a>
        {{# } else { }}
        <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="enable">启用</a>
        {{# } }}
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
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
                elem: "#notice_list",
                height: "full-200",
                url: "ajax.php?act=getagentnotice",
                page: true,
                limit: 20,
                limits: [10, 20, 50, 100],
                title: "公告列表",
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
                        field: "title",
                        title: "公告标题",
                        width: 200,
                        templet: function(d) {
                            var title = d.title;
                            if (d.is_sticky) {
                                title += ' <i class="layui-icon sticky-icon">&#xe623;</i>';
                            }
                            return title;
                        }
                    }, {
                        field: "notice_type",
                        title: "公告类型",
                        width: 120,
                        align: "center",
                        templet: function(d) {
                            var types = {'1': '系统公告', '2': '活动通知', '3': '重要提醒'};
                            return '<span class="notice-type-' + d.notice_type + '">' + types[d.notice_type] + '</span>';
                        }
                    }, {
                        field: "priority",
                        title: "优先级",
                        width: 90,
                        align: "center",
                        sort: true
                    }, {
                        field: "target_level",
                        title: "目标等级",
                        width: 120,
                        align: "center",
                        templet: function(d) {
                            return d.target_level == 0 ? '所有等级' : '等级ID:' + d.target_level;
                        }
                    }, {
                        field: "read_count",
                        title: "阅读次数",
                        width: 100,
                        align: "center",
                        sort: true
                    }, {
                        field: "publish_time",
                        title: "发布时间",
                        width: 160,
                        align: "center"
                    }, {
                        field: "state",
                        title: "状态",
                        width: 90,
                        align: "center",
                        templet: function(d) {
                            return d.state == 1 ? '<span style="color: green;">启用</span>' : '<span style="color: red;">禁用</span>';
                        }
                    }, {
                        fixed: "right",
                        title: "操作",
                        toolbar: "#barDemo",
                        width: 200,
                        align: "center"
                    }]
                ]
            });

            // 添加公告
            window.addNotice = function() {
                layer.open({
                    type: 2,
                    title: '发布公告',
                    area: ['90%', '90%'],
                    maxmin: true,
                    content: 'agent_notice_edit.php?act=add',
                    end: function() {
                        table.reload('notice_list');
                    }
                });
            };

            // 卡片视图
            window.showCardView = function() {
                window.location.href = 'agent_notice_view.php';
            };

            // 刷新
            window.reload = function() {
                table.reload('notice_list');
            };

            // 行工具栏事件
            table.on('tool(notice_list)', function(obj) {
                var data = obj.data;

                if (obj.event === 'view') {
                    layer.open({
                        type: 2,
                        title: '查看公告 - ' + data.title,
                        area: ['800px', '600px'],
                        content: 'agent_notice_view.php?id=' + data.id
                    });
                } else if (obj.event === 'edit') {
                    layer.open({
                        type: 2,
                        title: '编辑公告 - ' + data.title,
                        area: ['800px', '700px'],
                        content: 'agent_notice_edit.php?act=edit&id=' + data.id,
                        end: function() {
                            table.reload('notice_list');
                        }
                    });
                } else if (obj.event === 'disable') {
                    layer.confirm('确定禁用该公告吗？', function(index) {
                        $.ajax({
                            url: 'ajax.php?act=updatenoticestate',
                            type: 'POST',
                            data: {id: data.id, state: 0},
                            dataType: 'json',
                            success: function(res) {
                                if (res.code == '1') {
                                    layer.msg(res.msg, {icon: 1});
                                    table.reload('notice_list');
                                } else {
                                    layer.msg(res.msg, {icon: 5});
                                }
                            }
                        });
                        layer.close(index);
                    });
                } else if (obj.event === 'enable') {
                    $.ajax({
                        url: 'ajax.php?act=updatenoticestate',
                        type: 'POST',
                        data: {id: data.id, state: 1},
                        dataType: 'json',
                        success: function(res) {
                            if (res.code == '1') {
                                layer.msg(res.msg, {icon: 1});
                                table.reload('notice_list');
                            } else {
                                layer.msg(res.msg, {icon: 5});
                            }
                        }
                    });
                } else if (obj.event === 'del') {
                    layer.confirm('确定删除该公告吗？删除后无法恢复！', function(index) {
                        $.ajax({
                            url: 'ajax.php?act=delagentnotice',
                            type: 'POST',
                            data: {id: data.id},
                            dataType: 'json',
                            success: function(res) {
                                if (res.code == '1') {
                                    layer.msg(res.msg, {icon: 1});
                                    obj.del();
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
