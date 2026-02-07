<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>代理等级管理</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../assets/layui/css/layui.css" />
    <style>
        .level-badge {
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .level-1 { background: #ff5722; color: white; }
        .level-2 { background: #ff9800; color: white; }
        .level-3 { background: #009688; color: white; }
        .level-other { background: #1e9fff; color: white; }
    </style>
</head>
<body>
    <div class="layui-card">
        <div class="layui-card-header">
            <span>代理等级管理</span>
            <div style="float: right;">
                <button class="layui-btn layui-btn-sm layui-btn-primary" style="margin-right: 10px;" onclick="importLevel()">
                    <i class="layui-icon layui-icon-download-circle"></i>导入等级
                </button>
                <button class="layui-btn layui-btn-sm layui-btn-warm" style="margin-right: 10px;" onclick="initLevels()">
                    <i class="layui-icon layui-icon-refresh"></i>初始化默认等级
                </button>
                <button class="layui-btn layui-btn-sm layui-btn-primary" style="margin-right: 10px;" onclick="showCardView()">
                    <i class="layui-icon layui-icon-template"></i>卡片视图
                </button>
                <button class="layui-btn layui-btn-sm layui-btn-normal" onclick="addLevel()">
                    <i class="layui-icon layui-icon-add-1"></i>添加等级
                </button>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="level_list" lay-filter="level_list"></table>
        </div>
    </div>

    <script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="config">配置权限</a>
        {{# if(d.state == 1) { }}
        <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="disable">禁用</a>
        {{# } else { }}
        <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="enable">启用</a>
        {{# } }}
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>

    <script type="text/html" id="levelInfo">
        <div style="padding: 10px;">
            <div><span class="layui-badge layui-bg-gray">折扣:</span> {{ d.kami_discount }}折</div>
            <div><span class="layui-badge layui-bg-gray">免费卡密:</span> {{ d.daily_free_kami }}个/天</div>
            <div><span class="layui-badge layui-bg-gray">初始余额:</span> ¥{{ d.initial_balance }}</div>
            <div><span class="layui-badge layui-bg-gray">可添加下级:</span> {{ d.max_sub_agents == 0 ? '无限制' : d.max_sub_agents + '个' }}</div>
        </div>
    </script>

    <script src="../assets/layui/layui.js"></script>
    <script>
        layui.use(['table', 'form', 'layer', 'util'], function() {
            var table = layui.table;
            var form = layui.form;
            var layer = layui.layer;
            var util = layui.util;
            var $ = layui.jquery;

            // 渲染表格
            table.render({
                elem: "#level_list",
                height: "full-200",
                url: "ajax_router.php?act=getagentlevel",
                page: true,
                limit: 20,
                limits: [10, 20, 50, 100],
                title: "代理等级列表",
                parseData: function(res) {
                    console.log('原始响应:', res);
                    // 直接使用后端返回的标准格式
                    var data = [];
                    var code = 0;
                    var msg = "";
                    var count = 0;
                    
                    // 兼容多种数据格式
                    if (res.code == "0" || res.code == 0) {
                        // Layui标准格式: {code: 0, msg: "", count: N, data: [...]}
                        code = 0;
                        msg = res.msg || "";
                        count = parseInt(res.count) || 0;
                        data = Array.isArray(res.data) ? res.data : [];
                    } else if (res.code == "1") {
                        // 兼容旧格式: {code: "1", data: [...]}
                        code = 0;
                        data = Array.isArray(res.data) ? res.data : [];
                        count = data.length;
                    } else {
                        // 其他情况尝试直接解析
                        data = Array.isArray(res) ? res : [];
                        count = data.length;
                    }
                    
                    console.log('解析后数据:', {
                        code: code,
                        msg: msg,
                        count: count,
                        data: data
                    });
                    
                    return {
                        "code": code,
                        "msg": msg,
                        "count": count,
                        "data": data
                    };
                },
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
                        field: "level_name",
                        title: "等级名称",
                        width: 120,
                        align: "center",
                        templet: function(d) {
                            return '<span class="level-badge level-' + d.level_order + '">' + d.level_name + '</span>';
                        }
                    }, {
                        field: "level_order",
                        title: "排序",
                        width: 80,
                        align: "center"
                    }, {
                        field: "kami_discount",
                        title: "卡密折扣",
                        width: 100,
                        align: "center",
                        templet: function(d) {
                            return '<span style="color: #ff5722; font-weight: bold;">' + (d.kami_discount * 10).toFixed(1) + '折</span>';
                        }
                    }, {
                        field: "daily_free_kami",
                        title: "每日免费",
                        width: 100,
                        align: "center",
                        templet: function(d) {
                            return '<span style="color: #1e9fff;">' + d.daily_free_kami + '个</span>';
                        }
                    }, {
                        field: "initial_balance",
                        title: "初始余额",
                        width: 120,
                        align: "center",
                        templet: function(d) {
                            return '<span style="color: #009688; font-weight: bold;">¥' + parseFloat(d.initial_balance).toFixed(2) + '</span>';
                        }
                    }, {
                        field: "max_sub_agents",
                        title: "下级数量",
                        width: 100,
                        align: "center",
                        templet: function(d) {
                            return d.max_sub_agents == 0 ? '无限制' : d.max_sub_agents;
                        }
                    }, {
                        field: "agent_count",
                        title: "代理数",
                        width: 90,
                        align: "center",
                        templet: function(d) {
                            return '<span class="layui-badge layui-bg-blue">' + (d.agent_count || 0) + '</span>';
                        }
                    }, {
                        field: "state",
                        title: "状态",
                        width: 90,
                        align: "center",
                        templet: function(d) {
                            return d.state == 1 ? '<span style="color: green;">启用</span>' : '<span style="color: red;">禁用</span>';
                        }
                    }, {
                        field: "created_time",
                        title: "创建时间",
                        width: 160,
                        align: "center"
                    }, {
                        fixed: "right",
                        title: "操作",
                        toolbar: "#barDemo",
                        width: 220,
                        align: "center"
                    }]
                ]
            });

            // 导入等级
            window.importLevel = function() {
                window.location.href = 'agent_level_import.php';
            };

            // 卡片视图
            window.showCardView = function() {
                window.location.href = 'agent_level_view.php';
            };

            // 初始化默认等级
            window.initLevels = function() {
                layer.confirm('确定要初始化默认等级吗？<br><br><span style="color: #ff5722;">注意：如果数据库中已有等级数据，将提示是否覆盖。</span>', function(index) {
                    $.ajax({
                        url: 'ajax_router.php?act=initdefaultlevels',
                        type: 'POST',
                        dataType: 'json',
                        success: function(res) {
                            if (res.code == '1') {
                                layer.msg(res.msg || '初始化成功！', {icon: 1, time: 2000}, function() {
                                    table.reload('level_list');
                                });
                            } else {
                                layer.msg(res.msg || '初始化失败！', {icon: 2});
                            }
                        },
                        error: function() {
                            layer.msg('系统错误，请稍后重试！', {icon: 2});
                        }
                    });
                    layer.close(index);
                });
            };

            // 添加等级
            window.addLevel = function() {
                layer.open({
                    type: 2,
                    title: '添加代理等级',
                    area: ['600px', '700px'],
                    content: 'agent_level_edit.php?act=add',
                    end: function() {
                        table.reload('level_list');
                    }
                });
            };

            // 行工具栏事件
            table.on('tool(level_list)', function(obj) {
                var data = obj.data;

                if (obj.event === 'edit') {
                    layer.open({
                        type: 2,
                        title: '编辑等级 - ' + data.level_name,
                        area: ['600px', '700px'],
                        content: 'agent_level_edit.php?act=edit&id=' + data.id,
                        end: function() {
                            table.reload('level_list');
                        }
                    });
                } else if (obj.event === 'config') {
                    layer.open({
                        type: 2,
                        title: '等级权限配置 - ' + data.level_name,
                        area: ['700px', '600px'],
                        content: 'agent_level_permission.php?id=' + data.id,
                        end: function() {
                            table.reload('level_list');
                        }
                    });
                } else if (obj.event === 'disable') {
                    layer.confirm('确定禁用该等级吗？', function(index) {
                        $.ajax({
                            url: 'ajax_router.php?act=updatelevelstate',
                            type: 'POST',
                            data: {id: data.id, state: 0},
                            dataType: 'json',
                            success: function(res) {
                                if (res.code == '1') {
                                    layer.msg(res.msg, {icon: 1});
                                    table.reload('level_list');
                                } else {
                                    layer.msg(res.msg, {icon: 5});
                                }
                            }
                        });
                        layer.close(index);
                    });
                } else if (obj.event === 'enable') {
                    $.ajax({
                        url: 'ajax_router.php?act=updatelevelstate',
                        type: 'POST',
                        data: {id: data.id, state: 1},
                        dataType: 'json',
                        success: function(res) {
                            if (res.code == '1') {
                                layer.msg(res.msg, {icon: 1});
                                table.reload('level_list');
                            } else {
                                layer.msg(res.msg, {icon: 5});
                            }
                        }
                    });
                } else if (obj.event === 'del') {
                    layer.confirm('确定删除该等级吗？删除后无法恢复！', function(index) {
                        $.ajax({
                            url: 'ajax_router.php?act=delagentlevel',
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
