<?php

final class RemindUser_Model
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
            SELECT  `user`.`id`,
                    `user`.`email`,
                    `user`.`unsubscribecode`,
                    `advisor`.`firstname` as advisorfirstname,
                    `advisor`.`lastname` as advisorlastname,
                    CONCAT(`advisor`.`firstname`, " ", `advisor`.`lastname`) as "advisor-name"
            FROM `user`
            JOIN `advisor` ON `advisor`.`id` = `user`.`createdby`
            WHERE `user`.`isactive` = '1'
                AND `user`.`createdby` != ''
                AND ( (DATE(`user`.`createdtimestamp`) = (CURDATE() - INTERVAL 10 DAY)) or (DATE(`user`.`createdtimestamp`) = (CURDATE() - INTERVAL 21 DAY)) )
                AND `user`.`verified` = 0
                ORDER BY `user`.`id` DESC
            ;
________QUERY;
        $queryRef = $this->database->prepare($query);
        $queryRef->execute();
        $reply = $queryRef->fetchAll(PDO::FETCH_ASSOC);
        return $reply;
    }

    final public function update_user_passwordtokenkey($user_id, $token)
    {
        $query = <<<_________QUERY
                UPDATE `user` SET `user`.`resetpasswordcode` = :token, resetpasswordexpiration = NOW() + INTERVAL 10 DAY WHERE `user`.`id` = :user_id
_________QUERY;
        $queryRef = $this->database->prepare($query);
        $queryRef->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $queryRef->bindParam(":token", $token, PDO::PARAM_INT);
        $queryRef->execute();
        return;
    }

}


?>
