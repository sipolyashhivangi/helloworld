<?php
final class LearningCenter_Model
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
            SELECT  `id`,
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
    final public function get_learningcenter_images ($post_ids)
    {
        if (is_array($post_ids)) $post_ids = join(',', $post_ids);
        $query = <<<________QUERY
            SELECT
                `post_parent`,
                `post_content`,
                `guid`,
                `post`.`post_mime_type`
            FROM
                `{$this->config['db.cms']}`.`wp_posts` AS `post`
            WHERE TRUE
                AND `post`.`post_parent` IN ({$post_ids})
                AND `post`.`post_type` = 'attachment'
                AND `post`.`post_mime_type` IN ('image/png','image/jpeg','image/gif','video/x-flv')
            ;
________QUERY;
        $query = $this->database->prepare($query);
        $query->execute();
        $reply = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
        $reply = array_map('reset',$reply);
        return $reply;
    }
    final public function get_learningcenter ($user_id)
    {
        $query = <<<________QUERY
            SELECT
                `action_step`.`actionsteps`,
                `action_step_meta`.`buttonstep1`,
                `action_step_meta`.`category`,
                `article`.`ID` AS `post_id`,
                `article`.`post_title`,
                `article`.`post_excerpt`,
                `article`.`post_name`
            FROM
                `{$this->config['db.name']}`.`actionstep` AS `action_step`
            JOIN
                `{$this->config['db.name2']}`.`actionstepmeta` AS `action_step_meta`
                ON
                    `action_step_meta`.`actionid` = `action_step`.`actionid`
            JOIN
                `{$this->config['db.name2']}`.`actionsteparticle` AS `article_bridge`
                ON `action_step`.`actionid` = `article_bridge`.`actionid`
            JOIN
                `{$this->config['db.cms']}`.`wp_posts` AS `article`
                ON `article_bridge`.`articleid` = `article`.`ID`
            WHERE
                `action_step`.`user_id` = :user_id
                AND
                `action_step`.`actionstatus` IN ('0','2','3')
            GROUP BY
                `action_step`.`actionsteps`
            LIMIT 10
            ;
________QUERY;
        $query = $this->database->prepare($query);
        $query->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $query->execute();
        $reply = $query->fetchAll(PDO::FETCH_ASSOC);
        return $reply;
    }
}
?>
