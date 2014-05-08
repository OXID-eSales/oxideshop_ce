#
# Drop indexes from oxobject2category
#

DROP INDEX `OXOBJECTID` ON `oxobject2category`;
DROP INDEX `OXPOS` ON `oxobject2category`;
DROP INDEX `OXMAINIDX` ON `oxobject2category`;
DROP INDEX `OXSHOPID` ON `oxobject2category`;
DROP INDEX `OXTIME` ON `oxobject2category`;

#
# Create "temporary" table to migrate oxobject2category_tmp
#

DROP TABLE IF EXISTS `oxobject2category_tmp`;
CREATE TABLE `oxobject2category_tmp` LIKE `oxobject2category`;
