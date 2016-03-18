<?php

/* * **********************************************************************
 *
 * Filename: check_articles.php
 * Author: Dan Tormey
 * Find articles that are in actionstepmeta but are not in actionsteparticles,
 * and create insert statements for missing articles.
 * Also, check if there are duplicate rows in actionsteparticles
 *
 * ********************************************************************** */

try {

    $ini_array = parse_ini_file("values.ini");
    $dbhost = $ini_array["dbhost"];
    $dbname1 = $ini_array["dbname1"];
    $dbname2 = $ini_array["dbname2"];
    $dbuser = $ini_array["dbuser"];
    $dbpassword = $ini_array["dbpassword"];

    $link = mysql_connect($dbhost, $dbuser, $dbpassword);

    if (!$link) {
        die('Could not connect: ' . mysql_error());
    }
    mysql_select_db($dbname2);

    echo "\n\n*****************************************************************************\n";
    echo "Truncate and insert new records into the actionsteparticle table:\n";
    echo "*****************************************************************************\n\n";


    echo "1. Count the number of records in actionsteparticle...\n";
    $articlesInActionsteparticleCount = 0;
    $countActionsteparticlesSql = 'SELECT count(*) FROM actionsteparticle';

    $countActionsteparticlesResults = mysql_query($countActionsteparticlesSql);
    $countActionsteparticles = mysql_fetch_array($countActionsteparticlesResults);
    echo "    Number of records in actionsteparticles before truncating: ". $countActionsteparticles[0]."\n\n";

    echo "2. Truncate the actionsteparticle table...\n\n";

    $truncateActionsteparticlesSql = 'Truncate table actionsteparticle';
    $truncateActionsteparticlesResults = mysql_query($truncateActionsteparticlesSql);

    echo "3. Count the number of records in actionsteparticle after truncating...\n";

    $articlesInActionsteparticleCount = 0;
    $countActionsteparticlesSql = 'SELECT count(*) FROM actionsteparticle';

    $countActionsteparticlesResults = mysql_query($countActionsteparticlesSql);
    $countActionsteparticles = mysql_fetch_array($countActionsteparticlesResults);
    echo "    Number of records in actionsteparticles after truncating: ". $countActionsteparticles[0]."\n\n";

    if ($countActionsteparticles[0] == 0) {

        echo "4. Retrieve articles from actionstepmeta and insert them into actionsteparticle...\n\n";
        $totalArticlesInActionstepmetaCount = 0;
        $actionsteparticlesArray = array();
        $actionstepmetaArray = array();

        $articlesInActionstepmetaSql = 'select actionid, articles from actionstepmeta where articles <> ""';
        $articlesInActionstepArticlesSql = 'select actionid, articleid from actionsteparticle';
        $actionstepmetaResults = mysql_query($articlesInActionstepmetaSql);

        if ($actionstepmetaResults) {
            while ($row = mysql_fetch_array($actionstepmetaResults)) {
                $articles = explode('|', $row['articles']);
                $actionId = $row[0];
                foreach ($articles as $article) {
                    $totalArticlesInActionstepmetaCount++;
                    $articleArray = explode('#', $article);

            //        echo "Action id: ".$actionId." and articleid: ".$articleArray[2]. " exists!\n";
                    $insertSql = "insert into actionsteparticle (actionid, articleid) values (".$actionId.", ".$articleArray[2].");\n";
                    if ($insertSql) {
                        $row = mysql_query($insertSql);
                    }
                    unset($insertSql);
                }
            }
        } else {
            die(mysql_error());
        }


        echo "4. Count the number of records in actionsteparticle after inserting from actionstepmeta...\n\n";

        $articlesInActionsteparticleCount = 0;
        $countActionsteparticlesSql = 'SELECT count(*) FROM actionsteparticle';

        $countActionsteparticlesResults = mysql_query($countActionsteparticlesSql);
        $countActionsteparticles = mysql_fetch_array($countActionsteparticlesResults);
        echo "    Number of records in actionsteparticles after inserting from actionstepmeta: ". $countActionsteparticles[0]."\n\n";

        echo "\n\n\n";
    }
} catch (Exception $E) {
    echo $E;
}

?>