SUBSHOP MAPPING MIGRATION SCRIPT

This is a SQL generator for migrating old subshop inheritance data to the new subshop mapping solution.

Basically this dir includes:
- Data base structure update script 1 (everything sans indices).
- Data base inheritance data migration script SQL generator.
- Data base structure update script 2 (adding missing indices).

How to update your db:

1. Execute 1-updateDb.sql SQL.
   This file adds 14 mapping tables (oxarticle2shop, oxcategories2shop,...), but skips indices.
   Also OXMAPID fields are added to original tables (oxarticles, ...)

2. Edit updateSqlGenerator.php file and set $NUMBER_OF_SUBSHOPS constant to required number of subshops.

3. Run updateSqlGenerator.php from command line:
   >php updateSqlGenerator.php
   After successful script execution 3-migrate.sql migration SQL is generated for all shops.

4. Run 2-pre-migration.sql
   This script drops indexes from oxobject2category and creates "temporary" table for migration.

5. Run 3-migrate.sql
   This script creates and inserts all mapping data to the newly created mapping tables and updates required data in
   table oxobject2category.

6. Run 4-post-migration.sql
   This script drops "temporary" table oxobject2category_tmp.

7. (OPTIONAL) Run 4_2-OPTIONAL-drop-columns.sql
   This script drops OXSHOPINCL and OXSHOPEXCL columns from multi-shop tables.
   IMPORTANT! All old "mapping" data will be lost, do not run it if you want to keep an old "mapping" data.

8. Run 5-addIndices.sql
   This script add missing indices after required data has been inserted.

After the steps are complete you have full mapping tables with included mapping data.
Next you can update the source code, clean tmp dirs, regenerated the db views etc.

Good luck with that!