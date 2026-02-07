<?php
/*
 * 代理登录验证码
 */
session_start();
include_once '../includes/ValidateCode.class.php';

$_vc = new ValidateCode();
$_vc->doimg();
$_SESSION['xx_session_code'] = $_vc->getCode();

