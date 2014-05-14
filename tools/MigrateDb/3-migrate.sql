#
# Migrating shop 1
#

INSERT INTO `oxdiscount2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', `oxdiscount`.`OXMAPID` FROM `oxv_oxdiscount_1` INNER JOIN `oxdiscount` ON `oxv_oxdiscount_1`.`OXID` = `oxdiscount`.`OXID`;
INSERT INTO `oxcategories2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', `oxcategories`.`OXMAPID` FROM `oxv_oxcategories_1` INNER JOIN `oxcategories` ON `oxv_oxcategories_1`.`OXID` = `oxcategories`.`OXID`;
INSERT INTO `oxattribute2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', `oxattribute`.`OXMAPID` FROM `oxv_oxattribute_1` INNER JOIN `oxattribute` ON `oxv_oxattribute_1`.`OXID` = `oxattribute`.`OXID`;
INSERT INTO `oxlinks2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', `oxlinks`.`OXMAPID` FROM `oxv_oxlinks_1` INNER JOIN `oxlinks` ON `oxv_oxlinks_1`.`OXID` = `oxlinks`.`OXID`;
INSERT INTO `oxvoucherseries2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', `oxvoucherseries`.`OXMAPID` FROM `oxv_oxvoucherseries_1` INNER JOIN `oxvoucherseries` ON `oxv_oxvoucherseries_1`.`OXID` = `oxvoucherseries`.`OXID`;
INSERT INTO `oxmanufacturers2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', `oxmanufacturers`.`OXMAPID` FROM `oxv_oxmanufacturers_1` INNER JOIN `oxmanufacturers` ON `oxv_oxmanufacturers_1`.`OXID` = `oxmanufacturers`.`OXID`;
INSERT INTO `oxnews2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', `oxnews`.`OXMAPID` FROM `oxv_oxnews_1` INNER JOIN `oxnews` ON `oxv_oxnews_1`.`OXID` = `oxnews`.`OXID`;
INSERT INTO `oxselectlist2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', `oxselectlist`.`OXMAPID` FROM `oxv_oxselectlist_1` INNER JOIN `oxselectlist` ON `oxv_oxselectlist_1`.`OXID` = `oxselectlist`.`OXID`;
INSERT INTO `oxwrapping2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', `oxwrapping`.`OXMAPID` FROM `oxv_oxwrapping_1` INNER JOIN `oxwrapping` ON `oxv_oxwrapping_1`.`OXID` = `oxwrapping`.`OXID`;
INSERT INTO `oxdeliveryset2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', `oxdeliveryset`.`OXMAPID` FROM `oxv_oxdeliveryset_1` INNER JOIN `oxdeliveryset` ON `oxv_oxdeliveryset_1`.`OXID` = `oxdeliveryset`.`OXID`;
INSERT INTO `oxdelivery2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', `oxdelivery`.`OXMAPID` FROM `oxv_oxdelivery_1` INNER JOIN `oxdelivery` ON `oxv_oxdelivery_1`.`OXID` = `oxdelivery`.`OXID`;
INSERT INTO `oxvendor2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', `oxvendor`.`OXMAPID` FROM `oxv_oxvendor_1` INNER JOIN `oxvendor` ON `oxv_oxvendor_1`.`OXID` = `oxvendor`.`OXID`;
INSERT INTO `oxarticles2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', `oxarticles`.`OXMAPID` FROM `oxv_oxarticles_1` INNER JOIN `oxarticles` ON `oxv_oxarticles_1`.`OXID` = `oxarticles`.`OXID`;

# Copy records from oxobject2category for each shop into "temporary" table
INSERT INTO `oxobject2category_tmp` (`OXID`, `OXSHOPID`, `OXOBJECTID`, `OXCATNID`, `OXPOS`, `OXTIME`, `OXTIMESTAMP`) SELECT MD5(CONCAT(`OXOBJECTID`, `OXCATNID`, '1')), '1', `OXOBJECTID`, `OXCATNID`, `OXPOS`, `OXTIME`, `OXTIMESTAMP` FROM `oxv_oxobject2category_1` WHERE `OXSHOPID` <> '1';

#
# Migrating oxobject2category
#

INSERT INTO `oxobject2category` SELECT * FROM `oxobject2category_tmp`;
