-- MySQL dump 10.13  Distrib 5.7.44, for Linux (x86_64)
--
-- Host: localhost    Database: com
-- ------------------------------------------------------
-- Server version	5.7.44-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `realname` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:1=启用,0=禁用',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `last_login` timestamp NULL DEFAULT NULL COMMENT '最后登录时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `can_add_agent` tinyint(1) NOT NULL DEFAULT '1' COMMENT '可添加代理',
  `can_manage_level` tinyint(1) NOT NULL DEFAULT '1' COMMENT '可管理等级',
  `can_manage_kami_price` tinyint(1) NOT NULL DEFAULT '1' COMMENT '可管理卡密价格',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='管理员表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'123','$2y$10$TX2PLeh746oXWhol5Pcr2.hKtWhZ2KV0YWJoQUBsrAPFxS0C1A.la','123',1,'2026-02-06 18:08:36','2026-02-06 18:09:47','',1,1,1);
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agent`
--

DROP TABLE IF EXISTS `agent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '代理ID',
  `unique_id` varchar(20) DEFAULT NULL COMMENT '专属ID，用于充值',
  `username` varchar(50) NOT NULL COMMENT '代理账号',
  `password` varchar(255) NOT NULL COMMENT '代理密码',
  `name` varchar(100) NOT NULL COMMENT '代理名称',
  `contact` varchar(100) DEFAULT NULL COMMENT '联系方式',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `parent_id` int(11) DEFAULT '0' COMMENT '上级代理ID,0表示总后台直属',
  `level` int(2) NOT NULL DEFAULT '1' COMMENT '代理级别',
  `level_id` int(11) NOT NULL DEFAULT '1' COMMENT '等级ID',
  `cookies` varchar(255) DEFAULT NULL COMMENT '登录会话',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:0=禁用,1=启用',
  `register_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '注册类型：1=卡密注册，2=账号密码注册',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `last_login` timestamp NULL DEFAULT NULL COMMENT '最后登录时间',
  `can_del_kami` tinyint(1) NOT NULL DEFAULT '1' COMMENT '允许删除卡密',
  `can_del_user` tinyint(1) NOT NULL DEFAULT '1' COMMENT '允许删除用户',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `username` (`username`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE,
  KEY `level_id` (`level_id`),
  KEY `unique_id` (`unique_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='代理表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agent`
--

