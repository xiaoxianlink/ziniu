
-- ----------------------------
-- Table structure for `cw_services_dyna`
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
) ENGINE=InnoDB AUTO_INCREMENT=3475 DEFAULT CHARSET=gbk ROW_FORMAT=DYNAMIC COMMENT='服务商定价表';


INSERT INTO `cw_auth_access` VALUES (2, 'admin_new/fuwu/dynamicdingjia', 'admin_url');