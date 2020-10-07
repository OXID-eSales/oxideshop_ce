#!/usr/bin/env sh

echo "Copying $1 to $2"

cat $1 | sed -e 's/`\s*text/` varchar(2048)/i' | sed -e 's/`\s*mediumtext/` varchar(4096)/i' | sed 's/ENGINE=InnoDB/ENGINE=MEMORY/i' > $2

echo >> $2
echo "# Initial data needed" >> $2
echo >> $2
echo "INSERT INTO oxconfig (OXID, OXVARNAME, OXVARVALUE, OXVARTYPE) VALUES ('initialid', 'initialname', 'initialvalue', 'str');" >> $2
echo "INSERT INTO oxshops (OXID, OXEDITION, OXVERSION) VALUES (1, 'CE', '6.0.0');" >> $2