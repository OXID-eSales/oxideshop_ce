#
# Drop indexes from oxobject2category
#

DROP INDEX `PRIMARY` ON `oxobject2category`;
DROP INDEX `OXOBJECTID` ON `oxobject2category`;
DROP INDEX `OXPOS` ON `oxobject2category`;
DROP INDEX `OXMAINIDX` ON `oxobject2category`;
DROP INDEX `OXSHOPID` ON `oxobject2category`;
DROP INDEX `OXTIME` ON `oxobject2category`;

#
# Create "temporary" table to migrate oxobject2category_tmp
#

DROP TABLE IF EXISTS `oxobject2category_tmp`;
CREATE TABLE `oxobject2category_tmp` (
  `OXID`        CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `OXSHOPID`    INT(11) NOT NULL DEFAULT '1',
  `OXOBJECTID`  CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `OXCATNID`    CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `OXPOS`       INT(11) NOT NULL DEFAULT '0',
  `OXTIME`      INT(11) DEFAULT 0 NOT NULL,
  `OXTIMESTAMP` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = MyISAM;
