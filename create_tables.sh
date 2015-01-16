source config/environment.sh

echo "Luodaan tietokantaulut..."

ssh $USERNAME@users.cs.helsinki.fi "
cd htdocs/$PROJECT_FOLDER/sql
cat drop_tables.sql create_tables.sql | psql -1 -f -
exit"

echo "Valmis!"
