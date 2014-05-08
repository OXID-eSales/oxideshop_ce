SUBSHOP MAPPING MIGRATION SCRIPT

This is a SQL generator for migrating old subshop inheritance data to the new subshop mapping solution.

IMPORTANT:
- Update your data BEFORE source code is updated.
  This is important as at point 2. (see bellow) you need to regenerate the shop views using the old view generation logics.

Basically this dir includes:
- Data base structure update script 1 (everything sans indices).
- Data base inheritance data migration script SQL generator.
- Data base structure update script 2 (adding missing indices).

How to update your db:

1. Execute 1-updateDb.sql SQL.
   This file adds 14 mapping tables (oxarticle2shop, oxcategories2shop,...), but skips indices.
   Also OXMAPID fields are added to original tables (oxarticles, ...)

2. Regenerate the shop views from subshop admin area
   This steps ensures that newly created OXMAPID fields are included into all subshop views.

3. Edit updateSqlGenerator.php file and set $NUMBER_OF_SUBSHOPS constant to required number of subshops.

4. Run updateSqlGenerator.php from command line:
   >php updateSqlGenerator.php
   After successful script execution 2-migrate.sql migration SQL is generated for all shops.

5. Run 2-pre-migration.sql
   This script drops indexes from oxobject2category and creates "temporary" table for migration.

6. Run 3-migrate.sql
   This script creates and inserts all mapping data to the newly created mapping tables and updates required data in
   table oxobject2category.

7. Run 4-post-migration.sql
   This script drops "temporary" table oxobject2category_tmp.

8. (OPTIONAL) Run 4_2-OPTIONAL-drop-columns.sql
   This script drops OXSHOPINCL and OXSHOPEXCL columns from multi-shop tables.
   IMPORTANT! All old "mapping" data will be lost, do not run it if you want to keep an old "mapping" data.

9. Run 5-addIndices.sql
   This script add missing indices after required data has been inserted.

After the steps are complete you have full mapping tables with included mapping data.
Next you can update the source code, clean tmp dirs, regenerated the db views etc.

Good luck with that!