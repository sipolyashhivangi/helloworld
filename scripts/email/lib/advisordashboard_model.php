<?php

final class AdvisorDashboard_Model
{
    private $config,
            $database;

    final public function __construct ($config)
    {
        $this->config = $config;
        $database = new Database($config);
        $this->database = $database->connect();
    }

    final public function get_advisors ()
    {
        $query = <<<________QUERY
            SELECT  `advisor`.`id`,
                    `advisor`.`email`,
                    `advisor`.`firstname`,
                    `advisor`.`lastname`,
                    `advisor`.`unsubscribecode`,
                    `advisorsubscription`.`stripestatus`
            FROM `advisor`
            LEFT JOIN `advisorsubscription` ON `advisor`.`id` = `advisorsubscription`.`advisor_id`
            WHERE `advisor`.`isactive` = '1'
                AND `advisor`.`verified` = 1
                AND `advisorsubscription`.`currentperiodend` > NOW()
                ORDER BY `advisor`.`id`
            LIMIT 10000
            ;
________QUERY;
        $queryRef = $this->database->prepare($query);
        $queryRef->execute();
        $reply = $queryRef->fetchAll(PDO::FETCH_ASSOC);
        return $reply;
    }

    final public function get_consumers($advisor_id)
    {
        $query = <<<________QUERY
            SELECT  `consumervsadvisor`.`user_id`,
                `consumervsadvisor`.`status`,
                `consumervsadvisor`.`permission`,
                `consumervsadvisor`.`dateconnect`,
                ROUND(`userscore`.`totalscore` + 250 * (IF(`montecarlouser`.`montecarloprobability` is NULL,`userscore`.`montecarloprobability`,`montecarlouser`.`montecarloprobability`) - `userscore`.`montecarloprobability`)) as totalscore,
                `scorechange`.`scorechange`,
                `user`.`firstname`,
                `user`.`lastname`,
                `user`.`email`
            FROM `consumervsadvisor`
            JOIN `user` ON `user`.`id` = `consumervsadvisor`.`user_id`
            JOIN `userscore` ON `userscore`.`user_id` = `consumervsadvisor`.`user_id`
            LEFT JOIN `montecarlouser` ON `userscore`.`user_id`=`montecarlouser`.`user_id`
            LEFT JOIN `scorechange` ON `scorechange`.`user_id` = `consumervsadvisor`.`user_id`
            WHERE `consumervsadvisor`.`advisor_id`=:advisor_id
                AND `user`.`isactive` = '1'
            ORDER BY `consumervsadvisor`.`user_id`
            LIMIT 10000
            ;
________QUERY;
        $queryRef = $this->database->prepare($query);
        $queryRef->bindParam(":advisor_id", $advisor_id, PDO::PARAM_INT);
        $queryRef->execute();
        $reply = $queryRef->fetchAll(PDO::FETCH_ASSOC);
        return $reply;
    }

    final public function get_top_scorechanged_consumers($advConsumers)
    {
        $topScoreChangedConsumers = array();
        if(count($advConsumers) > 0) {

            foreach($advConsumers as $key => $consumer) {

                $totalScore = $consumer["totalscore"];
                $advConsumers[$key]['buttonname'] = 'Financial Summary';
                if($consumer["status"] != 1) {
                    $advConsumers[$key]['buttonname'] = 'View Message';
                }

                if(isset($consumer["scorechange"]) && $consumer["scorechange"] != '') {
                    $scoreChangeObjArr = json_decode($consumer["scorechange"], true);

                    $firstdate = date(key($scoreChangeObjArr));
                    $firsttime = strtotime($firstdate);

                    $nintydate = date('Y-m-d', strtotime("-89 days"));
                    $nintytime = strtotime($nintydate);

                    if ($firsttime <= $nintytime) {
                        if (isset($scoreChangeObjArr[$nintydate]) && isset($scoreChangeObjArr[$nintydate]["Total"])) {
                            $oldscore = $scoreChangeObjArr[$nintydate]["Total"];
                        }
                        else {
                            while($firsttime < $nintytime) {
                                $dateKey = date("Y-m-d", $firsttime);
                                if (isset($scoreChangeObjArr["$dateKey"])) {
                                    $oldscore = $scoreChangeObjArr["$dateKey"]["Total"];
                                }
                                $firsttime = strtotime('+1 day', $firsttime);
                            }
                        }

                        $scoreDiff = $totalScore - $oldscore;
                        if ($scoreDiff >= 0) {
                            $advConsumers[$key]['positive'] = true;
                            $advConsumers[$key]['scoredifferencetext'] = "+" . abs($scoreDiff) . " pts";
                        }
                        if ($scoreDiff < 0) {
                            $advConsumers[$key]['negative'] = true;
                            $advConsumers[$key]['scoredifferencetext'] = "-" . abs($scoreDiff) . " pts";
                        }
                    } else {
                        $scoreDiff = $totalScore - 202;
                        if ($scoreDiff >= 0) {
                            $advConsumers[$key]['positive'] = true;
                            $advConsumers[$key]['scoredifferencetext'] = "+" . abs($scoreDiff) . " pts";
                        }
                        if ($scoreDiff < 0) {
                            $advConsumers[$key]['negative'] = true;
                            $advConsumers[$key]['scoredifferencetext'] = "-" . abs($scoreDiff) . " pts";
                        }
                    }
                } else {
                    $scoreDiff = $totalScore - 202;
                    if ($scoreDiff >= 0) {
                        $advConsumers[$key]['positive'] = true;
                        $advConsumers[$key]['scoredifferencetext'] = "+" . abs($scoreDiff) . " pts";
                    }
                    if ($scoreDiff < 0) {
                        $advConsumers[$key]['negative'] = true;
                        $advConsumers[$key]['scoredifferencetext'] = "-" . abs($scoreDiff) . " pts";
                    }
                }
                $advConsumers[$key]['scoredifference'] = abs($scoreDiff);
                if($scoreDiff != 0 || $consumer["status"] != 1) {
                    $topScoreChangedConsumers[] = $advConsumers[$key];
                }
            }
        }

        if(count($topScoreChangedConsumers) > 0) {
            usort($topScoreChangedConsumers, 'sortByScoreDiffDesc');
            $i = 1;
            foreach($topScoreChangedConsumers as $key => $consumer) {
                if(trim($consumer['firstname']) == '' && trim($consumer['lastname']) == '') {
                    $topScoreChangedConsumers[$key]['firstname'] = 'Client ' . $i;
                    $topScoreChangedConsumers[$key]['lastname'] = '';
                }
                $i++;
            }
        }

        return $topScoreChangedConsumers;
    }
}

function sortByScoreDiffDesc($arr1,$arr2)
{
    if ($arr1['buttonname'] == $arr2['buttonname']) {
        if ($arr1['scoredifference'] == $arr2['scoredifference']) return 0;
        return ($arr1['scoredifference'] < $arr2['scoredifference']) ? 1 : -1;
    }
    else
    {
        if ($arr1['buttonname'] == 'View Message') return -1;
    else return 1;
    }
}

?>
