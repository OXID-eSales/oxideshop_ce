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

# Copy records from oxobject2category for each shop into "temporary" table
INSERT IGNORE INTO `oxobject2category_tmp` (`OXID`, `OXSHOPID`, `OXOBJECTID`, `OXCATNID`, `OXPOS`, `OXTIME`, `OXTIMESTAMP`) SELECT MD5(CONCAT(`OXOBJECTID`, `OXCATNID`, '1')), '1', `OXOBJECTID`, `OXCATNID`, `OXPOS`, `OXTIME`, `OXTIMESTAMP` FROM `oxv_oxobject2category_1` WHERE `OXSHOPID` <> '1';

#
# Migrating oxobject2category
#

INSERT INTO `oxobject2category` SELECT * FROM `oxobject2category_tmp`;
