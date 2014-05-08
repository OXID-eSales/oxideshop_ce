#
# Migrating oxobject2category
#

INSERT IGNORE INTO `oxobject2category` SELECT * FROM `oxobject2category_tmp`;
