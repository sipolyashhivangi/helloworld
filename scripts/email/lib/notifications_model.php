<?php
final class Notifications_Model
{
    private $config,
            $database;
    final public function __construct ($config)
    {
        $this->config = $config;
        $database = new Database($config);
        $this->database = $database->connect();
    }
    final public function get_users ()
    {
        $query = <<<________QUERY
            SELECT  `id` as `id`,
                    `email`,
                    `firstname`,
                    `lastname`,
                    `unsubscribecode`
            FROM    `user`
            WHERE   `isactive`='1'
                AND `verified`=1;
________QUERY;
        $query = $this->database->prepare($query);
        $query->execute();
        $reply = $query->fetchAll(PDO::FETCH_ASSOC);
        return $reply;
    }
    final public function get_notifications ($user_id)
    {
        $query = <<<________QUERY
            SELECT  `id`,
                    `info`,
                    `context`,
                    `message`,
                    `template`
            FROM    `notification`
            WHERE   `user_id`=:user_id
                AND `status` = 0;
________QUERY;
        $query = $this->database->prepare($query);
        $query->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $query->execute();
        $reply = $query->fetchAll(PDO::FETCH_ASSOC);
        return $reply;
    }
}
?>
