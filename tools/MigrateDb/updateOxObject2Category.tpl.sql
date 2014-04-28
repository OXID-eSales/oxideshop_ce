#
# Migrating oxobject2category
#

INSERT INTO `oxobject2category` SELECT * FROM `oxobject2category_tmp`;
