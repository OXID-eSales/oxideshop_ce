#
# Drop OXSHOPINCL and OXSHOPEXCL columns from multi-shop tables.
#

DROP PROCEDURE IF EXISTS drop_columns;

DELIMITER //
CREATE PROCEDURE drop_columns(
  p_database VARCHAR(32),
  p_table    VARCHAR(32)
)
  BEGIN
    DECLARE column_to_drop VARCHAR(32);

    REPEAT
      SET column_to_drop = NULL;

      SELECT
        column_name
      FROM
        information_schema.columns
      WHERE table_schema = p_database
        AND TABLE_NAME = p_table
        AND (column_name LIKE 'OXSHOPINCL%' OR column_name LIKE 'OXSHOPEXCL%')
      LIMIT 1
      INTO column_to_drop;

      IF column_to_drop IS NOT NULL THEN
        SET @drop_column = concat('ALTER TABLE `', p_database, '`.`', p_table, '` DROP COLUMN `', column_to_drop, '`');
        PREPARE stmt FROM @drop_column;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
      END IF;

    UNTIL column_to_drop IS NULL
    END REPEAT;
  END
//
DELIMITER ;

CALL drop_columns(DATABASE(), 'oxarticles');
CALL drop_columns(DATABASE(), 'oxattribute');
CALL drop_columns(DATABASE(), 'oxcategories');
CALL drop_columns(DATABASE(), 'oxdelivery');
CALL drop_columns(DATABASE(), 'oxdeliveryset');
CALL drop_columns(DATABASE(), 'oxdiscount');
CALL drop_columns(DATABASE(), 'oxlinks');
CALL drop_columns(DATABASE(), 'oxmanufacturers');
CALL drop_columns(DATABASE(), 'oxnews');
CALL drop_columns(DATABASE(), 'oxvoucherseries');
CALL drop_columns(DATABASE(), 'oxselectlist');
CALL drop_columns(DATABASE(), 'oxwrapping');
CALL drop_columns(DATABASE(), 'oxvendor');
CALL drop_columns(DATABASE(), 'oxobject2category');

DROP PROCEDURE drop_columns;
