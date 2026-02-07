<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>余额充值管理</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../assets/layui/css/layui.css" />
    <style>
        .recharge-stats {
            padding: 20px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            padding: 20px;
            color: white;
            text-align: center;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            margin-top: 10px;
        }
        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md3">
            <div class="stat-card">
                <div class="stat-label">今日充值总额</div>
                <div class="stat-value" id="todayTotal">¥0.00</div>
            </div>
        </div>
        <div class="layui-col-md3">
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="stat-label">本月充值总额</div>
                <div class="stat-value" id="monthTotal">¥0.00</div>
            </div>
        </div>
        <div class="layui-col-md3">
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="stat-label">充值次数</div>
                <div class="stat-value" id="rechargeCount">0</div>
            </div>
        </div>
        <div class="layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-body">
                    <button class="layui-btn layui-btn-fluid layui-btn-normal" onclick="quickRecharge()">
                        <i class="layui-icon layui-icon-add-1"></i>快速充值
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="layui-card">
        <div class="layui-card-header">余额充值记录</div>
        <div class="layui-card-body">
            <table id="recharge_list" lay-filter="recharge_list"></table>
        </div>
    </div>

    <script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-xs" lay-event="view">详情</a>
        <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="recharge">再次充值</a>
    </script>

    <script src="../assets/layui/layui.js"></script>
    <script>
        layui.use(['table', 'layer', 'jquery'], function() {
            var table = layui.table;
            var layer = layui.layer;
            var $ = layui.jquery;

            // 渲染表格
            table.render({
                elem: "#recharge_list",
                height: "full-300",
                url: "ajax.php?act=getrechargelist",
                page: true,
                limit: 50,
                limits: [20, 50, 100, 200],
                title: "充值记录",
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
                        field: "agent_name",
                        title: "代理账号",
                        width: 150,
                        align: "center"
                    }, {
                        field: "recharge_amount",
                        title: "充值金额",
                        width: 120,
                        align: "center",
                        templet: function(d) {
                            return '<span style="color: #ff5722; font-weight: bold;">¥' + parseFloat(d.recharge_amount).toFixed(2) + '</span>';
                        }
                    }, {
                        field: "balance_before",
                        title: "充值前",
                        width: 120,
                        align: "center",
                        templet: function(d) {
                            return '¥' + parseFloat(d.balance_before).toFixed(2);
                        }
                    }, {
                        field: "balance_after",
                        title: "充值后",
                        width: 120,
                        align: "center",
                        templet: function(d) {
                            return '<span style="color: #009688; font-weight: bold;">¥' + parseFloat(d.balance_after).toFixed(2) + '</span>';
                        }
                    }, {
                        field: "recharge_type",
                        title: "充值类型",
                        width: 100,
                        align: "center",
                        templet: function(d) {
                            var types = {'1': '后台充值', '2': '自动充值'};
                            return types[d.recharge_type] || '未知';
                        }
                    }, {
                        field: "created_by",
                        title: "操作人",
                        width: 120,
                        align: "center"
                    }, {
                        field: "recharge_time",
                        title: "充值时间",
                        width: 160,
                        align: "center"
                    }, {
                        field: "remark",
                        title: "备注",
                        minWidth: 200
                    }, {
                        fixed: "right",
                        title: "操作",
                        toolbar: "#barDemo",
                        width: 150,
                        align: "center"
                    }]
                ]
            });

            // 加载统计数据
            function loadStats() {
                $.ajax({
                    url: 'ajax.php?act=getrechargestats',
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        if (res.code == '1') {
                            $('#todayTotal').text('¥' + parseFloat(res.data.today_total).toFixed(2));
                            $('#monthTotal').text('¥' + parseFloat(res.data.month_total).toFixed(2));
                            $('#rechargeCount').text(res.data.count);
                        }
                    }
                });
            }
            loadStats();

            // 快速充值
            window.quickRecharge = function() {
                layer.open({
                    type: 2,
                    title: '快速充值',
                    area: ['500px', '400px'],
                    content: 'balance_recharge_quick.php',
                    end: function() {
                        table.reload('recharge_list');
                        loadStats();
                    }
                });
            };

            // 行工具栏事件
            table.on('tool(recharge_list)', function(obj) {
                var data = obj.data;

                if (obj.event === 'view') {
                    layer.open({
                        type: 1,
                        title: '充值详情',
                        area: ['500px', '350px'],
                        content: '<div style="padding: 20px;">' +
                                 '<div><strong>代理账号：</strong>' + data.agent_name + '</div>' +
                                 '<div><strong>充值金额：</strong><span style="color: #ff5722; font-weight: bold;">¥' + parseFloat(data.recharge_amount).toFixed(2) + '</span></div>' +
                                 '<div><strong>充值前余额：</strong>¥' + parseFloat(data.balance_before).toFixed(2) + '</div>' +
                                 '<div><strong>充值后余额：</strong><span style="color: #009688; font-weight: bold;">¥' + parseFloat(data.balance_after).toFixed(2) + '</span></div>' +
                                 '<div><strong>充值类型：</strong>' + (data.recharge_type == 1 ? '后台充值' : '自动充值') + '</div>' +
                                 '<div><strong>操作人：</strong>' + data.created_by + '</div>' +
                                 '<div><strong>充值时间：</strong>' + data.recharge_time + '</div>' +
                                 '<div><strong>备注：</strong>' + (data.remark || '无') + '</div>' +
                                 '</div>'
                    });
                } else if (obj.event === 'recharge') {
                    layer.open({
                        type: 2,
                        title: '充值 - ' + data.agent_name,
                        area: ['500px', '350px'],
                        content: 'balance_recharge_quick.php?agent_id=' + data.id,
                        end: function() {
                            table.reload('recharge_list');
                            loadStats();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
