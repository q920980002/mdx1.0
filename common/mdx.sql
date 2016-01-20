DROP table if exists `mdx_passport`;
CREATE TABLE `mdx_passport` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`phone` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册手机号',
`email` varchar(100) NOT NULL DEFAULT '' COMMENT '关联邮箱',
`password` char(24) NOT NULL DEFAULT '' COMMENT '登录密码',
`second_password` char(24) NOT NULL DEFAULT '' COMMENT '二级密码',
`source` varchar(32) NOT NULL DEFAULT '0' COMMENT '用户来源，1:weixin，2:web，3:ios8.1，4:facebook',
`ip` char(15) NOT NULL DEFAULT '' COMMENT '注册ip',
`status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '账号状态：1正常，0锁定，－1注销',
`log_msg` varchar(100) NOT NULL DEFAULT '' COMMENT '登陆后提示信息，防止钓鱼网站',
`last_time` int(11) NOT NULL DEFAULT '0' COMMENT '上次登录时间',
`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '账号创建时间',
`update_time` int(11) NOT NULL DEFAULT '0' COMMENT '用户信息最后更新时间，更新前需入快照表',
PRIMARY KEY (`id`),
UNIQUE KEY `uk_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户登录信息表';


DROP table if exists `mdx_account`;
CREATE TABLE `mdx_account` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`passport_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户passport id，关联passport库',
`balance` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '账户资金',
`frozen` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '冻结金额',
`income` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总收入，包括充值、回款、货基收益等',
`expend` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总支出，包括提现、投资',
`profit` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总收益，包括回款、货基收益等但不包括充值',
`create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
`update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间，更新前需入快照表',
PRIMARY KEY (`id`),
UNIQUE KEY `uk_user_id` (`passport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户财务表';


DROP TABLE IF EXISTS `mdx_account_auth`;
CREATE TABLE `mdx_account_auth` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户account id',
  `id_number` varchar(50) NOT NULL DEFAULT '' COMMENT '身份证号或者机构id，需加密',
  `name` varchar(100) DEFAULT '' COMMENT '姓名，需加密',
  `sex` tinyint(1) DEFAULT '1' COMMENT '性别 1:男 2：女 0：未知',
  `auth_provider` varchar(32) NOT NULL DEFAULT '' COMMENT '认证方',
  `auth_user_number` varchar(50) NOT NULL COMMENT '汇付用户编号',
  `auth_user_pwd` varchar(32) NOT NULL COMMENT '汇付操作密码（需加密）',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '认证状态：1通过，0未认证，－1认证失败',
  `auth_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '认证通过时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_account_id` (`account_id`),
  UNIQUE KEY `uk_id_number` (`id_number`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='用户认证信息表';


DROP TABLE IF EXISTS `mdx_pnrpay_error`;
CREATE TABLE `mdx_pnrpay_error` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(4) NOT NULL COMMENT '用户id',
  `error_msg` varchar(100) NOT NULL COMMENT '错误信息',
  `org_data` text NOT NULL COMMENT '原数据',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;