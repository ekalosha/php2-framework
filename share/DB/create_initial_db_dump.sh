#!/bin/bash

read -p "Are you really want to create dump of the Development database? [y/n] "
if [ ! $REPLY ] || [ $REPLY != y ]; then
    echo "Dump process aborted. Dump is not created."
    exit 0
fi

INITIAL_DB_NAME="PHP2_V3_Development"
TEMP_DB_NAME="PHP2_V3_Temp"
DUMP_FILE_NAME="PHP2_Initial.DB.dump.sql"

echo "Copying ${INITIAL_DB_NAME} database to ${TEMP_DB_NAME}"
mysqlhotcopy -u root --allowold ${INITIAL_DB_NAME} ${TEMP_DB_NAME} > /dev/null

echo "Cleaning Temporary data in the Development database"
mysql -uroot ${TEMP_DB_NAME} <<EOF
-- Deleting Temporary Users
DELETE FROM SysUser WHERE ID > 10;

-- Deleting all cities except for Canada, Ukraine and US
DELETE FROM City WHERE CountryID NOT IN (39, 226, 229);

quit
EOF

echo "Dumping temporary database"
mysqldump -u root --routines ${TEMP_DB_NAME} > ${DUMP_FILE_NAME}

echo "Deleting Temporary database"
mysql -uroot <<EOF
DROP DATABASE IF EXISTS ${TEMP_DB_NAME};
quit
EOF

echo "Archiving dump file"
tar -czf ${DUMP_FILE_NAME}.tar.gz ${DUMP_FILE_NAME}
rm -rf ${DUMP_FILE_NAME}
chmod 666 ${DUMP_FILE_NAME}.tar.gz
