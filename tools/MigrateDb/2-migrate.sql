
#Migrating shop 1

INSERT INTO `oxdiscount2shop` (`OXMAPSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxdiscount_1`;
INSERT INTO `oxcategories2shop` (`OXMAPSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxcategories_1`;
INSERT INTO `oxattribute2shop` (`OXMAPSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxattribute_1`;
INSERT INTO `oxlinks2shop` (`OXMAPSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxlinks_1`;
INSERT INTO `oxvoucherseries2shop` (`OXMAPSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxvoucherseries_1`;
INSERT INTO `oxmanufacturers2shop` (`OXMAPSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxmanufacturers_1`;
INSERT INTO `oxnews2shop` (`OXMAPSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxnews_1`;
INSERT INTO `oxselectlist2shop` (`OXMAPSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxselectlist_1`;
INSERT INTO `oxwrapping2shop` (`OXMAPSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxwrapping_1`;
INSERT INTO `oxdeliveryset2shop` (`OXMAPSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxdeliveryset_1`;
INSERT INTO `oxdelivery2shop` (`OXMAPSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxdelivery_1`;
INSERT INTO `oxvendor2shop` (`OXMAPSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxvendor_1`;
INSERT INTO `oxobject2category2shop` (`OXMAPSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxobject2category_1`;
INSERT INTO `oxarticles2shop` (`OXMAPSHOPID`, `OXMAPOBJECTID`) SELECT '1', OXMAPID FROM `oxv_oxarticles_1`;
