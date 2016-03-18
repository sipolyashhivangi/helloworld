<?php

/* * ********************************************************************
 * Filename: Wordpress.php
 * Folder: components
 * Description: Interaction with the Wordpress XMLRPC service
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012 - 2013
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

//include nu soap client
require_once(realpath(dirname(__FILE__) . '/../../cms/wp-includes/class-IXR.php'));

class WordpressClient extends CApplicationComponent {

    public $username = null;
    public $password = null;
    public $xmlrpcUrl = null;


    /*
     * Get wordpress posts
     */

    function getPosts($section = "post", $number = 5) {
        $client = new IXR_Client($this->xmlrpcUrl);
        
        $filter = array('post_type' => $section, 'number' => $number);
        $posts = array();

        if ($client->query('wp.getPosts', 1, $this->username, $this->password, $filter)) {
            $postsResponse = $client->getResponse();
            foreach ($postsResponse as $p) {
                $post = array();
				foreach($p["terms"] as $t)
				{
					$term = array();
					$term["name"] = $t["name"];
    	            $term["taxonomy"] = $t["taxonomy"];
    	            $term["term_id"] = $t["term_id"];
					$post["terms"][] = $term;
                }
				if (isset($p['post_source']))
				{
	                $post["post_source"] = $p["post_source"];
	            }
                $post["post_id"] = $p["post_id"];
                $post["post_title"] = $p["post_title"];
                $post["post_excerpt"] = $p["post_excerpt"];
                $post["post_content"] = $p["post_content"];
                $posts[] = $post;
            }

            if (!empty($posts)) {
                return $posts;
            } else {
                return false;
            }
        }
    }

    /*
     * Get wordpress post
     */

    function getArticlePost($postid) {
        $client = new IXR_Client($this->xmlrpcUrl);

        if ($client->query('wp.getPost', 1, $this->username, $this->password, $postid)) {
            $post = $client->getResponse();

            if (!empty($post)) {
                $postedDate = date("F j, Y", strtotime($post["post_date"]->month . "/" . $post["post_date"]->day . "/" . $post["post_date"]->year));
                $post["datePosted"] = $postedDate;
                $post["post_content"] = nl2br($post["post_content"]);

                $client->query('wp.getUser', 1, $this->username, $this->password, $post["post_author"]);
                $user = $client->getResponse();
                $post["author"] = $user["first_name"] . " " . $user["last_name"];
                $post["authortitle"] = $user["bio"];

                return $post;
            } else {
                return false;
            }
        }
    }

    /**
     * Get all categories
     * @return type
     */
    function getAllCategories() {
        $client = new IXR_Client($this->xmlrpcUrl);

        $categories = array();
        if ($client->query('wp.getCategories', 1, $this->username, $this->password)) {
            foreach ($client->getResponse() as $cat) {
                $category = array();
                $category["categoryId"] = $cat["categoryId"];
                $category["categoryName"] = $cat["categoryName"];
                $category["categoryDescription"] = $cat["categoryDescription"];
                $categories[] = $category;
            }
        }
        return $categories;
    }

    /**
     * 
     * @return type
     */
    function getSearchArticles($search) {
        $client = new IXR_Client($this->xmlrpcUrl);

        $filter = array('post_type' => "post", 's' => $search);

        if ($client->query('wp.getPosts', 1, $this->username, $this->password, $filter)) {
            $searchPosts = $client->getResponse();
            //print_r($searchPosts);die;
            if (!empty($searchPosts)) {
                return $searchPosts;
            } else {
                return false;
            }
        }
        return false;
    }
    /**
     * 
     * @return type
     */
    function getSearchArticlesByCat($catid) {
        $client = new IXR_Client($this->xmlrpcUrl);

        if ($client->query('wp.getPosts', 1, $this->username, $this->password)) {
            $searchTerms = $client->getResponse();
            if (!empty($searchTerms)) {
                return $searchTerms;
            } else {
                return false;
            }
        }   
        return false;
    }   

     /**
     * 
     * @return type
     */
    function getMediaByParentId($parentid){
        $client = new IXR_Client($this->xmlrpcUrl);
        
        if ($parentid <> ''){
            $filter = array('parent_id' => $parentid);    
            if ($client->query('wp.getMediaLibrary', 1, $this->username, $this->password, $filter)) {
                $mediafiles = array();
                foreach ($client->getResponse() as $medias) {
                    $newmediaArray = array();
                    $extension = substr($medias["link"], -3);
                    $medias["ext"] = $extension;
                    if($extension == 'jpg' || $extension == 'gif' || $extension == 'bmp' || $extension == 'png'){
                        $newmediaArray["images"] = $medias;
                    }elseif($extension == 'ogg' || $extension == 'mp4' || $extension == 'flv' || $extension == 'xlv'){
                        $newmediaArray["videos"] = $medias;
                    }else{                    
                        if(stristr($medias["link"],'youtube.com')){
                            $medias['link'] = str_replace('http://www.youtube.com/embed/', '', $medias['link']);
                            $newmediaArray["youtube"] = $medias;
                        }else{
                            $newmediaArray["others"] = $medias;
                        }
                    }                    
                    $mediafiles[] = $newmediaArray;
                }
                return $mediafiles;
            }else{
                return false;
            }
        }
    }
    
    function getGlossarys($search){
        $client = new IXR_Client($this->xmlrpcUrl);
        
        $filter = array('post_type' => 'glossary-term', 'orderby' => 'post_title', 'order' => 'ASC', 'number' => 1, 'offset' => $search);
        $posts = array();

        if ($client->query('wp.getPosts', 1, $this->username, $this->password, $filter)) {
            $postsResponse = $client->getResponse(); 

            if (!empty($postsResponse)) {
                return $postsResponse;
            } else {
                return false;
            }
        }
    }    
}

?>