#
# Migrating shop <shop_id>
#

INSERT INTO `oxdiscount2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '<shop_id>', `oxdiscount`.OXMAPID FROM `oxv_oxdiscount_<shop_id>` INNER JOIN `oxdiscount` ON `oxv_oxdiscount_<shop_id>`.`OXID` = `oxdiscount`.`OXID`;
INSERT INTO `oxcategories2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '<shop_id>', `oxcategories`.OXMAPID FROM `oxv_oxcategories_<shop_id>` INNER JOIN `oxcategories` ON `oxv_oxcategories_<shop_id>`.`OXID` = `oxcategories`.`OXID`;
INSERT INTO `oxattribute2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '<shop_id>', `oxattribute`.OXMAPID FROM `oxv_oxattribute_<shop_id>` INNER JOIN `oxattribute` ON `oxv_oxattribute_<shop_id>`.`OXID` = `oxattribute`.`OXID`;
INSERT INTO `oxlinks2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '<shop_id>', `oxlinks`.OXMAPID FROM `oxv_oxlinks_<shop_id>` INNER JOIN `oxlinks` ON `oxv_oxlinks_<shop_id>`.`OXID` = `oxlinks`.`OXID`;
INSERT INTO `oxvoucherseries2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '<shop_id>', `oxvoucherseries`.OXMAPID FROM `oxv_oxvoucherseries_<shop_id>` INNER JOIN `oxvoucherseries` ON `oxv_oxvoucherseries_<shop_id>`.`OXID` = `oxvoucherseries`.`OXID`;
INSERT INTO `oxmanufacturers2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '<shop_id>', `oxmanufacturers`.OXMAPID FROM `oxv_oxmanufacturers_<shop_id>` INNER JOIN `oxmanufacturers` ON `oxv_oxmanufacturers_<shop_id>`.`OXID` = `oxmanufacturers`.`OXID`;
INSERT INTO `oxnews2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '<shop_id>', `oxnews`.OXMAPID FROM `oxv_oxnews_<shop_id>` INNER JOIN `oxnews` ON `oxv_oxnews_<shop_id>`.`OXID` = `oxnews`.`OXID`;
INSERT INTO `oxselectlist2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '<shop_id>', `oxselectlist`.OXMAPID FROM `oxv_oxselectlist_<shop_id>` INNER JOIN `oxselectlist` ON `oxv_oxselectlist_<shop_id>`.`OXID` = `oxselectlist`.`OXID`;
INSERT INTO `oxwrapping2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '<shop_id>', `oxwrapping`.OXMAPID FROM `oxv_oxwrapping_<shop_id>` INNER JOIN `oxwrapping` ON `oxv_oxwrapping_<shop_id>`.`OXID` = `oxwrapping`.`OXID`;
INSERT INTO `oxdeliveryset2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '<shop_id>', `oxdeliveryset`.OXMAPID FROM `oxv_oxdeliveryset_<shop_id>` INNER JOIN `oxdeliveryset` ON `oxv_oxdeliveryset_<shop_id>`.`OXID` = `oxdeliveryset`.`OXID`;
INSERT INTO `oxdelivery2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '<shop_id>', `oxdelivery`.OXMAPID FROM `oxv_oxdelivery_<shop_id>` INNER JOIN `oxdelivery` ON `oxv_oxdelivery_<shop_id>`.`OXID` = `oxdelivery`.`OXID`;
INSERT INTO `oxvendor2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '<shop_id>', `oxvendor`.OXMAPID FROM `oxv_oxvendor_<shop_id>` INNER JOIN `oxvendor` ON `oxv_oxvendor_<shop_id>`.`OXID` = `oxvendor`.`OXID`;
INSERT INTO `oxarticles2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '<shop_id>', `oxarticles`.OXMAPID FROM `oxv_oxarticles_<shop_id>` INNER JOIN `oxarticles` ON `oxv_oxarticles_<shop_id>`.`OXID` = `oxarticles`.`OXID`;

# Copy records from oxobject2category for each shop into "temporary" table
INSERT IGNORE INTO `oxobject2category_tmp` (`OXID`, `OXSHOPID`, `OXOBJECTID`, `OXCATNID`, `OXPOS`, `OXTIME`, `OXTIMESTAMP`) SELECT MD5(CONCAT(`OXOBJECTID`, `OXCATNID`, '<shop_id>')), '<shop_id>', `OXOBJECTID`, `OXCATNID`, `OXPOS`, `OXTIME`, `OXTIMESTAMP` FROM `oxv_oxobject2category_<shop_id>` WHERE `OXSHOPID` <> '<shop_id>';

