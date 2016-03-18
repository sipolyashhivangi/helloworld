<?php

/**
 * @author Alex
 */
$link = mysql_connect($paramsLocal['db.host'], $paramsLocal['db.username'], $paramsLocal['db.password']);
if (!$link) {
    die('Not connected : ' . mysql_error());
}

// make foo the current db
$db_selected = mysql_select_db($paramsLocal['db.cms'], $link);
if (!$db_selected) {
    die('Can\'t use db.cms : ' . mysql_error());
}

// For SEO purpose of LC
function getArticle($id) {
    try {
        //Need to prune
        //$result = mysql_query('SELECT post_title, post_excerpt, guid, post_mime_type  from wp_posts where ID IN (SELECT A.post_id FROM wp_posts AS P INNER JOIN wp_postmeta AS A ON P.ID = A.post_id WHERE P.ID ="' . $id . '" AND A.meta_key="_thumbnail_id") AND post_mime_type !="application/pdf"');
        $result1 = mysql_query('SELECT guid,post_mime_type from wp_posts WHERE ID IN (SELECT A.meta_value FROM wp_posts AS P INNER JOIN wp_postmeta AS A ON P.ID = A.post_id WHERE P.post_name = "' . $id . '" AND A.meta_key="_thumbnail_id") and post_type="attachment"');
        $result2 = mysql_query('SELECT post_title,post_excerpt from wp_posts WHERE ID IN (SELECT A.post_id FROM wp_posts AS P INNER JOIN wp_postmeta AS A ON P.ID = A.post_id WHERE P.post_name = "' . $id . '"  AND A.meta_key="_thumbnail_id")');
        $row = mysql_fetch_array($result1);
        if ($row["guid"]) {
            array_push($row, (mysql_fetch_array($result2)));
            $row["post_title"]=$row[2]["post_title"];
            $row["post_excerpt"]=$row[2]["post_excerpt"];
            return $row;
        } else {
            $result = mysql_query('SELECT P.post_title, P.post_excerpt, A.guid, A.post_mime_type FROM wp_posts as P INNER JOIN wp_posts as A ON P.ID=A.post_parent AND A.post_type="attachment"  WHERE P.post_name = "' . $id . '"');
            $row = mysql_fetch_array($result);
            return $row;
        }
    } catch (Exception $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
    }
}
//For SEO purpose of LC
function getCategory($id) {
    $result = mysql_query('SELECT T.name AS categoryName, C.description AS categoryDescription 
        FROM wp_terms AS T 
        INNER JOIN wp_term_taxonomy AS C ON C.term_id = T.term_id
        WHERE T.slug = "' . $id . '"');
    if ($result) {
        $row = mysql_fetch_array($result);
        return $row;
    }
}

?>
