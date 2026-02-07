-- 修复代理表密码字段长度问题
-- password_hash() 生成的密码长度为60字符，需要将字段长度从50改为255

ALTER TABLE `agent` MODIFY COLUMN `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '代理密码';

