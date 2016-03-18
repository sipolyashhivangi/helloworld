<?php

/**
 * This is the model class for db "cms".
 *
 */
class WpTerms extends WActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return cms the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'wp_term_relationships';
    }

    /**
     * Recommended articles include articles from 1) the recommended articles popup (flex1),
     * 2) other user actionstep articles, and 3) articles from the taxonomy.
     * @param type $taxonomy_id
     * @return type post_id, post_title, post_excerpt, categoryname
     */
    function getRecoArticles($taxonomy_id, $type) {
        try {
            $mediafiles = array();
            $articleCount = 0;
            $maxArticles = 8;

            // If the user is logged in.
            if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $userid = Yii::app()->getSession()->get('wsuser')->id;

                $userArticles = Actionstep::model()->findBySql("SELECT actionid, flexi1
                    from actionstep where user_id = :user_id AND actionid = 90 AND
                    flexi1 <> ''", array("user_id" => $userid));

                $articleIds = '';
                $articleArray = array();
                $articleHash = array();

                // articles from 1) the recommended articles popup
                if ($userArticles) {
                    $narray = explode(' id="', $userArticles->flexi1);
                    foreach ($narray as $k => $nval) {
                        if ($articleCount < $maxArticles && $k != 0) {
                            $artdiv = explode('"', $nval);
                            $articleHash[$artdiv[0]] = $artdiv[0];
                            $articleArray[] = $artdiv[0];
                            $articleCount++;
                        }
                    }
                }

                $articlesRead = array();
                $userMediaHash = array();
                if ($articleCount < $maxArticles) {
                    $todayDate = new DateTime();
                    $lapseDate = $todayDate->sub(new DateInterval('P90D'));
                    $articlesRead = UserMedia::model()->findAllBySql("select media_id from usermedia
                    where modified > :lapseDate and user_id=:user_id", array("lapseDate" => $lapseDate->format('Y-m-d g:i:s'), "user_id" => $userid));
                    if ($articlesRead) {
                        foreach ($articlesRead as $artRead) {
                            $userMediaHash[$artRead['media_id']] = $artRead['media_id'];
                        }
                    }
                }


                // articles from 2) other user actionstep articles
                if ($articleCount < $maxArticles) {
                    $userActionsteps = Actionstep::model()->findAllBySql("SELECT actionid, actionstatus
                        from actionstep where user_id = :user_id AND actionstatus IN ('0','2','3')", array("user_id" => $userid));

                    $metaActionsteps = Actionstepmeta::model()->findAllBySql("SELECT actionid, articles
                        from actionstepmeta where articles <> ''");

                    if ($userActionsteps && $metaActionsteps) {
                        foreach ($userActionsteps as $userStep) {
                            foreach ($metaActionsteps as $metaStep) {
                                if ($userStep->actionid == $metaStep->actionid) {
                                    $narray = explode('|', $metaStep->articles);
                                    foreach ($narray as $k => $nval) {
                                        if ($articleCount < $maxArticles && $k != 0) {
                                            $artdiv = explode('#', $nval);
                                            if (!isset($articleHash[$artdiv[2]]) && !isset($userMediaHash[$artdiv[2]])) {
                                                $articleHash[$artdiv[2]] = $artdiv[2];
                                                $articleArray[] = $artdiv[2];
                                                $articleCount++;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // articles from 3) articles from the taxonomy
                if ($articleCount < $maxArticles) {
                    $generalArticlesSQL = "SELECT P.ID AS post_id, T.name AS categoryname
                    FROM wp_posts AS P
                    INNER JOIN wp_term_relationships AS R ON P.ID = R.object_id
                    INNER JOIN wp_terms AS T ON T.term_id = R.term_taxonomy_id
                    WHERE P.ID IN (SELECT object_id FROM wp_term_relationships WHERE term_taxonomy_id = :taxonomy_id)
                    AND R.term_taxonomy_id <> :taxonomy_id AND (P.post_status = 'publish' or P.post_status = 'private') AND P.post_type = :post_type
                    ORDER BY categoryname";

                    $connection = Yii::app()->cms;
                    $command = $connection->createCommand($generalArticlesSQL)->bindValue('taxonomy_id', $taxonomy_id)->bindValue('attach', 'attachment')->bindValue('post_type', $type);
                    $generalArticles = $command->queryAll();

                    if ($generalArticles) {
                        foreach ($generalArticles as $genStep) {
                            if ($articleCount < $maxArticles) {
                                if (!isset($articleHash[$genStep["post_id"]]) && !isset($userMediaHash[$genStep["post_id"]])) {
                                    $articleHash[$genStep["post_id"]] = $genStep["post_id"];
                                    $articleArray[] = $genStep["post_id"];
                                    $articleCount++;
                                }
                            }
                        }
                        if ($articleCount < $maxArticles) {
                            foreach ($generalArticles as $genStep) {
                                if ($articleCount < $maxArticles) {
                                    if (!isset($articleHash[$genStep["post_id"]])) {
                                        $articleHash[$genStep["post_id"]] = $genStep["post_id"];
                                        $articleArray[] = $genStep["post_id"];
                                        $articleCount++;
                                    }
                                }
                            }
                        }
                    }
                }

                $articleIds = implode(",", $articleArray);

                if ($articleIds != '') {
                    $userArticleSql = "SELECT P.ID AS post_id, P.post_title AS post_title, P.post_excerpt, CONCAT(DATE_FORMAT(P.post_date,'%a %b %d %Y %H:%i:%S'),' UTC') AS posted_date, P.post_content AS post_content, P.post_name AS post_name, T.name AS categoryname, T.slug AS categoryslug,
                    (SELECT guid AS link FROM wp_posts WHERE post_parent = P.ID AND post_type = 'attachment' LIMIT 1) AS link
                    FROM wp_posts AS P
                    INNER JOIN wp_term_relationships AS R ON P.ID = R.object_id
                    INNER JOIN wp_terms AS T ON T.term_id = R.term_taxonomy_id
                    INNER JOIN wp_term_taxonomy AS C ON C.term_id = T.term_id
                    WHERE C.taxonomy = :ctype AND P.ID in (" . $articleIds . ") AND P.post_type = :ptype";
                    $connection = Yii::app()->cms;
                    $command = $connection->createCommand($userArticleSql)->bindValue('ctype', 'category')->bindValue('ptype', $type);
                    $row = $command->queryAll();
                }
            } else {
                // If the user is not logged in.
                     $userArtSql = "SELECT P.ID AS post_id, P.post_title, P.post_name, P.post_excerpt, CONCAT(DATE_FORMAT(P.post_date,'%a %b %d %Y %H:%i:%S'),' UTC') AS posted_date, T.name AS categoryname, T.slug AS categoryslug,
                    (SELECT guid AS link FROM wp_posts WHERE post_parent = P.ID AND post_type = :attach LIMIT 1) AS link
                    FROM wp_posts AS P
                    INNER JOIN wp_term_relationships AS R ON P.ID = R.object_id
                    INNER JOIN wp_terms AS T ON T.term_id = R.term_taxonomy_id
                    WHERE P.ID IN (SELECT object_id FROM wp_term_relationships WHERE term_taxonomy_id = :taxonomy_id)
                    AND R.term_taxonomy_id <> :taxonomy_id AND (P.post_status = 'publish' or P.post_status = 'private') AND P.post_type = :post_type
                    ORDER BY categoryname";

                $connection = Yii::app()->cms;
                $command = $connection->createCommand($userArtSql)->bindValue('taxonomy_id', $taxonomy_id)->bindValue('attach', 'attachment')->bindValue('post_type', $type);
                $row = $command->queryAll();
            }
            // Used for both logged in and not logged in.
            foreach ($row as $medias) {
                if ($type == "type_blog") {
                    $medias["post_url"] = "blog";
                } else {
                    $medias["post_url"] = "learningcenter";
                }
                if (isset($medias['link'])) {
                    $extension = substr($medias["link"], -3);
                    if ($extension == 'jpg' || $extension == 'gif' || $extension == 'bmp' || $extension == 'png') {
                        $medias["images"] = array('link' => $medias["link"], 'ext' => $extension);
                    } elseif ($extension == 'ogg' || $extension == 'mp4' || $extension == 'flv' || $extension == 'xlv') {
                        $medias["videos"] = array('link' => $medias["link"], 'ext' => $extension);
                    } else {
                        if (stristr($medias["link"], 'youtube.com')) {
                            $link = str_replace('http://www.youtube.com/embed/', '', $medias['link']);
                            $link = str_replace('http://www.youtube.com/watch?v=', '', $link);

                            $medias["youtube"] = array('link' => $link, 'ext' => $extension);
                        }
                    }
                }
                $mediafiles[] = $medias;
            }
            return $mediafiles;
        } catch (Exception $E) {
            echo $E;
        }
    }

    /**
     * Get Article Title by Category
     * @param type $category_id
     * @return type post_id, post_title
     */
    function getCategoryTitles($category_id, $type, $postid = 0) {
        try {
            if ($type == "type_blog") {
                $post_url = "blog";
            } else {
                $post_url = "learningcenter";
            }
            $connection = Yii::app()->cms;
            //Checking if the data is there. !
            if ($postid == 0) {
                $userTitleSql = "SELECT p.ID AS post_id, p.post_title AS post_title, p.post_name AS post_name
                        FROM wp_term_relationships AS t, wp_posts AS p
                        WHERE p.ID = t.object_id AND t.term_taxonomy_id=:cat_id AND p.post_type = :post_type LIMIT 5";
                $command = $connection->createCommand($userTitleSql)->bindValue('cat_id', $category_id)->bindValue('post_type', $type);
            } else {
                $userTitleSql = "SELECT :url AS post_url, p.ID AS post_id, p.post_title AS post_title, p.post_name AS post_name
                        FROM wp_term_relationships AS t, wp_posts AS p
                        WHERE p.ID > :pid AND p.ID = t.object_id AND t.term_taxonomy_id=:cat_id AND p.post_type = :post_type LIMIT 5";
                $command = $connection->createCommand($userTitleSql)->bindValue('cat_id', $category_id)->bindValue('post_type', $type)->bindValue('url', $post_url)->bindValue('pid', $postid);
            }
            $row = $command->queryAll();
            return $row;
        } catch (Exception $E) {
            echo $E;
        }
    }

    /**
     * Get Articles by Category
     * @param type $category_id
     * @return type post_id, post_title, category
     */
    function getArticlesByCategory($category_id, $type) {
        try {
            $mediafiles = array();
            //Checking if the data is there. !
            $userTitleSql = "SELECT p.ID AS post_id, p.post_title AS post_title, p.post_name AS post_name, p.post_excerpt AS post_excerpt, c.name AS category,
                        (SELECT guid AS link FROM wp_posts WHERE post_parent = p.ID AND post_type = :attach LIMIT 1) AS link
                        FROM wp_term_relationships AS t, wp_posts AS p, wp_terms AS c
                        WHERE p.ID = t.object_id AND c.term_id = t.term_taxonomy_id AND (p.post_status = 'publish' or p.post_status = 'private') AND p.post_type = :post_type
                        AND t.term_taxonomy_id =:cat_id";
            $connection = Yii::app()->cms;
            $command = $connection->createCommand($userTitleSql)->bindValue('cat_id', $category_id)->bindValue('attach', 'attachment')->bindValue('post_type', $type);
            $row = $command->queryAll();
            foreach ($row as $medias) {
                if ($type == "type_blog") {
                    $medias["post_url"] = "blog";
                } else {
                    $medias["post_url"] = "learningcenter";
                }
                if (isset($medias['link'])) {
                    $extension = substr($medias["link"], -3);
                    if ($extension == 'jpg' || $extension == 'gif' || $extension == 'bmp' || $extension == 'png') {
                        $medias["images"] = array('link' => $medias["link"], 'ext' => $extension);
                    } elseif ($extension == 'ogg' || $extension == 'mp4' || $extension == 'flv' || $extension == 'xlv') {
                        $medias["videos"] = array('link' => $medias["link"], 'ext' => $extension);
                    } else {
                        if (stristr($medias["link"], 'youtube.com')) {
                            $link = str_replace('http://www.youtube.com/embed/', '', $medias['link']);
                            $link = str_replace('http://www.youtube.com/watch?v=', '', $link);

                            $medias["youtube"] = array('link' => $link, 'ext' => $extension);
                        }
                    }
                }
                $mediafiles[] = $medias;
            }
            return $mediafiles;
        } catch (Exception $E) {
            echo $E;
        }
    }

    /**
     * Get all Category
     * @param type $ctype
     * @return categoryName, categoryId
     */
    function getAllCategory($ctype, $ptype) {
        try {
            //Checking if the data is there. !
            $userCatSql = "SELECT c.name AS categoryName, c.term_id AS categoryId, c.slug AS categorySlug, t.description AS categoryDescription
                        FROM wp_term_taxonomy AS t, wp_terms AS c
                        WHERE c.term_id = t.term_id AND t.taxonomy=:ctype";
            $connection = Yii::app()->cms;
            $command = $connection->createCommand($userCatSql)->bindValue('ctype', $ctype);
            $row = $command->queryAll();
            $mediafiles = array();
            $count = 0;
            foreach ($row as $medias) {
                if ($ptype == "type_blog") {
                    $medias["post_url"] = "blogcategory";
                } else {
                    $medias["post_url"] = "category";
                }
                $mediafiles[] = $medias;
            }
            if ($ptype == "type_blog") {
                $mediafiles = array_splice($mediafiles, 8, 8);
            } else {
                $mediafiles = array_splice($mediafiles, 0, 8);
            }
            return $mediafiles;
        } catch (Exception $E) {
            echo $E;
        }
    }

    /**
     * Get all videos
     * @param type $ctype, $ptype
     * @return post_id, post_title, post_excerpt, categoryname, source
     */
    function getAllVideos($ctype, $atype, $ptype) {
        try {
            //Checking if the data is there. !
            $userVideoSql = "SELECT P.ID AS post_id, P.post_name AS post_name, P.post_title AS post_title, P.post_excerpt, T.name AS categoryname, A.guid AS source
                        FROM wp_posts AS P
                        INNER JOIN wp_term_relationships AS R ON P.ID = R.object_id
                        INNER JOIN wp_terms AS T ON T.term_id = R.term_taxonomy_id
                        INNER JOIN wp_term_taxonomy AS C ON C.term_id = T.term_id
                        INNER JOIN wp_posts AS A ON A.post_parent = P.ID
                        WHERE C.taxonomy = :ctype AND A.post_type = :atype AND P.post_type = :ptype AND (P.post_status = 'publish' or P.post_status = 'private') AND (A.post_mime_type IN ('video/x-flv','audio/ogg'))
                        ORDER BY categoryname, post_title ";
            $connection = Yii::app()->cms;
            $command = $connection->createCommand($userVideoSql)->bindValue('ctype', $ctype)->bindValue('atype', $atype)->bindValue('ptype', $ptype);
            $row = $command->queryAll();
            return $row;
        } catch (Exception $E) {
            echo $E;
        }
    }

    /**
     * Get all attachments
     * @param type $parentid, $ptype
     * @return post_id, post_title, link
     */
    function getAllAttachsByParent($parentid, $ptype) {
        try {
            //Checking if the data is there. !
            $userAttachSql = "SELECT A.ID AS post_id, A.post_title AS post_title, A.guid AS link
                        FROM wp_posts AS P
                        INNER JOIN wp_posts AS A ON A.post_parent = P.ID
                        WHERE A.post_type = :ptype AND P.ID = :pid ORDER BY post_title";
            $connection = Yii::app()->cms;
            $command = $connection->createCommand($userAttachSql)->bindValue('pid', $parentid)->bindValue('ptype', $ptype);
            $row = $command->queryAll();

            $mediafiles = array();
            foreach ($row as $medias) {
                $newmediaArray = array();
                if ($ptype == "type_blog") {
                    $medias["post_url"] = "blog";
                } else {
                    $medias["post_url"] = "learningcenter";
                }
                $extension = substr($medias["link"], -3);
                $medias["ext"] = $extension;
                if ($extension == 'jpg' || $extension == 'gif' || $extension == 'bmp' || $extension == 'png') {
                    $newmediaArray["images"] = $medias;
                } elseif ($extension == 'ogg' || $extension == 'mp4' || $extension == 'flv' || $extension == 'xlv') {
                    $newmediaArray["videos"] = $medias;
                } else {
                    if (stristr($medias["link"], 'youtube.com')) {
                        /////////////////////////////
                        $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
                        $sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
                        $protocol = substr($sp, 0, strpos($sp, "/")) . $s;
                        //////////////////////////////////////

                        $medias['link'] = str_replace('http://www.youtube.com/embed/', '', $medias['link']);
                        $medias['link'] = str_replace('http://www.youtube.com/watch?v=', '', $medias['link']);
                        $medias['protocol'] = $protocol;
                        $newmediaArray["youtube"] = $medias;
                    } else {
                        $newmediaArray["others"] = $medias;
                    }
                }
                $mediafiles[] = $newmediaArray;
            }
            return $mediafiles;
        } catch (Exception $E) {
            echo $E;
        }
    }

    /*
     * Get details of an article by using ID.
     * @param type $postid
     * @return $post_id, $post_title, $post_content, $categoryname.
     */

    function getArticleById($postid, $ptype) {
        try {
            $userArticleSql = "SELECT P.ID AS post_id, P.post_title AS post_title, P.post_content AS post_content, P.post_name AS post_name, T.name AS categoryname, T.term_id AS term_id, T.slug AS categoryslug
            FROM wp_posts AS P
            INNER JOIN wp_term_relationships AS R ON P.ID = R.object_id
            INNER JOIN wp_terms AS T ON T.term_id = R.term_taxonomy_id
            INNER JOIN wp_term_taxonomy AS C ON C.term_id = T.term_id
            WHERE C.taxonomy = :ctype AND P.post_name = :pid AND P.post_type = :ptype";
            $connection = Yii::app()->cms;
            $command = $connection->createCommand($userArticleSql)->bindValue('pid', $postid)->bindValue('ctype', 'category')->bindValue('ptype', $ptype);
            $row = $command->queryAll();
            if ($row) {
                $row[0]['post_content'] = nl2br($row[0]['post_content']);
                return $row[0];
            }
        } catch (Exception $E) {
            echo $E;
        }
    }

    /*
     * Get details of a glossary by using glossary Letter.
     * @param type $title, $type.
     * @return $post_id, $post_title, $post_content.
     */

    function getGlossaryByName($title, $type) {
        try {
            $userGlossarySql = "SELECT P.ID AS post_id, P.post_title AS post_title, P.post_content AS post_content
            FROM wp_posts AS P
            WHERE P.post_title LIKE :title AND P.post_type = :type";
            $connection = Yii::app()->cms;
            $command = $connection->createCommand($userGlossarySql)->bindValue('title', $title)->bindValue('type', $type);
            $row = $command->queryAll();
            if ($row) {
                $row[0]['post_content'] = nl2br($row[0]['post_content']);
                return $row;
            }
        } catch (Exception $E) {
            echo $E;
        }
    }

    /*
     * Get all details of press release.
     * @param type $type.
     * @return $post_id, $post_title, $post_content.
     */

    function getAllPress($type) {
        try {
            $userGlossarySql = "SELECT P.ID AS post_id, P.post_title AS post_title, P.post_content AS post_content
            FROM wp_posts AS P
            WHERE P.post_type = :type AND (P.post_status = 'publish' or P.post_status = 'private') ";
            $connection = Yii::app()->cms;
            $command = $connection->createCommand($userGlossarySql)->bindValue('type', $type);
            $row = $command->queryAll();
            return $row;
        } catch (Exception $E) {
            echo $E;
        }
    }

    /*
     * Get all details of articles by using a search parameter.
     * @param type $search.
     * @return $post_id, $post_title, $post_excerpt, $post_content, $categoryname.
     */

    function getArticleBySearch($search, $type) {
        try {
            $mediafiles = array();
            $userArticleSql = "SELECT P.ID AS post_id, P.post_title AS post_title, P.post_name AS post_name, P.post_excerpt AS post_excerpt, P.post_content AS post_content, T.name AS categoryname, T.slug AS categoryslug,
            CONCAT(DATE_FORMAT(P.post_date,'%a %b %d %Y %H:%i:%S'),' UTC') AS posted_date, U.display_name, (SELECT guid AS link FROM wp_posts WHERE post_parent = P.ID AND post_type = 'attachment' LIMIT 1) AS link
            FROM wp_posts AS P
            INNER JOIN wp_term_relationships AS R ON P.ID = R.object_id
            INNER JOIN wp_terms AS T ON T.term_id = R.term_taxonomy_id
            INNER JOIN wp_term_taxonomy AS C ON C.term_id = T.term_id
            INNER JOIN wp_users U ON (P.post_author = U.ID)
            WHERE C.taxonomy = :ctype AND (P.post_title LIKE :s OR P.post_excerpt LIKE :s)  AND (P.post_status = 'publish' or P.post_status = 'private') AND P.post_type = :type LIMIT 16";
            $connection = Yii::app()->cms;
            $command = $connection->createCommand($userArticleSql)->bindValue('ctype', 'category')->bindValue('s', '%' . $search . '%')->bindValue('type', $type);
            $row = $command->queryAll();
            foreach ($row as $medias) {
                if ($type == "type_blog") {
                    $medias["post_url"] = "blog";
                } else {
                    $medias["post_url"] = "learningcenter";
                }

                if($medias["display_name"]=="admin"){
                    $medias["display_name"]="FlexScore";
                }

                if (isset($medias['link'])) {
                    $extension = substr($medias["link"], -3);
                    if ($extension == 'jpg' || $extension == 'gif' || $extension == 'bmp' || $extension == 'png') {
                        $medias["images"] = array('link' => $medias["link"], 'ext' => $extension);
                    } elseif ($extension == 'ogg' || $extension == 'mp4' || $extension == 'flv' || $extension == 'xlv') {
                        $medias["videos"] = array('link' => $medias["link"], 'ext' => $extension);
                    } else {
                        if (stristr($medias["link"], 'youtube.com')) {
                            $link = str_replace('http://www.youtube.com/embed/', '', $medias['link']);
                            $link = str_replace('http://www.youtube.com/watch?v=', '', $link);

                            $medias["youtube"] = array('link' => $link, 'ext' => $extension);
                        }
                    }
                }
                $mediafiles[] = $medias;
            }
            return $mediafiles;
        } catch (Exception $E) {
            echo $E;
        }
    }

    /*
     * Get video key by using youtube link.
     * @param type $ykey.
     * @return $name.
     */

    function getVidkey($ykey) {
        try {
            $qc = new CDbCriteria();
            $qc->condition = "description = :description";
            $qc->params = array(':description' => $ykey);
            $row = Otlt::model()->find($qc);
            if (isset($row->name)) {
                return $row->name;
            } else {
                return '';
            }
        } catch (Exception $E) {
            echo $E;
        }
    }

    public function getAllPressPDF($type) {
        try {
            $userGlossarySql = "SELECT P.ID AS post_id, DATE_FORMAT(P.post_date, '%M %d - %Y') as press_release_date,P.post_title AS post_title, P.post_content AS post_content, pmeta.meta_key, pmeta.meta_value as pdf_external_link
            FROM wp_posts AS P
            inner join wp_postmeta as pmeta WHERE pmeta.post_id = P.id and P.post_type = :post_type and pmeta.meta_key = 'pdf_link' and pmeta.meta_value != '' order by P.post_date desc";
            $connection = Yii::app()->cms;
            $command = $connection->createCommand($userGlossarySql)->bindValue('post_type', $type);


            $resultArray['pressInfo'] = $command->queryAll();

            /* foreach($row as $key=>$value) {
              $categoryQuery = "select wp_terms.name, wp_term_relationships.object_id from wp_terms
              inner join wp_term_taxonomy on wp_terms.term_id = wp_term_taxonomy.term_id
              inner join wp_term_relationships on wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id
              where wp_term_taxonomy.`taxonomy` = 'Press' and wp_term_relationships.object_id =:object_id";
              $connection = Yii::app()->cms;
              $categoryResult = $connection->createCommand($categoryQuery)->bindValue('object_id', $value['post_id']);
              $categoryArray = $categoryResult->queryAll();

              foreach($categoryArray as $keyCategory=>$valueCategory) {
              #$resultArray['pressCategory'] = $valueCategory['name'];
              $resultArray['pressInfo'] = $value;
              }
              $finalResultArray[] = $resultArray;
              } */

            //echo '<pre>';
            //  print_r($resultArray);
            return $resultArray;
        } catch (Exception $E) {
            echo $E;
        }
    }

    public function getAllArticles($type, $sortType = null) {
        try {
            if($sortType == null) {
                $sortType = "P.ID";
            }
            $userGlossarySql = "SELECT P.ID AS post_id, P.post_title AS post_title, P.post_content AS post_content, M.meta_key, M.meta_value
            FROM wp_posts AS P
            INNER JOIN wp_postmeta as M
            WHERE M.post_id = P.id
            AND P.post_type = :post_type
            AND M.meta_key != ''
            AND P.post_title != 'Auto Draft'
            AND M.meta_key='PDF'
            AND M.meta_value='No'
            ORDER BY " . $sortType . " DESC";
            $connection = Yii::app()->cms;
            $command = $connection->createCommand($userGlossarySql)->bindValue('post_type', $type);
            $row = $command->queryAll();

            foreach ($row as $keyArticle => $valueArticle) {

                $resultArray['post_id'] = $valueArticle['post_id'];
                $resultArray['post_title'] = $valueArticle['post_title'];
                $resultArray['post_content'] = $valueArticle['post_content'];
                $resultArray['meta_key'] = $valueArticle['meta_key'];
                $resultArray['meta_value'] = $valueArticle['meta_value'];

                $userGlossarySql1 = "SELECT P.guid
                FROM wp_posts AS P
                INNER JOIN wp_postmeta as M
                WHERE M.meta_value = P.id and M.post_id = :post_id
                LIMIT 1,1";
                $connection1 = Yii::app()->cms;
                $command1 = $connection1->createCommand($userGlossarySql1)->bindValue('post_id', $valueArticle['post_id']);
                $row1 = $command1->queryAll();

                foreach ($row1 as $key => $value) {

                    $resultArray['articleImage'] = $value['guid'];
                    //echo '<pre>';
                    // print_r($returnArray);
                }

                $returnArray[] = $resultArray;
            }

            return $returnArray;
        } catch (Exception $E) {
            echo $E;
        }
    }

    /*
     * Get video key by using youtube link.
     * @param type $ykey.
     * @return $name.
     */

    function getCatId($slug) {
        try {
            $userCatSql = "SELECT term_id FROM wp_terms WHERE slug = :slug";
            $connection = Yii::app()->cms;
            $command = $connection->createCommand($userCatSql)->bindValue('slug', $slug);
            $row = $command->queryAll();
            if ($row) {
                return $row[0]['term_id'];
            }
        } catch (Exception $E) {
            echo $E;
        }
    }


    function getLatestArticles($category = null){
         try {
         $mediafiles = array();
         $categorySearch = "";
         if($category) {
         	$categorySearch = "AND T.slug = '$category'";
         }
         $latestArtSql = "SELECT P.ID AS post_id, P.post_title, P.post_name, P.post_excerpt, T.name AS categoryname, T.slug AS categoryslug, CONCAT(DATE_FORMAT(P.post_date,'%a %b %d %Y %H:%i:%S'),' UTC') AS posted_date,
                    U.display_name, (SELECT guid AS link FROM wp_posts WHERE post_parent = P.ID AND post_type = 'attachment' LIMIT 1) AS link
                    FROM wp_posts AS P
                    INNER JOIN wp_term_relationships AS R ON P.ID = R.object_id
                    INNER JOIN wp_terms AS T ON T.term_id = R.term_taxonomy_id $categorySearch
                    INNER JOIN wp_users U ON (P.post_author = U.ID)
                    WHERE (P.post_status = 'publish' or P.post_status = 'private') AND P.post_type = 'type_blog'
                    ORDER BY P.post_date DESC";

        $connection = Yii::app()->cms;
        $command = $connection->createCommand($latestArtSql);
        $row = $command->queryAll();

        // Used for both logged in and not logged in.
            foreach ($row as $medias) {
                $medias["post_url"] = "blog";
                if($medias["display_name"]=="admin"){
                    $medias["display_name"]="FlexScore";
                }
                if (isset($medias['link'])) {
                    $extension = substr($medias["link"], -3);
                    if ($extension == 'jpg' || $extension == 'gif' || $extension == 'bmp' || $extension == 'png') {
                        $medias["images"] = array('link' => $medias["link"], 'ext' => $extension);
                    } elseif ($extension == 'ogg' || $extension == 'mp4' || $extension == 'flv' || $extension == 'xlv') {
                        $medias["videos"] = array('link' => $medias["link"], 'ext' => $extension);
                    } else {
                        if (stristr($medias["link"], 'youtube.com')) {
                            $link = str_replace('http://www.youtube.com/embed/', '', $medias['link']);
                            $link = str_replace('http://www.youtube.com/watch?v=', '', $link);

                            $medias["youtube"] = array('link' => $link, 'ext' => $extension);
                        }
                    }
                }
                $mediafiles[] = $medias;
            }
            return $mediafiles;
        } catch (Exception $E) {
            echo $E;
        }
    }

    function getLatestJobs($category = null){
         try {
         $mediafiles = array();
         $latestArtSql = "SELECT P.ID AS post_id, P.post_title, P.post_name, P.post_excerpt
                    FROM wp_posts AS P
                    WHERE (P.post_status = 'publish' or P.post_status = 'private') AND P.post_type = 'type_job'
                    ORDER BY P.post_date DESC";

        $connection = Yii::app()->cms;
        $command = $connection->createCommand($latestArtSql);
        $row = $command->queryAll();

        // Used for both logged in and not logged in.
            foreach ($row as $medias) {
                $medias["post_url"] = "jobs";
                $mediafiles[] = $medias;
            }
            return $mediafiles;
        } catch (Exception $E) {
            echo $E;
        }
    }

    function getRelatedJobs($postid = null){
         try {
         $mediafiles = array();
         $latestArtSql = "SELECT P.ID AS post_id, P.post_title, P.post_name, P.post_excerpt
                    FROM wp_posts AS P
                    WHERE (P.post_status = 'publish' or P.post_status = 'private') AND P.post_type = 'type_job'
                    AND P.post_name!='".$postid."' ORDER BY P.post_date DESC";

        $connection = Yii::app()->cms;
        $command = $connection->createCommand($latestArtSql);
        $row = $command->queryAll();

        // Used for both logged in and not logged in.
            foreach ($row as $medias) {
                $medias["post_url"] = "jobs";
                $mediafiles[] = $medias;
            }
            return $mediafiles;
        } catch (Exception $E) {
            echo $E;
        }
    }

    function getJobArticleById($postid, $ptype) {
        try {
            $userArticleSql = "SELECT P.ID AS post_id, P.post_title AS post_title, P.post_content AS post_content, P.post_name AS post_name
                    FROM wp_posts AS P
                    WHERE P.post_name='".$postid."' AND (P.post_status = 'publish' or P.post_status = 'private') AND P.post_type = '".$ptype."'
                    ORDER BY P.post_date DESC";
            $connection = Yii::app()->cms;
            $command = $connection->createCommand($userArticleSql)->bindValue('pid', $postid)->bindValue('ctype', 'category')->bindValue('ptype', $ptype);
            $row = $command->queryAll();
            if ($row) {
                $row[0]['post_content'] = nl2br($row[0]['post_content']);
                return $row[0];
            }
        } catch (Exception $E) {
            echo $E;
        }
    }

}

?>