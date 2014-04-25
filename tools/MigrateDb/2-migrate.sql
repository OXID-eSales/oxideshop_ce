#
# Migrating shop 1
#

INSERT INTO `oxdiscount2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxdiscount_1`;
INSERT INTO `oxcategories2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxcategories_1`;
INSERT INTO `oxattribute2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxattribute_1`;
INSERT INTO `oxlinks2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxlinks_1`;
INSERT INTO `oxvoucherseries2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxvoucherseries_1`;
INSERT INTO `oxmanufacturers2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxmanufacturers_1`;
INSERT INTO `oxnews2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxnews_1`;
INSERT INTO `oxselectlist2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxselectlist_1`;
INSERT INTO `oxwrapping2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxwrapping_1`;
INSERT INTO `oxdeliveryset2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxdeliveryset_1`;
INSERT INTO `oxdelivery2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxdelivery_1`;
INSERT INTO `oxvendor2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxvendor_1`;
INSERT INTO `oxarticles2shop` (`OXSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxarticles_1`;

# Duplicate records for each shop
INSERT INTO `oxobject2category` (`OXID`, `OXSHOPID`, `OXOBJECTID`, `OXCATNID`, `OXPOS`, `OXTIME`, `OXTIMESTAMP`) SELECT `OXID`, '1', `OXOBJECTID`, `OXCATNID`, `OXPOS`, `OXTIME`, `OXTIMESTAMP` FROM `oxv_oxobject2category_<shop_id>` WHERE `OXSHOPID` <> '1';
