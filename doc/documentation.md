## Installation

1. Make a copy of 'config/database_example.php' called 'config/database.php'
2. Input PostgreSQL settings to 'config/database.php' for production data
2. Input PostgreSQL settings to 'tests/unit.suite.yml' for running tests
3. If you get a "permission denied" PHP error for the cache folder, have a look at [this](http://stackoverflow.com/questions/8103860/move-uploaded-file-gives-failed-to-open-stream-permission-denied-error-after).
4. Create tables
5. Run `bash autoload.sh` or `php composer.phar dump-autoload` in project folder

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

## Run tests

Disc-golf-stats uses [Codeception](http://codeception.com) for testing.

Run tests:

```shell
./vendor/bin/codecept run
```

## Changing a password

A password changing service is not implemented yet, but here are the steps that can be taken to let a user change their password.

real_id = The playerid of the user that is changing their password

temp_id = The temporary account's playerid

1. Make a backup file of the database just in case with `pg_dump dbname > outfile`
1. Enable registering by uncommenting the two registering urls (get /register, post /register) in config/routes.php.
2. The user will input an account by the name of "temp" and input their new password into the registering form.
3. This will create a new temp account (with temp_id) that has the new password.
4. Copy the password in psql (insert the ids): `UPDATE player SET password = (SELECT password FROM player WHERE playerid = temp_id) WHERE playerid = real_id`
5. Copy the salt in psql (insert the ids): `UPDATE player SET salt = (SELECT salt FROM player WHERE playerid = temp_id) WHERE playerid = real_id`
6. The user can now log into their real account using the new password.
7. Delete the temp account in psql (insert the id CAREFULLY): `DELETE FROM player WHERE playerid = temp_id`
