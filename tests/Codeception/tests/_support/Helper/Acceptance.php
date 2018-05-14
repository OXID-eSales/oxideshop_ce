<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
    public function clearShopCache()
    {
        $this->getModule('WebDriver')->_restart();
    }

    public function cleanUp()
    {
        $this->getModule('Db')->_beforeSuite();
        $this->getModule('Db')->_cleanup();
    }

    /**
     * Removes \n signs and it leading spaces from string. Keeps only single space in the ends of each row.
     *
     * TODO: duplicate?
     *
     * @param string $line Not formatted string (with spaces and \n signs).
     *
     * @return string Formatted string with single spaces and no \n signs.
     */
    public function clearString($line)
    {
        return trim(preg_replace("/[ \t\r\n]+/", ' ', $line));
    }

    /**
     * Delete entries from $table where $criteria conditions
     * Use: $I->deleteFromDatabase('users', ['id' => '111111', 'banned' => 'yes']);
     *
     * @param  string $table    tablename
     * @param  array $criteria conditions. See seeInDatabase() method.
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function deleteFromDatabase($table, $criteria)
    {
        $dbh = $this->getModule('Db')->dbh;
        $query = "delete from %s where %s";
        $params = [];
        foreach ($criteria as $k => $v) {
            $params[] = "$k = ?";
        }
        $params = implode(' AND ', $params);
        $query = sprintf($query, $table, $params);
        $sth = $dbh->prepare($query);
        return $sth->execute(array_values($criteria));
    }

    public function updateConfigInDatabase($name, $value, $type='bool')
    {
        /** @var \Codeception\Module\Db $dbModule */
        $dbModule = $this->getModule('Db');
        $record = $dbModule->grabNumRecords('oxconfig', ['oxvarname' => $name]);
        $dbh = $dbModule->dbh;
        if ($record > 0) {
            $query = "update oxconfig set oxvarvalue=ENCODE( :value, 'fq45QS09_fqyx09239QQ') where oxvarname=:name";
            $sth = $dbh->prepare($query);
            $sth->execute(['name' => $name, 'value' => $value]);
        } else {
            $query = "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue)
                       values(:oxid, 1, :name, :type, ENCODE( :value, 'fq45QS09_fqyx09239QQ'))";
            $sth = $dbh->prepare($query);
            $sth->execute([
                'oxid' => md5($name.$type),
                'name' => $name,
                'type' => $type,
                'value' => $value
            ]);
        }
    }

}
