<?php

/* * ********************************************************************
 * Filename: LearningcenterController.php
 * Folder: controllers
 * Description: Learning center class
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012 - 2015
 * Change History: 
 * Version         Author               Change Description
 * ******************************************************************** */

class LearningcenterController extends Controller {

    public $recommended = 12;

// Define access control
//canbe accessed withoutlogin
    public function accessRules() {
        return array_merge(
                array(array('allow', 'users' => array('?'))),
                // Include parent access rules
                parent::accessRules()
        );
    }

    /**
     * Here we fetch all data for Learning center landing page. It Includes Recommended articles, Topics and Videos.
     */
    public function actionLcmain() {
        $termObject = new WpTerms();
        $recommended_article = array();
        $recommended_article_more = array();
        $featurevideos = array();
        $topics = array();
        $allLatestPosts = array();

        /// Latest Articles Start





        //// Recommended Articles Start
        $allRecoPosts = $termObject->getRecoArticles($this->recommended, 'post');
        if (isset($allRecoPosts)) {
            if (count($allRecoPosts) > 4) {
                $recommended_article_more = array_splice($allRecoPosts, 4);
            } else {
                $recommended_article_more = array();
            }
            if (count($allRecoPosts) > 0) {
                $recommended_article = array_splice($allRecoPosts, 0, 4);
            } else {
                $recommended_article = array();
            }
        }
        // Recommended Articles End
        ////// Topics Start
        $categories = $termObject->getAllCategory('category', 'post');
        $sources = array(
            0 => array("./ui/images/learningCenter/retirementWhite.png", "#00669a", "width: 65%; padding-top: 55px; margin-left: 38px"),
            1 => array("./ui/images/learningCenter/estatePlanningWhite.png", "#f36639", "width: 65%; padding-top: 47px; margin-left: 40px"),
            2 => array("./ui/images/learningCenter/insuranceWhite.png", "#00b1b8", "width: 55%; padding-top: 50px; margin-left: 50px"),
            3 => array("./ui/images/learningCenter/investingWhite.png", "#687483", "width: 65%; padding-top: 60px; margin-left: 30px"),
            4 => array("./ui/images/learningCenter/taxPlanningWhite.png", "#ff8da6", "width: 55%; padding-top: 55px; margin-left: 50px"),
            5 => array("./ui/images/learningCenter/goalWhite.png", "#00605e", "width: 65%; padding-top: 45px; margin-left: 38px"),
            6 => array("./ui/images/learningCenter/debtWhite.png", "#9a180d", "width: 40%; padding-top: 50px; margin-left: 75px"),
            7 => array("./ui/images/learningCenter/financialPlanningWhite.png", "#ffc324", "width: 40%; padding-top: 65px; margin-left: 65px")
        );

        $i = 0;
        if (isset($categories)) {
            foreach ($categories as $category) {
                if ($i <= 7) {
                    $topic_articles = array();
                    $articles = $termObject->getCategoryTitles($category["categoryId"], 'post');
                    if ($articles) {
                        foreach ($articles as $inner) {
                            if (strlen($inner['post_title']) >= 24) {
                                $inner['post_title'] = substr($inner['post_title'], 0, 24) . '..';
                            }
                            $topic_articles[] = array('post_id' => $inner['post_id'], 'post_title' => $inner['post_title'], 'post_name' => $inner['post_name'], 'post_url' => "learningcenter");
                        }
                    }
                    $topics[] = array("name" => $category["categoryName"], "id" => $category["categorySlug"], "source" => $sources[$i][0], "background" => $sources[$i][1], "style" => $sources[$i][2], "articles" => $topic_articles, "post_url" => "category");
                    $i++;
                }
            }
        }
        ////// Topics End
        //// Featured Videos Start  - Summary Page Videos
        $allvideos = $termObject->getAllVideos('category', 'attachment', 'post');
        if (isset($allvideos)) {
            foreach ($allvideos as $vidvalue) {
                if (stristr($vidvalue["source"], 'youtube.com')) {
                    $vidvalue['source'] = str_replace('http://www.youtube.com/embed/', '', $vidvalue['source']);
                    $vidvalue['source'] = str_replace('http://www.youtube.com/watch?v=', '', $vidvalue['source']);
                    $vidvalue['post_url'] = 'learningcenter';
                    $featurevideos["youtube"][] = $vidvalue;
                } else {
                    $vidvalue['ext'] = substr($vidvalue['source'], -3);
                    $vidvalue['post_url'] = 'learningcenter';
                    $featurevideos["videos"][] = $vidvalue;
                }
            }
        }
        //// Featured Videos End
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "recoposts" => $recommended_article, "recopostsmore" => $recommended_article_more, "topics" => $topics, "featvideos" => $featurevideos,"action"=>"learningsummary")));
    }

    /**
     *  Fetch all Press release from DB.

     */
    public function actionGetpress() {
        $termObject = new WpTerms();
        $press = $termObject->getAllPress("type_press");
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "press" => $press)));
    }

    /**
     * Fetch details of an article and its attachments using article ID.
     */
    public function actionLcpost() {
        $postid = $_POST["id"];

        $termObject = new WpTerms();
        $article = $termObject->getArticleById($postid, 'post');
        $postid = $article['post_id'];
        $term_id = $article['term_id'];
        $category = $termObject->getAllCategory('category', 'post');
        $article['files'] = $termObject->getAllAttachsByParent($postid, 'attachment');
        $article['relatedarticle'] = $termObject->getCategoryTitles($term_id, 'post', $postid);

        $article['category_type']="category";
        $article['video']="";
        if (isset($article['files'][0]['youtube']['link']) && $article['files'][0]['youtube']['link'] <> '') {
            $article['key'] = $article['files'][0]['youtube']['link'];
            $article['video']=1;
        }
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
            $article['user_id'] = $user_id;
        }
        $articleSearch[] = $article;
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "article" => $articleSearch, "categories" => $category)));
    }

    /**
     * Fetch all artilces from DB using search parameter.
     */
    public function actionLcpostsearch() {
        $search = $_POST["search"];

        $articleSearchMore = "";
        $articleSearchLess = "";
        $termObject = new WpTerms();
        $articles = $termObject->getArticleBySearch($search, 'post');
        $categories = $termObject->getAllCategory('category', 'post');
        if ($articles) {
            $articleSearchMore = array_splice($articles, 8);
            $articleSearchLess = array_splice($articles, 0, 8);
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "articles" => $articleSearchLess, "articlesmore" => $articleSearchMore, "categories" => $categories)));
    }

    /**
     * Fetch all article details from DB using category ID and attach category image manually. Category is fixed to 8 only.
     */
    public function actionLccatsearch() {
        $slug = $_POST["catid"];

        $articleSearchMore = "";
        $articleSearch = "";
        $termObject = new WpTerms();
        $catid = $termObject->getCatId($slug);
        $articles = $termObject->getArticlesByCategory($catid, 'post');
        if (!empty($articles)) {
            $articleSearchMore = array_splice($articles, 8);
            $articleSearch = array_splice($articles, 0, 8);
        }
        $categories = $termObject->getAllCategory('category', 'post');
        $cat = array("categoryName" => "");
        $sources = array(
            0 => array("./ui/images/learningCenter/retirementWhite.png", "#00669a", "width: 70%;margin-top:15px"),
            1 => array("./ui/images/learningCenter/estatePlanningWhite.png", "#f36639", "width:65%;margin-top:15px"),
            2 => array("./ui/images/learningCenter/insuranceWhite.png", "#00b1b8", "width: 65%;margin-top:15px"),
            3 => array("./ui/images/learningCenter/investingWhite.png", "#687483", "width:70%;margin-top:20px;margin-right:5px"),
            4 => array("./ui/images/learningCenter/taxPlanningWhite.png", "#ff8da6", "width: 65%;margin-top:15px"),
            5 => array("./ui/images/learningCenter/goalWhite.png", "#00605e", "width: 60%;margin-top:15px;margin-left:5px"),
            6 => array("./ui/images/learningCenter/debtWhite.png", "#9a180d", "width: 50%;margin-left:18px;margin-top:10px"),
            7 => array("./ui/images/learningCenter/financialPlanningWhite.png", "#ffc324", "width: 65%;margin-top:15px;")
        );

        $cCount = 0;
        foreach ($categories as $category) {
            if ($category["categoryId"] == $catid) {
                $cat = $category;
                break;
            }
            $cCount++;
        }
        $cCount = $cCount % 8;
        $description = "";
        if (isset($cat["categoryDescription"]))
            $description = $cat["categoryDescription"];

        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "catid" => $catid, "name" => $cat["categoryName"], "source" => $sources[$cCount][0], "background" => $sources[$cCount][1], "style" => $sources[$cCount][2], "description" => $description, "articles" => $articleSearch, "articlesmore" => $articleSearchMore, "categories" => $categories,"blogaction"=>"")));
    }

    /**
     * Fetch all Glossary Details from DB by Glossary Letter. This is showing in Glossary section.
     */
    public function actionLcglossary() {
        $search = $_POST["postLetter"];
        $termObject = new WpTerms();
        $articles = $termObject->getGlossaryByName($search, 'glossary-term');
        $category = $termObject->getAllCategory('category', 'post');
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "article" => $articles, "categories" => $category)));
    }

    /**
     * Fetch all Press release from DB. This is showing in press section.
     */
    public function actionLcpresscms() {

        $termObject = new WpTerms();
        $articles = $termObject->getAllArticles("type_press", "P.post_date");
        if (!empty($articles)) {
            foreach ($articles as $k => $article) {
                $articles[$k]["post_content"] = str_replace("\n", "<br>", $article["post_content"]);
            }
        }
        $pdf = $termObject->getAllPressPDF("type_press");

        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "pdf" => $pdf, "article" => $articles)));
    }

    public function actionLcpress() {
        $termObject = new WpTerms();
        $articles = $termObject->getAllPress("type_press", "P.post_date");
        $category = $termObject->getAllCategory('category', 'post');
        if (!empty($articles)) {
            foreach ($articles as $k => $article) {
                $articles[$k]["post_content"] = str_replace("\n", "<br>", $article["post_content"]);
            }
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "article" => $articles, "categories" => $category)));
    }

    /**
     * Fetch all Blog Details from DB. This is showing in Blog Summary section.
     */
    public function actionBlogsummary() {
        $termObject = new WpTerms();
        $recommended_article = array();
        $recommended_article_more = array();
        $featurevideos = array();
        $topics = array();

        //checked loggedin user for blog registration//
        $checklogin="not_loggedin";
        if(isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $checklogin = "loggedin";
        }else{
            $checklogin = "not_loggedin";
        }

        //get userid for loggedin user))

        //// Recommended Articles Start
        $allRecoPosts = $termObject->getRecoArticles($this->recommended, 'post');
        $results = $termObject->getLatestArticles();
        $posts = array();
        $categoryHash = array();
        $allLatestPosts = array();
        if($results) {
        	foreach($results as $result) {
				if(!isset($categoryHash[$result["post_id"]])) {
					$categoryHash[$result["post_id"]] = array();
				}
				$length = count($categoryHash[$result["post_id"]]);
				$categoryHash[$result["post_id"]][$length] = array();
				$categoryHash[$result["post_id"]][$length]["categoryname"] = $result["categoryname"];
				$categoryHash[$result["post_id"]][$length]["categoryslug"] = $result["categoryslug"];

				$post = $result;
				$post["categories"] = $categoryHash[$result["post_id"]];
				$posts[$result["post_id"]] = $post;
			}
			foreach($posts as $key => $value) {
				$allLatestPosts[] = $value;
			}
        }
        if(!empty($allLatestPosts) && count($allLatestPosts) > 20) {
			$allLatestPosts = array_splice($allLatestPosts, 0, 20);
		}
        if (isset($allRecoPosts)) {
            if (count($allRecoPosts) > 4) {
                $recommended_article_more = array_splice($allRecoPosts, 4);
            } else {
                $recommended_article_more = array();
            }
            if (count($allRecoPosts) > 0) {
                $recommended_article = array_splice($allRecoPosts, 0, 4);
            } else {
                $recommended_article = array();
            }
        }
        // Recommended Articles End
        ////// Topics Start
        $categories = $termObject->getAllCategory('category', 'type_blog');
        $sources = array(
            0 => array("./ui/images/learningCenter/retirementWhite.png", "#00669a", "width: 65%; padding-top: 55px; margin-left: 38px"),
            1 => array("./ui/images/learningCenter/estatePlanningWhite.png", "#f36639", "width: 65%; padding-top: 47px; margin-left: 40px"),
            2 => array("./ui/images/learningCenter/insuranceWhite.png", "#00b1b8", "width: 55%; padding-top: 50px; margin-left: 50px"),
            3 => array("./ui/images/learningCenter/investingWhite.png", "#687483", "width: 65%; padding-top: 60px; margin-left: 30px"),
            4 => array("./ui/images/learningCenter/taxPlanningWhite.png", "#ff8da6", "width: 55%; padding-top: 55px; margin-left: 50px"),
            5 => array("./ui/images/learningCenter/goalWhite.png", "#00605e", "width: 65%; padding-top: 45px; margin-left: 38px"),
            6 => array("./ui/images/learningCenter/debtWhite.png", "#9a180d", "width: 40%; padding-top: 50px; margin-left: 75px"),
            7 => array("./ui/images/learningCenter/financialPlanningWhite.png", "#ffc324", "width: 40%; padding-top: 65px; margin-left: 65px")
        );

        ////// Topics End
        //// Featured Videos Start  - Summary Page Videos
        $allvideos = $termObject->getAllVideos('category', 'attachment', 'type_blog');
        if (isset($allvideos)) {
            foreach ($allvideos as $vidvalue) {
                if (stristr($vidvalue["source"], 'youtube.com')) {
                    $vidvalue['source'] = str_replace('http://www.youtube.com/embed/', '', $vidvalue['source']);
                    $vidvalue['source'] = str_replace('http://www.youtube.com/watch?v=', '', $vidvalue['source']);
                    $vidvalue['post_url'] = 'blog';
                    $featurevideos["youtube"][] = $vidvalue;
                } else {
                    $vidvalue['ext'] = substr($vidvalue['source'], -3);
                    $vidvalue['post_url'] = 'blog';
                    $featurevideos["videos"][] = $vidvalue;
                }
            }
        }
        //// Featured Videos End
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "recoposts" => $recommended_article, "recopostsmore" => $recommended_article_more, "categories" => $categories, "featvideos" => $featurevideos,"getLatestArticles"=>$allLatestPosts,"action"=>"","checklogin"=>$checklogin)));
    }

    /**
     * Fetch details of an article and its attachments using article ID.
     */
    public function actionBlogpost() {
        $postid = $_POST["id"];

        $termObject = new WpTerms();

        $recommended_article = array();
        $recommended_article_more = array();

        $article = $termObject->getArticleById($postid, 'type_blog');
        $postid = $article['post_id'];
        $term_id = $article['term_id'];
        $category = $termObject->getAllCategory('category', 'type_blog');
        $article['files'] = $termObject->getAllAttachsByParent($postid, 'attachment');
        $article['category_type']="blogcategory";

        $article['relatedarticle'] = $termObject->getCategoryTitles($term_id, 'type_blog', $postid);

        $allRecoPosts = $termObject->getRecoArticles($this->recommended, 'post');
        $checklogin="not_loggedin";
        if(isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $checklogin = "loggedin";
        }else{
            $checklogin = "not_loggedin";
        }

        if (isset($allRecoPosts)) {
            if (count($allRecoPosts) > 4) {
                $recommended_article_more = array_splice($allRecoPosts, 4);
            } else {
                $recommended_article_more = array();
            }
            if (count($allRecoPosts) > 0) {
                $recommended_article = array_splice($allRecoPosts, 0, 4);
            } else {
                $recommended_article = array();
            }
        }

        if (isset($article['files'][0]['youtube']['link']) && $article['files'][0]['youtube']['link'] <> '') {
            $article['key'] = $termObject->getVidkey($article['files'][0]['youtube']['link']);
        }
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
            $article['user_id'] = $user_id;
        }
        $articleSearch[] = $article;
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "recoposts" => $recommended_article, "article" => $articleSearch, "categories" => $category,"blogaction"=>"blog","checklogin"=>$checklogin)));
    }

    /**
     * Fetch all blogs from DB using search parameter.
     */
    public function actionBlogsearch() {
        $search = $_POST["search"];

        $articleSearchMore = "";
        $articleSearchLess = "";
        $termObject = new WpTerms();

        $recommended_article = array();
        $recommended_article_more = array();

        $results = $termObject->getArticleBySearch($search, 'type_blog');
        $categories = $termObject->getAllCategory('category', 'type_blog');

        $allRecoPosts = $termObject->getRecoArticles($this->recommended, 'post');
        $checklogin="not_loggedin";
        if(isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $checklogin = "loggedin";
        }else{
            $checklogin = "not_loggedin";
        }

        if (isset($allRecoPosts)) {
            if (count($allRecoPosts) > 4) {
                $recommended_article_more = array_splice($allRecoPosts, 4);
            } else {
                $recommended_article_more = array();
            }
            if (count($allRecoPosts) > 0) {
                $recommended_article = array_splice($allRecoPosts, 0, 4);
            } else {
                $recommended_article = array();
            }
        }

        $posts = array();
        $categoryHash = array();
        $allLatestPosts = array();
        if($results) {
        	foreach($results as $result) {
				if(!isset($categoryHash[$result["post_id"]])) {
					$categoryHash[$result["post_id"]] = array();
				}
				$length = count($categoryHash[$result["post_id"]]);
				$categoryHash[$result["post_id"]][$length] = array();
				$categoryHash[$result["post_id"]][$length]["categoryname"] = $result["categoryname"];
				$categoryHash[$result["post_id"]][$length]["categoryslug"] = $result["categoryslug"];

				$post = $result;
				$post["categories"] = $categoryHash[$result["post_id"]];
				$posts[$result["post_id"]] = $post;
			}
			foreach($posts as $key => $value) {
				$allLatestPosts[] = $value;
			}
        }
        if(!empty($allLatestPosts) && count($allLatestPosts) > 20) {
			$allLatestPosts = array_splice($allLatestPosts, 20);
		}
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "recoposts" => $recommended_article, "getLatestArticles" => $allLatestPosts, "categories" => $categories,"blogaction"=>"blog","checklogin"=>$checklogin)));
    }

    /**
     * Fetch all blog details from DB using category ID and attach category image manually. Category is fixed to 8 only.
     */
    public function actionBlogcat() {
        $slug = $_POST["catid"];

        $articleSearchMore = "";
        $articleSearch = "";
        $termObject = new WpTerms();

        $recommended_article = array();
        $recommended_article_more = array();

        $catid = $termObject->getCatId($slug);
        $results = $termObject->getLatestArticles($slug);
        $posts = array();
        $categoryHash = array();
        $allLatestPosts = array();

        $allRecoPosts = $termObject->getRecoArticles($this->recommended, 'post');
        $checklogin="not_loggedin";
        if(isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $checklogin = "loggedin";
        }else{
            $checklogin = "not_loggedin";
        }

        if (isset($allRecoPosts)) {
            if (count($allRecoPosts) > 4) {
                $recommended_article_more = array_splice($allRecoPosts, 4);
            } else {
                $recommended_article_more = array();
            }
            if (count($allRecoPosts) > 0) {
                $recommended_article = array_splice($allRecoPosts, 0, 4);
            } else {
                $recommended_article = array();
            }
        }

        if($results) {
        	foreach($results as $result) {
				if(!isset($categoryHash[$result["post_id"]])) {
					$categoryHash[$result["post_id"]] = array();
				}
				$length = count($categoryHash[$result["post_id"]]);
				$categoryHash[$result["post_id"]][$length] = array();
				$categoryHash[$result["post_id"]][$length]["categoryname"] = $result["categoryname"];
				$categoryHash[$result["post_id"]][$length]["categoryslug"] = $result["categoryslug"];

				$post = $result;
				$post["categories"] = $categoryHash[$result["post_id"]];
				$posts[$result["post_id"]] = $post;
			}
			foreach($posts as $key => $value) {
				$allLatestPosts[] = $value;
			}
        }

        $categories = $termObject->getAllCategory('category', 'type_blog');
        $cat = array("categoryName" => "");
        $sources = array(
            0 => array("./ui/images/learningCenter/retirementWhite.png", "#00669a", "width: 70%;margin-top:15px"),
            1 => array("./ui/images/learningCenter/estatePlanningWhite.png", "#f36639", "width:65%;margin-top:15px"),
            2 => array("./ui/images/learningCenter/insuranceWhite.png", "#00b1b8", "width: 65%;margin-top:15px"),
            3 => array("./ui/images/learningCenter/investingWhite.png", "#687483", "width:70%;margin-top:20px;margin-right:5px"),
            4 => array("./ui/images/learningCenter/taxPlanningWhite.png", "#ff8da6", "width: 65%;margin-top:15px"),
            5 => array("./ui/images/learningCenter/goalWhite.png", "#00605e", "width: 60%;margin-top:15px;margin-left:5px"),
            6 => array("./ui/images/learningCenter/debtWhite.png", "#9a180d", "width: 50%;margin-left:18px;margin-top:10px"),
            7 => array("./ui/images/learningCenter/financialPlanningWhite.png", "#ffc324", "width: 65%;margin-top:15px;")
        );

        $cCount = 0;
        foreach ($categories as $category) {
            if ($category["categoryId"] == $catid) {
                $cat = $category;
                break;
            }
            $cCount++;
        }
        $cCount = $cCount % 8;
        $description = "";
        if (isset($cat["categoryDescription"]))
            $description = $cat["categoryDescription"];

        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "recoposts" => $recommended_article, "catid" => $catid, "name" => $cat["categoryName"], "source" => $sources[$cCount][0], "background" => $sources[$cCount][1], "style" => $sources[$cCount][2], "description" => $description, "getLatestArticles" => $allLatestPosts, "categories" => $categories, "blogaction"=>"blog","checklogin"=>$checklogin)));
    }

    public function actionJobsummary() {
        $termObject = new WpTerms();
        $jobs = $termObject->getLatestJobs();
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "getLatestArticles"=>$jobs)));
    }


     public function actionJobpost() {
        $postid = $_POST["id"];

        $termObject = new WpTerms();


        $article = $termObject->getJobArticleById($postid, 'type_job');
        $article['relatedarticle'] = $termObject->getRelatedJobs($postid);

        $articleSearch[] = $article;
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "article" => $articleSearch)));
    }

    public function actionGetRecommended() {
        $termObject = new WpTerms();
        $posts = array();
        $allRecoPosts = $termObject->getRecoArticles($this->recommended, 'post');
        if(isset($allRecoPosts)) {
        	foreach($allRecoPosts as $post) {
        	    $posts[] = array('id' => $post['post_name'], 'link' => "https://www.flexscore.com/learningcenter/" . $post['post_name'], 'post_title' => $post['post_title'], 'categoryname' => $post['categoryname'], 'images' => $post['images']);
        	}
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "recommended" => $posts)));
    }

    public function actionGetTopics() {
        $termObject = new WpTerms();
        $topics = array();

        $categories = $termObject->getAllCategory('category', 'post');
        $sources = array(
            0 => array("https://www.flexscore.com/ui/images/learningCenter/retirementWhite.png", "#00669a", "width: 65%; padding-top: 55px; margin-left: 38px"),
            1 => array("https://www.flexscore.com/ui/images/learningCenter/estatePlanningWhite.png", "#f36639", "width: 65%; padding-top: 47px; margin-left: 40px"),
            2 => array("https://www.flexscore.com/ui/images/learningCenter/insuranceWhite.png", "#00b1b8", "width: 55%; padding-top: 50px; margin-left: 50px"),
            3 => array("https://www.flexscore.com/ui/images/learningCenter/investingWhite.png", "#687483", "width: 65%; padding-top: 60px; margin-left: 30px"),
            4 => array("https://www.flexscore.com/ui/images/learningCenter/taxPlanningWhite.png", "#ff8da6", "width: 55%; padding-top: 55px; margin-left: 50px"),
            5 => array("https://www.flexscore.com/ui/images/learningCenter/goalWhite.png", "#00605e", "width: 65%; padding-top: 45px; margin-left: 38px"),
            6 => array("https://www.flexscore.com/ui/images/learningCenter/debtWhite.png", "#9a180d", "width: 40%; padding-top: 50px; margin-left: 75px"),
            7 => array("https://www.flexscore.com/ui/images/learningCenter/financialPlanningWhite.png", "#ffc324", "width: 40%; padding-top: 65px; margin-left: 65px")
        );

        $i = 0;
        if (isset($categories)) {
            foreach ($categories as $category) {
                if ($i <= 7) {
                    $topics[] = array("categoryname" => $category["categoryName"], 'link' => "https://www.flexscore.com/category/" . $category["categorySlug"],  "catid" => $category["categorySlug"], "image" => $sources[$i][0], "background" => $sources[$i][1], "style" => $sources[$i][2]);
                    $i++;
                }
            }
        }

        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "topics" => $topics)));
    }

    public function actionGetBlogs() {
        $termObject = new WpTerms();
        $results = $termObject->getLatestArticles();
        $posts = array();
        $categoryHash = array();
        $allLatestPosts = array();
        if($results) {
        	foreach($results as $result) {
				if(!isset($categoryHash[$result["post_id"]])) {
					$categoryHash[$result["post_id"]] = array();
				}
				$length = count($categoryHash[$result["post_id"]]);
				$categoryHash[$result["post_id"]][$length] = array();
				$categoryHash[$result["post_id"]][$length]["categoryname"] = $result["categoryname"];
				$categoryHash[$result["post_id"]][$length]["categoryslug"] = $result["categoryslug"];

				$post = $result;
				$post["categories"] = $categoryHash[$result["post_id"]];
				$posts[$result["post_id"]] = $post;
			}
			foreach($posts as $key => $value) {
				$allLatestPosts[] = $value;
			}
        }
        if(!empty($allLatestPosts) && count($allLatestPosts) > 20) {
			$allLatestPosts = array_splice($allLatestPosts, 0, 20);
		}
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "blogs" => $allLatestPosts)));
	}

}

?>