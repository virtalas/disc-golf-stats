## Installation

1. Make a copy of 'config/database_example.php' called 'config/database.php'
2. Input PostgreSQL settings in 'config/database.php'

## Import data into PostgreSQL

```shell
bash backup.sh
psql
\i drop_tables.sql
\i imported_database_2015XXXXXXXX.sql
```
