# Database scripts

# upgrades/

* use the database you created e.g. `USE hrdb;`

* they should be granular (include one DDL command)
  e.g. do not create multiple tables
  esp. in database systems which do **not** support transactions for DDL commands

# downgrades/

* each upgrade script should have an associated downgrade script:
  e.g. `001--abc.sql` ==> `001--xyz.sql` using first 3 digits for example.
* upgrade/downgrade manager should run keep audit trail of upgrades used
  e.g. in a table `migrations` (common name)

# data/

* database seeding (creating initial data set) is usually useful to improve development time

* if there are crucial records for the system, they should be included in migration scripts; otherwise the records here are just **realistic** test records to speed up development and testing.
