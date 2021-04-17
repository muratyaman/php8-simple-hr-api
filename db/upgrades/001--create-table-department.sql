CREATE TABLE `tbl_department` (
  `id` tinyint NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tbl_department_name_IDX` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 
COMMENT='department table for HR, Marketing, IT, etc. and it would be rarely updated'
;
