<?php
final class Database
{
    private $connection = false;
    final public function __construct ($config)
    {
        try {
            $this->connection = new PDO(
                "mysql:dbname={$config['db.name']};host={$config['db.host']};charset=utf8",
                $config['db.username'],
                $config['db.password']
            );
        } catch (PDOException $e) {
            die( 'Connection failed: ' . $e->getMessage() );
        }
    }
    final public function connect ()
    {
        return $this->connection;
    }
}
?>
