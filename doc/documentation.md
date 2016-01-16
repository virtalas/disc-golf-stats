## Installation

1. Make a copy of 'config/database_example.php' called 'config/database.php'
2. Input PostgreSQL settings in 'config/database.php'
3. If you get a "permission denied" PHP error for the cache folder, have a look at [this](http://stackoverflow.com/questions/8103860/move-uploaded-file-gives-failed-to-open-stream-permission-denied-error-after).
4. Create tables
5. Run `bash autoload.php` or `php composer.phar dump-autoload` in project folder

## Create tables

```shell
psql
\i path/to/disc-golf-stats/sql/create_tables.sql
```

## Import data into PostgreSQL

```shell
bash backup.sh
psql
\i path/to/disc-golf-stats/sql/drop_tables.sql
\i path/to/imported_database_2015XXXXXXXX.sql
```
