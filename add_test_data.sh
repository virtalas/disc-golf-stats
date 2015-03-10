#!/bin/bash

source config/environment.sh

echo "Lisätään testidata..."

ssh $USERNAME@users.cs.helsinki.fi "
cd htdocs/$PROJECT_FOLDER/sql
psql < add_test_data.sql
exit"

echo "Valmis!"
