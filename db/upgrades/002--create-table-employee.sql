CREATE TABLE `tbl_employee` (
  `id` char(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'we will keep UUID v4 values',
  `first_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `last_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `dept_id` tinyint NOT NULL COMMENT 'index and foreign key needed',
  `salary` decimal(11,2) NOT NULL DEFAULT '0.00',
  `dob` date DEFAULT NULL COMMENT 'date of birth',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tbl_employee_dept_id_IDX` (`dept_id`) USING BTREE,
  KEY `tbl_employee_salary_IDX` (`salary`) USING BTREE,
  KEY `tbl_employee_last_name_IDX` (`last_name`) USING BTREE,
  KEY `tbl_employee_first_name_IDX` (`first_name`) USING BTREE,
  KEY `tbl_employee_dob_IDX` (`dob`) USING BTREE,
  CONSTRAINT `tbl_employee_FK` FOREIGN KEY (`dept_id`) REFERENCES `tbl_department` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
