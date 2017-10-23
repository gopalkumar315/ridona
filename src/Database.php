<?php
namespace ridona;

class Database extends Parse
{
    public $prepared_statements_values = array();
    public $sql                        = '';

    public function __construct($dsn, $user, $password, $database = false)
    {
        try {
            $this->dbh = new \PDO($dsn, $user, $password);
            $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->dbh->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
            $this->database = $database;

        } catch (\PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }
    public function tables($tables = false)
    {
        if (!empty($tables)) {
            $this->tables = $tables;
        } else {
            $sth = $this->dbh->prepare("select TABLE_NAME from information_schema.TABLES where TABLE_SCHEMA=?");
            $sth->execute(array($this->database));
            $this->tables = $sth->fetchAll(\PDO::FETCH_COLUMN);
        }

        foreach ($this->tables as $table) {
            $this->sql[] = "select * from $table";
        }
        return $this;
    }
    public function query($sql, $values = array())
    {
        $this->sql                        = [$sql];
        $this->prepared_statements_values = $values;
        return $this;

    }
    public function by_entire()
    {
        foreach ($this->sql as $sql) {echo $sql;
            $sth = $this->dbh->prepare($sql);
            $sth->execute($this->prepared_statements_values);

             while ($row = $sth->fetch(\PDO::FETCH_NUM)) {
                 yield $row;
             }
        }
    }
    public function by_chunk($chunk_size = 10000)
    {

        foreach ($this->sql as $sql) {

            $offset       = 0;
            $exp          = 1;
            $sql_template = trim($sql, ' ');
            $sql_template .= " limit %d offset %d";

            if (strstr($sql, ' limit')) {
                throw new BadSqlException('you cant use LIMIT in chunk mode');
            }

            while (
                $sth = $this->dbh->prepare($sql = sprintf($sql_template, $chunk_size, $offset)) and
                $sth->execute($this->prepared_statements_values) and
                $row = $sth->fetch(\PDO::FETCH_NUM)
            ) {
                $offset = $chunk_size * $exp;
                $exp++;
                do {
                    yield $row;
                } while ($row = $sth->fetch(\PDO::FETCH_NUM));
            }
        }

    }
}
