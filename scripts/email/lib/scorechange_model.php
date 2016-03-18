<?php
final class ScoreChange_Model
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
    final public function get_score ($user_id)
    {
        $query = <<<________QUERY
            SELECT  `userscore`.`id`,
                    `userscore`.`user_id`,
                     ROUND(`userscore`.`totalscore` + 250 * (IF(`montecarlouser`.`montecarloprobability` is NULL,`userscore`.`montecarloprobability`,`montecarlouser`.`montecarloprobability`) - `userscore`.`montecarloprobability`)) as totalscore,
                    `userscore`.`timestamp`
            FROM `userscore`
            LEFT JOIN `montecarlouser` ON `userscore`.`user_id`=`montecarlouser`.`user_id`
            WHERE `userscore`.`user_id`=:user_id
            ORDER BY `userscore`.`timestamp` DESC
            LIMIT 1;
________QUERY;
        $query = $this->database->prepare($query);
        $query->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $query->execute();
        $reply = $query->fetchAll(PDO::FETCH_ASSOC);
        $reply = array_shift($reply);
        return $reply;
    }
    final public function get_scorechange ($user_id)
    {
        $query = <<<________QUERY
            SELECT  `id`,
                    `user_id`,
                    `scorechange`,
                    `timestamp`
            FROM `scorechange`
            WHERE `user_id`=:user_id
            ORDER BY `timestamp` DESC
            LIMIT 1;
________QUERY;
        $query = $this->database->prepare($query);
        $query->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $query->execute();
        $reply = $query->fetchAll(PDO::FETCH_ASSOC);
        return array_shift($reply);
    }
    final public function get_actionstep ($user_id)
    {
        $query = <<<________QUERY
            SELECT  `actionstep`.`id`,
                    `actionstep`.`type`,
                    `actionstep`.`points`,
                    `actionstep`.`actionsteps`,
                    `actionstepmeta`.`category`,
                    `actionstepmeta`.`buttonstep1`
            FROM `{$this->config['db.name']}`.`actionstep`
            JOIN `{$this->config['db.name2']}`.`actionstepmeta`
                ON `actionstep`.`actionid` = `actionstepmeta`.`actionid`
            WHERE `actionstep`.`user_id`=:user_id AND `actionstep`.`actionstatus` IN ('0', '2','3')
            ORDER BY FIELD(`actionstep`.`type`, 'instant','short','mid'), `actionstep`.`points` DESC
            LIMIT 1;
________QUERY;
        $query = $this->database->prepare($query);
        $query->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $query->execute();
        $reply = $query->fetchAll(PDO::FETCH_ASSOC);
        return array_shift($reply);
    }
    final public function get_goals ($user_id)
    {
        $colors = [
            ['F36639','AF5813'],
            ['00B1B8','137279'],
            ['FFC324','C08D1D']
        ];
        $query = <<<________QUERY
            SELECT
                `id`,
                `goalname`,
                `goaltype`,
                `status`,
                CASE
                    WHEN `saved`=`goalamount`
                    THEN 100
                    ELSE LEAST(
                        100,
                        GREATEST(
                            0,
                            ROUND(
                                ( `saved` / `goalamount` ) * 100
                            )
                        )
                    )
                END AS `percentage`
            FROM `{$this->config['db.name']}`.`goal`
            WHERE `goalstatus` = 1
                AND `goaltype` <> 'DEBT'
                AND `user_id`=:user_id
            ORDER BY `goalpriority` ASC
            LIMIT 3;
________QUERY;
        $query = $this->database->prepare($query);
        $query->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $query->execute();
        $reply = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($reply as $index => &$value):
            $default_goal_names = [
                'RETIREMENT'    => 'Retirement Goal',
                'HOUSE'         => 'Buy a House',
                'COLLEGE'       => 'Pay for College',
                'CUSTOM'        => 'Custom',
            ];
            if (empty($value['goalname'])):
                $value['goalname'] = $default_goal_names[$value['goaltype']];
            endif;
            switch ($value['status']):
                case 'On Track':
                    $value['status_color'] = '5fa439';
                break;
                case 'Needs Attention': default:
                    $value['status_color'] = 'ec1c23';
            endswitch;
            $value['color'] = $colors[($index % 3)];
        endforeach;
        return $reply;
    }
}
?>
