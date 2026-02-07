<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>卡密价格设置</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../assets/layui/css/layui.css" />
    <style>
        .price-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .price-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        .price-card.disabled {
            background: #95a5a6;
            opacity: 0.7;
        }
        .price-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .price-amount {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .price-days {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 15px;
        }
        .price-actions {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="layui-card">
        <div class="layui-card-header">
            <span>卡密价格设置</span>
            <button class="layui-btn layui-btn-sm layui-btn-normal" style="float: right;" onclick="editPrice()">
                <i class="layui-icon layui-icon-edit"></i>批量编辑
            </button>
        </div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15" id="priceContainer">
                <!-- 价格卡片将通过JS动态生成 -->
            </div>
        </div>
    </div>

    <script src="../assets/layui/layui.js"></script>
    <script>
        layui.use(['layer', 'jquery'], function() {
            var layer = layui.layer;
            var $ = layui.jquery;

            // 加载价格数据
            function loadPrices() {
                $.ajax({
                    url: 'ajax.php?act=getkamiprice',
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        if (res.code == '1') {
                            renderPriceCards(res.data);
                        } else {
                            layer.msg(res.msg, {icon: 5});
                        }
                    },
                    error: function() {
                        layer.msg('加载失败', {icon: 5});
                    }
                });
            }

            // 渲染价格卡片
            function renderPriceCards(prices) {
                var html = '';
                var colors = [
                    'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                    'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                    'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                    'linear-gradient(135deg, #fa709a 0%, #fee140 100%)'
                ];

                $.each(prices, function(index, item) {
                    var color = colors[index % colors.length];
                    var disabled = item.state == 0 ? 'disabled' : '';
                    var statusText = item.state == 1 ? '启用' : '禁用';
                    var statusClass = item.state == 1 ? 'layui-btn-normal' : 'layui-btn-warm';

                    html += '<div class="layui-col-md4 layui-col-sm6">';
                    html += '<div class="price-card ' + disabled + '" style="background: ' + color + ';" data-id="' + item.id + '">';
                    html += '<div class="price-title">' + item.type_name + '</div>';
                    html += '<div class="price-amount">¥' + parseFloat(item.price).toFixed(2) + '</div>';
                    html += '<div class="price-days">' + (item.days == 0 ? '永久有效' : '有效期 ' + item.days + ' 天') + '</div>';
                    html += '<div class="price-actions">';
                    html += '<button class="layui-btn layui-btn-xs layui-btn-primary ' + statusClass + '" onclick="toggleState(' + item.id + ', ' + (item.state == 1 ? 0 : 1) + ')">' + statusText + '</button>';
                    html += '<button class="layui-btn layui-btn-xs layui-btn-primary" onclick="editSingle(' + item.id + ')">编辑</button>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                });

                $('#priceContainer').html(html);
            }

            // 加载数据
            loadPrices();

            // 切换状态
            window.toggleState = function(id, state) {
                $.ajax({
                    url: 'ajax.php?act=updatepricestate',
                    type: 'POST',
                    data: {id: id, state: state},
                    dataType: 'json',
                    success: function(res) {
                        if (res.code == '1') {
                            layer.msg(res.msg, {icon: 1});
                            loadPrices();
                        } else {
                            layer.msg(res.msg, {icon: 5});
                        }
                    }
                });
            };

            // 批量编辑
            window.editPrice = function() {
                layer.open({
                    type: 2,
                    title: '批量编辑卡密价格',
                    area: ['800px', '600px'],
                    content: 'kami_price_edit.php',
                    end: function() {
                        loadPrices();
                    }
                });
            };

            // 单个编辑
            window.editSingle = function(id) {
                layer.open({
                    type: 2,
                    title: '编辑卡密价格',
                    area: ['500px', '500px'],
                    content: 'kami_price_edit.php?id=' + id,
                    end: function() {
                        loadPrices();
                    }
                });
            };
        });
    </script>
</body>
</html>
