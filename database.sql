CREATE TABLE table_show (
  id             INT PRIMARY KEY AUTO_INCREMENT
  COMMENT '表注释',
  table_name     CHAR(50) COMMENT '表名',
  table_row      CHAR(50) COMMENT '字段名称',
  table_row_name CHAR(50) COMMENT '字段名注释',
  flag           INT COMMENT '是1否0前台显示',
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;


CREATE TABLE menu (
  `id`               INT(11)     NOT NULL AUTO_INCREMENT
  COMMENT '左侧菜单',
  `f_moduleid`       CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL
  COMMENT 'id',
  `f_parentid`       CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL
  COMMENT '父亲',
  `f_encode`         CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL
  COMMENT 'SysManage',
  `f_fullname`       CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL
  COMMENT '名称',
  `f_icon`           CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL
  COMMENT '图标',
  `f_urladdress`     CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL,
  `f_target`         CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL
  COMMENT 'expand',
  `f_ismenu`         CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL
  COMMENT '0',
  `f_allowexpand`    CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL
  COMMENT '1',
  `f_ispublic`       CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL
  COMMENT '0',
  `f_allowedit`      CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL,
  `f_allowdelete`    CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL,
  `f_sortcode`       CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL
  COMMENT '1',
  `f_deletemark`     CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL
  COMMENT '0',
  `f_enabledmark`    CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL,
  `f_description`    CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL,
  `f_createdate`     CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL,
  `f_createuserid`   CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL,
  `f_createusername` CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL,
  `f_modifydate`     DATETIME(0) NULL     DEFAULT NULL,
  `f_modifyuserid`   CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL,
  `f_modifyusername` CHAR(80) CHARACTER SET utf8
  COLLATE utf8_general_ci        NULL     DEFAULT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE role (
  id        INT PRIMARY KEY AUTO_INCREMENT
  COMMENT '角色',
  role_name CHAR(20) COMMENT '角色名称'

)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE role_menu (
  id          INT PRIMARY KEY AUTO_INCREMENT
  COMMENT '角色关联',
  role_id     INT COMMENT '角色id',
  role_name   CHAR(20) COMMENT '角色名称',
  permis_id   INT COMMENT '',
  permis_name CHAR(40) COMMENT '权限名称'
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE permis (
  id          INT PRIMARY KEY AUTO_INCREMENT
  COMMENT '权限关联',
  user_id     INT COMMENT '用户ID',
  user_name   CHAR(40) COMMENT '用户名',
  permis_id   INT COMMENT '',
  permis_name CHAR(40) COMMENT '权限名称'
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
