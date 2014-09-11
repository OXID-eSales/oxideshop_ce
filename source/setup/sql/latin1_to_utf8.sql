ALTER TABLE `oxattribute` MODIFY `OXTITLE` varchar(128) NOT NULL default '' COMMENT 'Title (multilanguage)';
ALTER TABLE `oxattribute` MODIFY `OXTITLE_1` varchar(128) NOT NULL default '';
ALTER TABLE `oxattribute` MODIFY `OXTITLE_2` varchar(128) NOT NULL default '';
ALTER TABLE `oxattribute` MODIFY `OXTITLE_3` varchar(128) NOT NULL default '';

ALTER TABLE `oxcountry` MODIFY `OXTITLE` varchar(128) NOT NULL default '' COMMENT 'Title (multilanguage)';
ALTER TABLE `oxcountry` MODIFY `OXSHORTDESC` varchar(128) NOT NULL default '' COMMENT 'Short description (multilanguage)';
ALTER TABLE `oxcountry` MODIFY `OXLONGDESC` varchar(255) NOT NULL default '' COMMENT 'Long description (multilanguage)';
ALTER TABLE `oxcountry` MODIFY `OXTITLE_1` varchar(128) NOT NULL default '';
ALTER TABLE `oxcountry` MODIFY `OXTITLE_2` varchar(128) NOT NULL default '';
ALTER TABLE `oxcountry` MODIFY `OXTITLE_3` varchar(128) NOT NULL default '';
ALTER TABLE `oxcountry` MODIFY `OXSHORTDESC_1` varchar(128) NOT NULL default '';
ALTER TABLE `oxcountry` MODIFY `OXSHORTDESC_2` varchar(128) NOT NULL default '';
ALTER TABLE `oxcountry` MODIFY `OXSHORTDESC_3` varchar(128) NOT NULL default '';
ALTER TABLE `oxcountry` MODIFY `OXLONGDESC_1` varchar(255) NOT NULL;
ALTER TABLE `oxcountry` MODIFY `OXLONGDESC_2` varchar(255) NOT NULL;
ALTER TABLE `oxcountry` MODIFY `OXLONGDESC_3` varchar(255) NOT NULL;

ALTER TABLE `oxgroups` MODIFY `OXTITLE` varchar(128) NOT NULL default '' COMMENT 'Title (multilanguage)';
ALTER TABLE `oxgroups` MODIFY `OXTITLE_1` varchar(128) NOT NULL default '';
ALTER TABLE `oxgroups` MODIFY `OXTITLE_2` varchar(128) NOT NULL default '';
ALTER TABLE `oxgroups` MODIFY `OXTITLE_3` varchar(128) NOT NULL default '';

ALTER TABLE `oxobject2attribute` MODIFY `OXVALUE_1` varchar(255) NOT NULL default '';
ALTER TABLE `oxobject2attribute` MODIFY `OXVALUE_2` varchar(255) NOT NULL default '';
ALTER TABLE `oxobject2attribute` MODIFY `OXVALUE_3` varchar(255) NOT NULL default '';

ALTER TABLE `oxorder` MODIFY `OXCURRENCY` varchar(32) NOT NULL default '' COMMENT 'Currency';
ALTER TABLE `oxorder` MODIFY `OXFOLDER` varchar(32) NOT NULL default '' COMMENT 'Folder: ORDERFOLDER_FINISHED, ORDERFOLDER_NEW, ORDERFOLDER_PROBLEMS';

ALTER TABLE `oxorderarticles` MODIFY `OXFOLDER` varchar(32) NOT NULL default '' COMMENT 'Folder: ORDERFOLDER_FINISHED, ORDERFOLDER_NEW, ORDERFOLDER_PROBLEMS';
ALTER TABLE `oxorderarticles` MODIFY `OXSUBCLASS` varchar(32) NOT NULL default '' COMMENT 'Subclass';


ALTER TABLE `oxshops` MODIFY `OXDEFCURRENCY` varchar(32) NOT NULL default '' COMMENT 'Default currency';

ALTER TABLE `oxuser` MODIFY `OXUPDATEKEY` varchar( 32 ) NOT NULL default '' COMMENT 'Update key';

ALTER TABLE `oxactions` MODIFY `OXTITLE` varchar(128) NOT NULL default '' COMMENT 'Title (multilanguage)';
ALTER TABLE `oxactions` MODIFY `OXTITLE_1` varchar(128) NOT NULL default '';
ALTER TABLE `oxactions` MODIFY `OXTITLE_2` varchar(128) NOT NULL default '';
ALTER TABLE `oxactions` MODIFY `OXTITLE_3` varchar(128) NOT NULL default '';

ALTER TABLE `oxnewssubscribed` MODIFY `OXSAL` varchar(64) NOT NULL default '' COMMENT 'User title prefix (Mr/Mrs)';

ALTER TABLE `oxvendor` MODIFY `OXICON` varchar(128) NOT NULL default '' COMMENT 'Icon filename';
ALTER TABLE `oxvendor` MODIFY `OXTITLE` varchar(255) NOT NULL default '' COMMENT 'Title (multilanguage)';
ALTER TABLE `oxvendor` MODIFY `OXSHORTDESC` varchar(255) NOT NULL default '' COMMENT 'Short description (multilanguage)';
ALTER TABLE `oxvendor` MODIFY `OXTITLE_1` varchar(255) NOT NULL default '';
ALTER TABLE `oxvendor` MODIFY `OXSHORTDESC_1` varchar(255) NOT NULL default '';
ALTER TABLE `oxvendor` MODIFY `OXTITLE_2` varchar(255) NOT NULL default '';
ALTER TABLE `oxvendor` MODIFY `OXSHORTDESC_2` varchar(255) NOT NULL default '';
ALTER TABLE `oxvendor` MODIFY `OXTITLE_3` varchar(255) NOT NULL default '';
ALTER TABLE `oxvendor` MODIFY `OXSHORTDESC_3` varchar(255) NOT NULL default '';

ALTER TABLE `oxmanufacturers` MODIFY `OXICON` varchar(128) NOT NULL default '' COMMENT 'Icon filename';
ALTER TABLE `oxmanufacturers` MODIFY `OXTITLE` varchar(255) NOT NULL default '' COMMENT 'Title (multilanguage)';
ALTER TABLE `oxmanufacturers` MODIFY `OXSHORTDESC` varchar(255) NOT NULL default '' COMMENT 'Short description (multilanguage)';
ALTER TABLE `oxmanufacturers` MODIFY `OXTITLE_1` varchar(255) NOT NULL default '';
ALTER TABLE `oxmanufacturers` MODIFY `OXSHORTDESC_1` varchar(255) NOT NULL default '';
ALTER TABLE `oxmanufacturers` MODIFY `OXTITLE_2` varchar(255) NOT NULL default '';
ALTER TABLE `oxmanufacturers` MODIFY `OXSHORTDESC_2` varchar(255) NOT NULL default '';
ALTER TABLE `oxmanufacturers` MODIFY `OXTITLE_3` varchar(255) NOT NULL default '';
ALTER TABLE `oxmanufacturers` MODIFY `OXSHORTDESC_3` varchar(255) NOT NULL default '';