<?php
include '../includes/common.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$pageTitle = '代理系统公告';
$cardTitle = '公告列表';
$tableUrl = 'agent_notice.php';
$refreshFunction = 'loadNotices';

include 'card_view_template.php';
?>
<script>
layui.use(['layer', 'jquery'], function() {
    var layer = layui.layer;
    var $ = layui.jquery;

    // 加载公告数据
    function loadNotices() {
        $.ajax({
            url: 'ajax.php?act=getagentnotice',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.code == '0' && res.data) {
                    renderNoticeCards(res.data);
                } else {
                    $('#cardContainer').html('<div style="text-align:center;padding:40px;color:#999;">暂无公告数据</div>');
                }
            },
            error: function() {
                layer.msg('加载失败', {icon: 5});
            }
        });
    }

    // 渲染公告卡片
    function renderNoticeCards(data) {
        var html = '';
        var colors = [
            'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
            'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
            'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
            'linear-gradient(135deg, #fa709a 0%, #fee140 100%)'
        ];

        $.each(data, function(index, item) {
            var color = colors[index % colors.length];
            var types = {'1': '系统公告', '2': '活动通知', '3': '重要提醒'};
            var typeClass = 'notice-type-' + item.notice_type;
            var stickyIcon = item.is_sticky ? '<i class="layui-icon" style="color: #ff5722;">&#xe623;</i> ' : '';
            
            html += '<div class="info-card">';
            html += '<div class="card-header">';
            html += '<div>';
            html += '<div class="card-title">' + stickyIcon + item.title + '</div>';
            html += '<span style="color: #666; font-size: 12px; margin-top: 5px; display: block;">';
            html += '<span class="' + typeClass + '">' + types[item.notice_type] + '</span> ';
            html += '<span style="margin-left: 10px;">优先级: ' + item.priority + '</span>';
            html += '</span>';
            html += '</div>';
            html += '</div>';
            
            html += '<div class="card-content">';
            html += '<div class="card-item"><div class="card-label">ID</div><div class="card-value">' + item.id + '</div></div>';
            html += '<div class="card-item"><div class="card-label">目标等级</div><div class="card-value">' + (item.target_level == 0 ? '所有等级' : '等级' + item.target_level) + '</div></div>';
            html += '<div class="card-item"><div class="card-label">阅读次数</div><div class="card-value">' + item.read_count + '</div></div>';
            html += '<div class="card-item"><div class="card-label">发布时间</div><div class="card-value" style="font-size: 12px;">' + item.publish_time + '</div></div>';
            html += '<div class="card-item"><div class="card-label">状态</div><div class="card-value" style="color: ' + (item.state == 1 ? '#009688' : '#ff5722') + ';">' + (item.state == 1 ? '启用' : '禁用') + '</div></div>';
            html += '</div>';
            
            html += '<div class="card-actions">';
            html += '<button class="layui-btn layui-btn-xs" onclick="viewNotice(' + item.id + ', \'' + item.title + '\')"><i class="layui-icon layui-icon-read"></i> 查看</button>';
            html += '<button class="layui-btn layui-btn-xs layui-btn-normal" onclick="editNotice(' + item.id + ', \'' + item.title + '\')"><i class="layui-icon layui-icon-edit"></i> 编辑</button>';
            html += '<button class="layui-btn layui-btn-xs ' + (item.state == 1 ? 'layui-btn-warm' : 'layui-btn-normal') + '" onclick="toggleState(' + item.id + ', ' + (item.state == 1 ? 0 : 1) + ')">' + (item.state == 1 ? '禁用' : '启用') + '</button>';
            html += '<button class="layui-btn layui-btn-xs layui-btn-danger" onclick="deleteNotice(' + item.id + ', \'' + item.title + '\')"><i class="layui-icon layui-icon-delete"></i> 删除</button>';
            html += '</div>';
            
            html += '</div>';
        });

        $('#cardContainer').html(html);
    }

    // 查看公告
    window.viewNotice = function(id, title) {
        layer.open({
            type: 2,
            title: '查看公告 - ' + title,
            area: ['90%', '90%'],
            maxmin: true,
            content: 'agent_notice_view.php?id=' + id
        });
    };

    // 编辑公告
    window.editNotice = function(id, title) {
        layer.open({
            type: 2,
            title: '编辑公告 - ' + title,
            area: ['90%', '90%'],
            maxmin: true,
            content: 'agent_notice_edit.php?act=edit&id=' + id,
            end: function() {
                loadNotices();
            }
        });
    };

    // 切换状态
    window.toggleState = function(id, state) {
        var action = state == 1 ? '启用' : '禁用';
        layer.confirm('确定' + action + '该公告吗？', function(index) {
            $.ajax({
                url: 'ajax.php?act=updatenoticestate',
                type: 'POST',
                data: {id: id, state: state},
                dataType: 'json',
                success: function(res) {
                    if (res.code == '1') {
                        layer.msg(res.msg, {icon: 1});
                        loadNotices();
                    } else {
                        layer.msg(res.msg, {icon: 5});
                    }
                }
            });
            layer.close(index);
        });
    };

    // 删除公告
    window.deleteNotice = function(id, title) {
        layer.confirm('确定删除公告 "' + title + '" 吗？删除后无法恢复！', function(index) {
            $.ajax({
                url: 'ajax.php?act=deletenotice',
                type: 'POST',
                data: {id: id},
                dataType: 'json',
                success: function(res) {
                    if (res.code == '1') {
                        layer.msg(res.msg, {icon: 1});
                        loadNotices();
                    } else {
                        layer.msg(res.msg, {icon: 5});
                    }
                }
            });
            layer.close(index);
        });
    };

    // 添加公告
    window.addNotice = function() {
        layer.open({
            type: 2,
            title: '发布公告',
            area: ['90%', '90%'],
            maxmin: true,
            content: 'agent_notice_edit.php?act=add',
            end: function() {
                loadNotices();
            }
        });
    };

    // 加载数据
    loadNotices();
});
</script>

<style>
.notice-type-1 { color: #1e9fff; }
.notice-type-2 { color: #ff5722; }
.notice-type-3 { color: #ff9800; }
</style>