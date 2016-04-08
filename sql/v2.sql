
-- ----------------------------
-- dynamic pricing
-- ----------------------------
DROP TABLE IF EXISTS `cw_services_dyna`;
CREATE TABLE `cw_services_dyna` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `services_id` varchar(32) NOT NULL COMMENT '服务商id',
  `code` int(11) NOT NULL COMMENT '城市code',
  `point_fee` int(11) NOT NULL COMMENT '违章代码id',
  `fee` int(10) NOT NULL COMMENT '定价',
  `create_time` int(11) DEFAULT NULL COMMENT '定价时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk ROW_FORMAT=DYNAMIC COMMENT='服务商定价表';

ALTER TABLE `cw_order`
ADD COLUMN `so_type`  int(2) NULL DEFAULT 1 AFTER `so_id`;

-- ----------------------------
-- mobile app
-- ----------------------------
ALTER TABLE `cw_services` ADD `nickname` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '昵称' ;

ALTER TABLE `cw_message` CHANGE `msg_type` `msg_type` INT(11) NULL DEFAULT NULL COMMENT '消息类型；1：违章提醒，2：新违章提醒，3：小仙提醒，4：小仙账务';

ALTER TABLE `cw_message` ADD `content` VARCHAR(128) NULL COMMENT '消息内容' ;

ALTER TABLE `cw_message` ADD `is_readed` INT(10) NULL COMMENT '消息状态：0未读，1已读' ;

ALTER TABLE `cw_message` CHANGE `msg_type` `msg_type` INT(11) NULL DEFAULT NULL COMMENT '消息类型；1：违章提醒，2：新违章提醒，3：小仙提醒，4：小仙账务，5：客服小仙';

ALTER TABLE `cw_message` ADD `tixing_type` INT(10) NULL COMMENT '小仙提醒类型，当msg_type=3时有效' AFTER `msg_type`;

ALTER TABLE `cw_message` CHANGE `tixing_type` `tixing_type` INT(10) NULL COMMENT '小仙提醒类型，当msg_type=3时有效。1开始接单状态更新提醒，2系统升级提醒，3新单推送提醒，4新单即将过期提醒，5接单处理即将过期提醒，6接单成功提醒';

ALTER TABLE `cw_message` ADD `zhangwu_type` INT(10) NULL COMMENT '小仙账务类型，当msg_type=4时有效' AFTER `tixing_type`;

ALTER TABLE `cw_message` CHANGE `zhangwu_type` `zhangwu_type` INT(10) NULL COMMENT '小仙账务类型，当type=4时有效。1可提现金额变动提醒，2账户金额变动提醒，3提现申请提醒，4提现到账提醒';

ALTER TABLE `cw_message` ADD `icon` VARCHAR(32) NULL COMMENT '图片' ;

ALTER TABLE `cw_message` CHANGE `tixing_type` `tixing_type` INT(10) NULL COMMENT '小仙提醒类型，当msg_type=3时有效。1开始接单状态更新提醒，2新订单即将过期提醒，3接单处理即将过期提醒，4新订单提醒，5新单超时取消提醒，6接单超时取消提醒，7订单结算提醒，8帮助与规则更新';

ALTER TABLE `cw_message` CHANGE `zhangwu_type` `zhangwu_type` INT(10) NULL COMMENT '小仙账务类型，当ty1账户余额变动提醒，2可提现金额变动提醒，3绑定银行卡/解绑银行卡，4提现操作';

CREATE TABLE `cw_yinhang` ( `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '编号id 自增长主键' , `bank_name` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '银行名称' , `bank_img` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '缩略图' , `state` INT(11) NOT NULL COMMENT '是否使用：0使用，1不使用' , `sort` INT(11) NOT NULL COMMENT '排序' , PRIMARY KEY (`id`) );

CREATE TABLE `cw_duanxin` ( `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '自增长主键' , `phone` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '手机号码' , `code` VARCHAR(32) CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL COMMENT '短信验证码' , `expires_time` DATETIME NOT NULL COMMENT '短信过期时间' , `create_time` DATETIME NOT NULL COMMENT '创建时间' , PRIMARY KEY (`id`) );

CREATE TABLE `cw_secret` ( `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '自增长主键' , `username` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '登录账户' , `secret` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '秘钥' , `clientUUID` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '设备id 用于区分每台设备' , `user_agent` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'User-Agent' , `expires_time` DATETIME NOT NULL COMMENT '秘钥过期时间' , `update_time` DATETIME NOT NULL COMMENT '秘钥更新时间' , PRIMARY KEY (`id`) ) COMMENT = '秘钥记录';

ALTER TABLE `cw_bank` ADD `yh_id` INT(11) NULL COMMENT '银行id' ;

-- ----------------------------
-- scan by state of car
-- ----------------------------
ALTER TABLE `cw_car`
ADD COLUMN `scan_state`  int(2) NULL DEFAULT 1 AFTER `unsubscribe_time`,
ADD COLUMN `scan_state_desc`  varchar(256) NULL AFTER `scan_state`,
ADD COLUMN `scan_state_time`  int(11) NULL AFTER `scan_state_desc`,
ADD COLUMN `scan_stop_query`  int(11) NULL AFTER `scan_state_time`;

-- ----------------------------
-- bizapi
-- ----------------------------
CREATE TABLE `cw_bizapi` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`app_domain`  varchar(256) NOT NULL ,
`app_id`  varchar(256) NOT NULL ,
`app_key`  varchar(256) NOT NULL ,
`create_time`  int(11) NOT NULL ,
`expiration_time`  int(11) NOT NULL ,
`state`  int(2) NOT NULL DEFAULT 1 ,
`last_time`  int(11) NULL ,
PRIMARY KEY (`id`)
)
;
ALTER TABLE `cw_car`
ADD COLUMN `channel_key`  varchar(128) NULL AFTER `channel`;

ALTER TABLE `cw_user`
ADD COLUMN `channel_key`  varchar(128) NULL AFTER `channel`;

insert into cw_menu (parentid, app, model, action, type, status, name, listorder) values (165, 'Admin_new', 'Bizapi',	'bizapi_list', 1, 1, '商用接口管理', 5);

insert into cw_auth_rule (module, type, name, title, status) values ('Admin', 'admin_url', 'admin_new/bizapi/bizapi_list', '商用接口管理', 1);	