LOCK TABLES `agent` WRITE;
/*!40000 ALTER TABLE `agent` DISABLE KEYS */;
INSERT INTO `agent` VALUES (1,NULL,'1111','$2y$10$Yx8u3K63xudYVRMed/cTnOLT8dBC.AmIEOrJG0kF.ctMYbp04GxKy','1111',NULL,1000.00,0,5,1,'89acrmR0FnfkzqD4jeizaEVY47icd62Kjl5EYxbKm31DKozWm+PfOzthzepABN5y0CuA5+kymnBWnx1iY2NuHsDx',1,2,'2026-02-06 17:30:23','2026-02-07 01:39:45',1,1);
/*!40000 ALTER TABLE `agent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agent_daily_kami`
--

DROP TABLE IF EXISTS `agent_daily_kami`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agent_daily_kami` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL COMMENT '代理ID',
  `agent_level_id` int(11) NOT NULL COMMENT '等级ID',
  `free_kami_count` int(11) NOT NULL COMMENT '今日免费生成数量',
  `paid_kami_count` int(11) NOT NULL COMMENT '今日付费生成数量',
  `total_kami_count` int(11) NOT NULL COMMENT '今日总生成数量',
  `record_date` date NOT NULL COMMENT '记录日期',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agent_date` (`agent_id`,`record_date`),
  KEY `record_date` (`record_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='代理每日卡密生成记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agent_daily_kami`
--

LOCK TABLES `agent_daily_kami` WRITE;
/*!40000 ALTER TABLE `agent_daily_kami` DISABLE KEYS */;
/*!40000 ALTER TABLE `agent_daily_kami` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agent_level`
--

DROP TABLE IF EXISTS `agent_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agent_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '等级ID',
  `level_name` varchar(50) NOT NULL COMMENT '等级名称',
  `level_order` int(3) NOT NULL DEFAULT '0' COMMENT '等级排序，数字越大等级越高',
  `can_manage_user` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否拥有用户管理权限：0=否，1=是',
  `can_add_agent` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可添加下级代理：0=否，1=是',
  `max_sub_agents` int(11) NOT NULL DEFAULT '0' COMMENT '可添加下级代理最大数量，0=无限制',
  `can_generate_kami` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可生成卡密：0=否，1=是',
  `daily_free_kami` int(11) NOT NULL DEFAULT '0' COMMENT '每日免费卡密生成数量，0=不免费',
  `kami_discount` decimal(5,2) NOT NULL DEFAULT '1.00' COMMENT '卡密生成折扣，1.00=原价，0.50=5折',
  `initial_balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '初始余额',
  `can_set_discount` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可设置下级折扣：0=否，1=是',
  `can_set_free_kami` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否可设置下级免费卡密数：0=否，1=是',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：0=禁用，1=启用',
  PRIMARY KEY (`id`),
  UNIQUE KEY `level_name` (`level_name`),
  KEY `level_order` (`level_order`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='代理等级配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agent_level`
--

LOCK TABLES `agent_level` WRITE;
/*!40000 ALTER TABLE `agent_level` DISABLE KEYS */;
INSERT INTO `agent_level` VALUES (1,'一级代理',1,1,1,10,1,5,1.00,100.00,0,0,'2026-02-06 20:30:43',1),(2,'二级代理',2,1,1,5,1,3,0.90,50.00,0,0,'2026-02-06 20:30:43',1),(3,'三级代理',3,0,0,0,1,0,0.80,20.00,0,0,'2026-02-06 20:30:43',1),(4,'滚滚滚',1,1,1,0,1,0,1.00,0.00,0,0,'2026-02-06 20:31:28',1);
/*!40000 ALTER TABLE `agent_level` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agent_notice`
--

DROP TABLE IF EXISTS `agent_notice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agent_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL COMMENT '公告标题',
  `content` text NOT NULL COMMENT '公告内容',
  `notice_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公告类型：1=系统公告，2=活动通知，3=重要提醒',
  `priority` int(3) NOT NULL DEFAULT '0' COMMENT '优先级，数字越大越重要',
  `target_level` int(11) DEFAULT '0' COMMENT '目标等级ID，0=所有等级可见',
  `is_sticky` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否置顶：0=否，1=是',
  `read_count` int(11) NOT NULL DEFAULT '0' COMMENT '阅读次数',
  `publish_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '发布时间',
  `expire_time` timestamp NULL DEFAULT NULL COMMENT '过期时间，NULL=不过期',
  `created_by` varchar(50) NOT NULL COMMENT '创建人',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：0=禁用，1=启用',
  PRIMARY KEY (`id`),
  KEY `publish_time` (`publish_time`),
  KEY `state` (`state`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='代理系统公告表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agent_notice`
--

LOCK TABLES `agent_notice` WRITE;
/*!40000 ALTER TABLE `agent_notice` DISABLE KEYS */;
INSERT INTO `agent_notice` VALUES (1,'欢迎使用代理管理系统','欢迎使用新一代多级代理管理系统！<br><br>本系统支持：<br>✅ 多级代理管理<br>✅ 精细化权限控制<br>✅ 灵活的卡密价格设置<br>✅ 每日免费卡密生成<br>✅ 实时余额充值<br><br>如有问题请联系管理员。',1,100,0,1,0,'2026-02-06 20:30:43',NULL,'system',1);
/*!40000 ALTER TABLE `agent_notice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agent_register`
--

DROP TABLE IF EXISTS `agent_register`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agent_register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT '注册账号',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `name` varchar(100) NOT NULL COMMENT '代理名称',
  `contact` varchar(100) DEFAULT NULL COMMENT '联系方式',
  `register_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '注册类型：1=卡密注册，2=账号密码注册',
  `kami_code` varchar(128) DEFAULT NULL COMMENT '使用的卡密（卡密注册时）',
  `level_id` int(11) NOT NULL DEFAULT '1' COMMENT '申请等级ID',
  `parent_id` int(11) DEFAULT '0' COMMENT '上级代理ID，0=无上级',
  `remark` text COMMENT '申请备注',
  `register_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '注册时间',
  `register_ip` varchar(50) DEFAULT NULL COMMENT '注册IP',
  `audit_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '审核状态：0=待审核，1=已通过，2=已拒绝',
  `audit_time` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `audit_by` varchar(50) DEFAULT NULL COMMENT '审核人',
  `audit_reason` varchar(500) DEFAULT NULL COMMENT '拒绝原因',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `audit_status` (`audit_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='代理注册审核表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agent_register`
--

LOCK TABLES `agent_register` WRITE;
/*!40000 ALTER TABLE `agent_register` DISABLE KEYS */;
/*!40000 ALTER TABLE `agent_register` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_agent_access`
--

DROP TABLE IF EXISTS `app_agent_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_agent_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appcode` varchar(255) NOT NULL COMMENT '应用码',
  `agent_id` int(11) NOT NULL COMMENT '代理ID',
  `created_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '授权时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `appcode_agent` (`appcode`,`agent_id`) USING BTREE,
  KEY `appcode` (`appcode`) USING BTREE,
  KEY `agent_id` (`agent_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='应用代理访问权限表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_agent_access`
--

LOCK TABLES `app_agent_access` WRITE;
/*!40000 ALTER TABLE `app_agent_access` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_agent_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_server`
--

DROP TABLE IF EXISTS `app_server`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `serverip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_server`
--

LOCK TABLES `app_server` WRITE;
/*!40000 ALTER TABLE `app_server` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_server` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `application`
--

DROP TABLE IF EXISTS `application`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application` (
  `appid` int(11) NOT NULL AUTO_INCREMENT,
  `appcode` varchar(255) NOT NULL COMMENT 'appcode',
  `appname` varchar(255) NOT NULL,
  `serverip` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL COMMENT '属于user',
  `found_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `is_public` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否公开：1=所有代理可见，0=仅指定代理可见',
  PRIMARY KEY (`appid`) USING BTREE,
  UNIQUE KEY `appcode` (`appcode`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=56544 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `application`
--

LOCK TABLES `application` WRITE;
/*!40000 ALTER TABLE `application` DISABLE KEYS */;
/*!40000 ALTER TABLE `application` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `balance_recharge`
--

DROP TABLE IF EXISTS `balance_recharge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `balance_recharge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL COMMENT '代理ID',
  `agent_name` varchar(50) NOT NULL COMMENT '代理账号',
  `recharge_amount` decimal(10,2) NOT NULL COMMENT '充值金额',
  `balance_before` decimal(10,2) NOT NULL COMMENT '充值前余额',
  `balance_after` decimal(10,2) NOT NULL COMMENT '充值后余额',
  `recharge_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '充值类型：1=后台充值，2=自动充值',
  `payment_method` varchar(20) DEFAULT NULL COMMENT '支付方式',
  `order_no` varchar(50) DEFAULT NULL COMMENT '订单号',
  `remark` varchar(200) DEFAULT NULL COMMENT '备注',
  `created_by` varchar(50) NOT NULL COMMENT '操作人',
  `recharge_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '充值时间',
  PRIMARY KEY (`id`),
  KEY `agent_id` (`agent_id`),
  KEY `recharge_time` (`recharge_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='余额充值记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `balance_recharge`
--

LOCK TABLES `balance_recharge` WRITE;
/*!40000 ALTER TABLE `balance_recharge` DISABLE KEYS */;
/*!40000 ALTER TABLE `balance_recharge` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kami`
--

DROP TABLE IF EXISTS `kami`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kami` (
  `id` int(15) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `kami` varchar(128) NOT NULL COMMENT '卡密',
  `times` varchar(20) NOT NULL COMMENT '时长',
  `comment` varchar(20) NOT NULL COMMENT '备注',
  `found_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `host` varchar(255) NOT NULL COMMENT '站点',
  `sc_user` varchar(20) NOT NULL COMMENT '生成用户',
  `state` int(1) NOT NULL DEFAULT '0' COMMENT '状态:0=未使用,1=已使用',
  `use_date` timestamp NULL DEFAULT NULL COMMENT '使用时间',
  `username` varchar(25) DEFAULT NULL COMMENT '使用账号',
  `app` varchar(255) NOT NULL COMMENT '使用app软件',
  `end_date` timestamp NULL DEFAULT NULL COMMENT '到期时间',
  `ext` text COMMENT '拓展参数',
  `agent_id` int(11) DEFAULT '0' COMMENT '生成代理ID,0表示总后台生成',
  `agent_level_id` int(11) DEFAULT '0' COMMENT '生成代理的等级ID',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `kami` (`kami`) USING BTREE,
  KEY `agent_id` (`agent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='注册卡密';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kami`
--

LOCK TABLES `kami` WRITE;
/*!40000 ALTER TABLE `kami` DISABLE KEYS */;
/*!40000 ALTER TABLE `kami` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kami_price`
--

DROP TABLE IF EXISTS `kami_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kami_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kami_type` varchar(20) NOT NULL COMMENT '卡密类型：day-天卡，week-周卡，month-月卡，year-年卡，forever-永久卡',
  `type_name` varchar(30) NOT NULL COMMENT '类型显示名称',
  `days` int(11) NOT NULL DEFAULT '0' COMMENT '有效天数，永久卡为0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '生成价格（元）',
  `description` varchar(200) DEFAULT NULL COMMENT '描述说明',
  `sort_order` int(3) NOT NULL DEFAULT '0' COMMENT '排序',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：0=禁用，1=启用',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kami_type` (`kami_type`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='卡密价格配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kami_price`
--

LOCK TABLES `kami_price` WRITE;
/*!40000 ALTER TABLE `kami_price` DISABLE KEYS */;
INSERT INTO `kami_price` VALUES (1,'day','天卡',1,1.00,'有效期1天',1,1,'2026-02-06 20:30:43'),(2,'week','周卡',7,6.00,'有效期7天',2,1,'2026-02-06 20:30:43'),(3,'month','月卡',30,20.00,'有效期30天',3,1,'2026-02-06 20:30:43'),(4,'year','年卡',365,200.00,'有效期365天',4,1,'2026-02-06 20:30:43'),(5,'forever','永久卡',0,500.00,'永久有效',5,1,'2026-02-06 20:30:43');
/*!40000 ALTER TABLE `kami_price` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `operation` varchar(255) NOT NULL,
  `msg` varchar(255) NOT NULL,
  `operationdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `operationer` varchar(255) NOT NULL COMMENT '操作人',
  `ip` varchar(255) NOT NULL COMMENT 'ip',
  PRIMARY KEY (`logid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=275 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
INSERT INTO `log` VALUES (250,'登录日志','登录成功','2026-02-06 17:17:16','admin','27.213.28.225'),(251,'错误','[getuserall] 未找到用户数据','2026-02-06 17:17:38','admin','27.213.28.225'),(252,'添加代理','添加了代理: 1111','2026-02-06 17:30:23','admin','27.213.28.225'),(253,'错误','[getuserall] 未找到用户数据','2026-02-06 17:31:43','admin','27.213.28.225'),(254,'错误','[getuserall] 未找到用户数据','2026-02-06 17:32:42','admin','27.213.28.225'),(255,'代理登录','验证失败: 用户名或密码不正确或账号已被禁用！','2026-02-06 17:38:53','111','27.213.28.225'),(256,'编辑代理','编辑了代理ID: 1','2026-02-06 17:39:10','admin','27.213.28.225'),(257,'代理登录','验证失败: 用户名或密码不正确或账号已被禁用！','2026-02-06 17:39:17','111','27.213.28.225'),(258,'代理登录','登录成功','2026-02-06 17:39:45','1111','27.213.28.225'),(259,'添加管理员','添加了管理员: 123','2026-02-06 18:08:36','admin','27.213.28.225'),(260,'登录日志','验证失败: 请求验证失败，请刷新页面重试！！！','2026-02-06 18:09:12','admin','27.213.28.225'),(261,'登录日志','验证失败: 请求验证失败，请刷新页面重试！！！','2026-02-06 18:09:15','admin','27.213.28.225'),(262,'登录日志','验证失败: 请求验证失败，请刷新页面重试！！！','2026-02-06 18:09:26','admin','27.213.28.225'),(263,'登录日志','登录成功','2026-02-06 18:09:47','123','27.213.28.225'),(264,'登录日志','验证失败: 请求验证失败，请刷新页面重试！！！','2026-02-06 19:35:38','','27.213.28.225'),(265,'登录日志','登录成功','2026-02-06 19:35:59','admin','27.213.28.225'),(266,'错误','[getuserall] 未找到用户数据','2026-02-06 21:07:58','admin','27.213.28.225'),(267,'登录日志','验证失败: 请求验证失败，请刷新页面重试！！！','2026-02-06 21:34:22','','117.181.254.223'),(268,'登录日志','登录成功','2026-02-06 21:34:41','admin','117.181.254.223'),(269,'登录日志','验证失败: 请求验证失败，请刷新页面重试！！！','2026-02-07 01:02:38','','27.189.9.19'),(270,'登录日志','登录成功','2026-02-07 01:02:55','admin','27.189.9.19'),(271,'登录日志','登录成功','2026-02-07 01:29:12','admin','27.213.28.225'),(272,'错误','[getuserall] 未找到用户数据','2026-02-07 01:35:42','admin','27.213.28.225'),(273,'错误','[getuserall] 未找到用户数据','2026-02-07 01:39:59','admin','27.213.28.225'),(274,'错误','[getuserall] 未找到用户数据','2026-02-07 01:40:01','admin','27.213.28.225');
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_list`
--

DROP TABLE IF EXISTS `order_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_list` (
  `id` int(15) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `order_id` varchar(40) NOT NULL COMMENT '订单号',
  `type` int(1) NOT NULL DEFAULT '0' COMMENT '支付类型:1=微信,2=支付宝',
  `price` varchar(20) NOT NULL DEFAULT '' COMMENT '订单价格',
  `comment` varchar(100) NOT NULL COMMENT '备注信息',
  `state` int(1) NOT NULL COMMENT '支付状态:0=未支付,1=已支付,2=异常',
  `complete` int(1) NOT NULL COMMENT '是否完结:0=未完结,1=已完结',
  `found_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `pay_date` timestamp NULL DEFAULT NULL,
  `username` varchar(20) NOT NULL COMMENT '开通账户',
  `password` varchar(20) NOT NULL COMMENT '开通密码',
  `code` varchar(60) NOT NULL COMMENT '用户码',
  `tel` varchar(20) NOT NULL COMMENT '电话号码',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='订单列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_list`
--

LOCK TABLES `order_list` WRITE;
/*!40000 ALTER TABLE `order_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `server_list`
--

DROP TABLE IF EXISTS `server_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `server_list` (
  `id` int(15) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `ip` varchar(60) NOT NULL COMMENT '服务器ip',
  `serveruser` varchar(40) NOT NULL COMMENT 'ccproxy登录账号',
  `password` varchar(40) NOT NULL COMMENT 'ccproxy登录密码',
  `state` int(1) NOT NULL DEFAULT '1' COMMENT '是否可用:0=不可用,1=可用',
  `comment` varchar(200) NOT NULL COMMENT '备注',
  `found_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `certificate` varchar(200) DEFAULT NULL COMMENT '证书地址',
  `cport` int(5) NOT NULL COMMENT 'CCProxy端口',
  `username` varchar(255) NOT NULL COMMENT '所属账号',
  `applist` text COMMENT '应用大全',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `ip` (`ip`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='服务器列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `server_list`
--

LOCK TABLES `server_list` WRITE;
/*!40000 ALTER TABLE `server_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `server_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sub_admin`
--

DROP TABLE IF EXISTS `sub_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sub_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(20) NOT NULL COMMENT '用户名',
  `password` varchar(20) NOT NULL COMMENT '密码',
  `hostname` varchar(20) NOT NULL COMMENT '网站标题',
  `cookies` varchar(255) NOT NULL COMMENT ' 登录会话',
  `found_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `over_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '到期时间',
  `siteurl` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '站点违规',
  `pan` varchar(255) NOT NULL COMMENT '网盘',
  `wzgg` text NOT NULL COMMENT '网站公告',
  `kf` varchar(255) NOT NULL COMMENT '客服',
  `img` varchar(255) NOT NULL COMMENT '图片',
  `ggswitch` int(1) NOT NULL COMMENT '公告开关',
  `qx` int(1) NOT NULL COMMENT '权限',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `username` (`username`) USING BTREE,
  KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='普通管理员';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_admin`
--

LOCK TABLES `sub_admin` WRITE;
/*!40000 ALTER TABLE `sub_admin` DISABLE KEYS */;
INSERT INTO `sub_admin` VALUES (1,'admin','123456','一花端口','fdc1BPqGmDSKyqm0bBm6tQsUkUPscW7bnGnXoRNENF8Yj57HPJe1717qJKT2zYD3WLTUhgj3m29Uzs69ynwP+0zDxg','2022-01-12 19:24:34','2032-09-01 19:24:34','xn--6orp22g.com',1,'./assets/img/bj.jpg','注意<br>PHP版本最好设置为7.3<br>不然容易出现问题<br><div style=\"color:red\"><span>我是公告</span></div>我是公告','http://wpa.qq.com/msgrd?v=3&uin=487735913&site=qq&menu=yes','https://tse1-mm.cn.bing.net/th/id/OIP-C.tV58EvgJSzLG3iOTLzB85QHaEo?pid=ImgDet&rs=1',1,0);
/*!40000 ALTER TABLE `sub_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'com'
--

--
-- Dumping routines for database 'com'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-07  1:42:21
