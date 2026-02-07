<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$pageTitle = '代理管理';
$cardTitle = '代理列表';
$tableUrl = 'agent_list.php';
$refreshFunction = 'loadAgents';

include 'card_view_template.php';
?>
<script>
layui.use(['layer', 'jquery'], function() {
    var layer = layui.layer;
    var $ = layui.jquery;

    // 加载代理数据
    function loadAgents() {
        $.ajax({
            url: 'ajax.php?act=getagent',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.code == '0' && res.data) {
                    renderAgentCards(res.data);
                } else {
                    $('#cardContainer').html('<div style="text-align:center;padding:40px;color:#999;">暂无代理数据</div>');
                }
            },
            error: function() {
                layer.msg('加载失败', {icon: 5});
            }
        });
    }

    // 渲染代理卡片
    function renderAgentCards(data) {
        var config = {
            titleField: 'username',
            badgeField: 'name',
            fields: [
                {
                    label: 'ID',
                    name: 'id',
                    value: function(item) { return item.id; }
                },
                {
                    label: '余额',
                    name: 'balance',
                    value: function(item) { return '¥' + parseFloat(item.balance).toFixed(2); },
                    className: 'text-danger'
                },
                {
                    label: '总卡密',
                    name: 'total_kami',
                    value: function(item) { return item.total_kami || 0; }
                },
                {
                    label: '已使用',
                    name: 'used_kami',
                    value: function(item) { return item.used_kami || 0; },
                    className: 'text-success'
                },
                {
                    label: '今日制卡',
                    name: 'today_kami',
                    value: function(item) { return item.today_kami || 0; },
                    className: 'text-info'
                },
                {
                    label: '今日使用',
                    name: 'today_used',
                    value: function(item) { return item.today_used || 0; },
                    className: 'text-warning'
                },
                {
                    label: '级别',
                    name: 'level',
                    value: function(item) { return 'L' + item.level; }
                },
                {
                    label: '状态',
                    name: 'state',
                    value: function(item) { return item.state == 1 ? '启用' : '禁用'; },
                    className: function(item) { return item.state == 1 ? 'text-success' : 'text-danger'; }
                }
            ],
            actions: [
                {
                    label: '详情',
                    icon: 'layui-icon-detail',
                    btnClass: 'layui-btn-xs',
                    onClick: function(item) { return 'onclick="showDetail(' + item.id + ', \'' + item.username + '\')"'; }
                },
                {
                    label: '充值',
                    icon: 'layui-icon-cart',
                    btnClass: 'layui-btn-xs layui-btn-normal',
                    onClick: function(item) { return 'onclick="recharge(' + item.id + ', \'' + item.username + '\')"'; }
                },
                {
                    label: '编辑',
                    icon: 'layui-icon-edit',
                    btnClass: 'layui-btn-xs layui-btn-normal',
                    onClick: function(item) { return 'onclick="editAgent(' + item.id + ', \'' + item.username + '\')"'; }
                },
                {
                    label: function(item) { return item.state == 1 ? '禁用' : '启用'; },
                    icon: 'layui-icon-circle',
                    btnClass: function(item) { return item.state == 1 ? 'layui-btn-xs layui-btn-warm' : 'layui-btn-xs layui-btn-normal'; },
                    onClick: function(item) { return 'onclick="toggleState(' + item.id + ', ' + (item.state == 1 ? 0 : 1) + ')"'; }
                },
                {
                    label: '删除',
                    icon: 'layui-icon-delete',
                    btnClass: 'layui-btn-xs layui-btn-danger',
                    onClick: function(item) { return 'onclick="deleteAgent(' + item.id + ', \'' + item.username + '\')"'; }
                }
            ]
        };

        window.renderCards(data, config);
    }

    // 查看详情
    window.showDetail = function(id, username) {
        layer.open({
            type: 2,
            title: '代理详情 - ' + username,
            area: ['90%', '90%'],
            maxmin: true,
            content: 'agent_detail.php?id=' + id
        });
    };

    // 充值
    window.recharge = function(id, username) {
        layer.prompt({
            title: '充值余额 - ' + username,
            formType: 0,
            value: ''
        }, function(value, index) {
            var amount = parseFloat(value);
            if (isNaN(amount) || amount <= 0) {
                layer.msg('请输入正确的金额', {icon: 5});
                return;
            }

            $.ajax({
                url: 'ajax.php?act=rechargeagent',
                type: 'POST',
                data: {id: id, amount: amount},
                dataType: 'json',
                success: function(res) {
                    if (res.code == '1') {
                        layer.msg(res.msg, {icon: 1});
                        loadAgents();
                    } else {
                        layer.msg(res.msg, {icon: 5});
                    }
                }
            });
            layer.close(index);
        });
    };

    // 编辑代理
    window.editAgent = function(id, username) {
        layer.open({
            type: 2,
            title: '编辑代理 - ' + username,
            area: ['90%', '80%'],
            maxmin: true,
            content: 'edit_agent.php?id=' + id,
            end: function() {
                loadAgents();
            }
        });
    };

    // 切换状态
    window.toggleState = function(id, state) {
        var action = state == 1 ? '启用' : '禁用';
        layer.confirm('确定' + action + '该代理吗？', function(index) {
            $.ajax({
                url: 'ajax.php?act=updateagentstate',
                type: 'POST',
                data: {id: id, state: state},
                dataType: 'json',
                success: function(res) {
                    if (res.code == '1') {
                        layer.msg(res.msg, {icon: 1});
                        loadAgents();
                    } else {
                        layer.msg(res.msg, {icon: 5});
                    }
                }
            });
            layer.close(index);
        });
    };

    // 删除代理
    window.deleteAgent = function(id, username) {
        layer.confirm('确定删除代理 ' + username + ' 吗？删除后无法恢复！', function(index) {
            $.ajax({
                url: 'ajax.php?act=delagent',
                type: 'POST',
                data: {id: id},
                dataType: 'json',
                success: function(res) {
                    if (res.code == '1') {
                        layer.msg(res.msg, {icon: 1});
                        loadAgents();
                    } else {
                        layer.msg(res.msg, {icon: 5});
                    }
                }
            });
            layer.close(index);
        });
    };

    // 添加代理
    window.addAgent = function() {
        layer.open({
            type: 2,
            title: '添加代理',
            area: ['90%', '80%'],
            maxmin: true,
            content: 'new_agent.php',
            end: function() {
                loadAgents();
            }
        });
    };

    // 加载数据
    loadAgents();
});
</script>

<style>
.text-danger { color: #ff5722; }
.text-success { color: #009688; }
.text-info { color: #1E9FFF; }
.text-warning { color: #FFB800; }

/* 添加代理按钮 */
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

@media (max-width: 768px) {
    .card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .card-header button {
        width: 100%;
        margin-left: 0 !important;
    }
}
</style>