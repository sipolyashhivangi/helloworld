<?php
/*
Plugin Name: WordGallery Glossary
Plugin URI: http://wordgallery-glossary.allstruck.net
Description: A simple glossary plugin for showing terms and definitions on one page with optional jQuery animation. Comes with a widget to display some random terms.
Version: 1.0.6
Author: David William Monaghan
Author URI: http://allstruck.com/
License: GPL2
*/


	/*  Copyright 2011 David William Monaghan, AllStruck  (email : wordgallery-glossary-copyright@allstruck.com)

		The software code provided with this plugin is open source but the contents 
		of this package are protected by standard copyrights. You must inquire about 
		any use of the contents of this package. 
		
		Code: Use it freely as long as any release of future versions remain open source
		and fully credited.
		Images, audio, video, and text: Not open source and may not 
		be used without written consent from the creator.

	    This program is free software; you can redistribute it and/or modify
	    it under the terms of the GNU General Public License, version 2, as 
	    published by the Free Software Foundation.

	    This program is distributed in the hope that it will be useful,
	    but WITHOUT ANY WARRANTY; without even the implied warranty of
	    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	    GNU General Public License for more details.

	    You should have received a copy of the GNU General Public License
	    along with this program; if not, write to the Free Software
	    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	*/


add_option('wgGlossary_fullname', 'WordGallery Glossary');
add_option('wgGlossary_name', 'WG Glossary');
add_option('wgGlossary_version', '0.7');
add_option('wgGlossary_url_slug', 'wg-glossary');
add_option('wgGlossary_url_slug_long', 'wordgallery-glossary');
add_option('wgGlossary_items_security_level', 'Editor');
add_option('wgGlossary_displayPageID', 0);
add_option('wgGlossary_alphabet_navigation_enabled', 1);
add_option('wgGlossary_ignore_excerpt', 1);
add_option('wgGlossary_show_read_more_link', 0);
add_option('wgGlossary_read_more_text', 'Read more...');
add_option('wgGlossary_use_jQuery', 0);

if ( ! function_exists( 'is_ssl' ) ) {
 function is_ssl() {
  if ( isset($_SERVER['HTTPS']) ) {
   if ( 'on' == strtolower($_SERVER['HTTPS']) )
    return true;
   if ( '1' == $_SERVER['HTTPS'] )
    return true;
  } elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
   return true;
  }
  return false;
 }
}

if ( version_compare( get_bloginfo( 'version' ) , '3.0' , '<' ) && is_ssl() ) {
 $wp_content_url = str_replace( 'http://' , 'https://' , get_option( 'siteurl' ) );
} else {
 $wp_content_url = get_option( 'siteurl' );
}
$wp_content_url .= '/wp-content';
$wp_content_dir = ABSPATH . 'wp-content';
$wp_plugin_url = $wp_content_url . '/plugins';
$wp_plugin_dir = $wp_content_dir . '/plugins';
$wpmu_plugin_url = $wp_content_url . '/mu-plugins';
$wpmu_plugin_dir = $wp_content_dir . '/mu-plugins';

$ABCMIN = 27;
add_option('wgGlossary_alphabet_navigation_minimum', $ABCMIN);





// Add glossary-term as a new type of post/page, 
// with option to display interface for editing enabled.
function add_glossary_item_post_type(){
	$glossaryPermalink = get_option('wgGlossary_url_slug');
	$glossaryItemSlug = "glossary-term";
	$args = array(
		'label' => 'Glossary Terms',
		'description' => 'Words or phrases to be described and displayed in glossary format on one page.',
		'public' => true,
		'show_ui' => true,
		'_builtin' => false,
		'capability_type' => 'page',
		'hierarchical' => true,
		'rewrite' => array('slug' => $glossaryItemSlug),
		'query_var' => true,
		'menu_position' => 24,
		'supports' => array('title','editor','author','excerpt','trackbacks','comments', 'revisions', 'page-attributes'),
		'taxonomies' => array('category' => 'term'));
	register_post_type('glossary-term',$args);
	flush_rewrite_rules();
}
add_action( 'init', 'add_glossary_item_post_type');


// Display glossary page
function wgGlossary_display_page($content){
	$glossaryPageID = get_option('wgGlossary_page_to_override');
	if (is_numeric($glossaryPageID) && is_page($glossaryPageID)){
		$glossary_item_index = get_children(array(
											'post_type'		=> 'glossary-term',
											'post_status'	=> 'publish',
											'orderby'		=> 'title',
											'order'			=> 'ASC',
											));
		if ($glossary_item_index){
			global $ABCMIN;
			global $styleFile;
			$minimumForABCNav = get_option('wgGlossary_alphabet_navigation_minimum');
			$ABCNavToggle = get_option('wgGlossary_alphabet_navigation_toggle');
			if (count($glossary_item_index) >= $ABCMIN && $ABCNavToggle == 1) {
				$alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
				$alphabetSoup .= '<center style="text-align:center;font-size:xx-large;margin:20px;">';
				foreach ($alphabet as $letter) {
					$alphabetSoup .= '<a href="#'.$letter.'">'. strtoupper($letter) . '</a> ';
				}
				$alphabetSoup .= '</center>';
			}
			
			
			$content .= $alphabetSoup;
			if (get_option("wgGlossary_use_jQuery") == 1) {
				extract($GLOBALS);
				$pluginURL = $wp_plugin_url;
				$content .= '<script type="text/javascript" src="'. $pluginURL .'/wordgallery-glossary/jquery.js"></script>
				';

				$content .= '<script type="text/javascript">
					$(document).ready(function() {';
						if (get_option('wgGlossary_jQuery_first_open') > 0) {
							
				$content .=	'$(".wgGlossaryItemWrapper h4").eq('. (get_option("wgGlossary_jQuery_first_open") - 1) .').addClass("active");
							$(".wgGlossaryItemWrapper h4.active").next("div.wgGlossaryItemDefinition").slideToggle();
							$(".wgGlossaryItemWrapper h4").next("div.wgGlossaryItemDefinition").slideToggle();';
						} else {
				$content .=	'$(".wgGlossaryItemWrapper h4").next("div.wgGlossaryItemDefinition").slideToggle();';
						}

				$content .=	'$(".wgGlossaryItemWrapper h4").click(function(){

							if ($(this).hasClass("active")) {
								$(this).next("div.wgGlossaryItemDefinition:visible").slideUp("fast");
								$(this).toggleClass("active");
								$(this).removeClass("active");
							} else {


						  $(this).next("div.wgGlossaryItemDefinition").slideToggle("slow");
						  $("h4.active").next("div.wgGlossaryItemDefinition:visible").slideUp("fast");
						  $("h4.active").removeClass("active");
						  $(this).toggleClass("active");
						  $(this).siblings("h4").removeClass("active");
						}

						return false;

						});
					});
				</script>
				';
				
			}
			
			$content .= '<div id="wgGlossaryItemList">';
			
			$excerptIgnore = get_option('wgGlossary_ignore_excerpt');
			foreach($glossary_item_index as $item){
				global $excerptIgnore;
				$content .= '<div class="wgGlossaryItemWrapper"><h4 class="wgGlossaryItemTitle"><a href="' . get_permalink($item) . '">' . $item->post_title . '</a></h4>';
				$content .= '<div class="wgGlossaryItemDefinition">';
				if ((($item->post_excerpt == "") || (get_option('wgGlossary_ignore_excerpt') == 1))) {
					$content .= $item->post_content;
					//$content = $content . '<a href="'. get_permalink($item) .'">'. get_option('wgGlossary_read_more_text') .'</a>';
				} else {
					$content .= $item->post_excerpt;
				}
				$readMoreLink = ' <br/> <a class="wgGlossaryItemReadMoreLink" style="float:right;" href="' . get_permalink($item) . '">' . get_option('wgGlossary_read_more_text').'</a>';
				if (get_option('wgGlossary_show_read_more_link') == 1) { 	
					$content .= $readMoreLink;
				}
					$content .= "</div></div>";
			}
			if (get_option('wgGlossary_show_credit_link')) {
				$content .= '<div class="wgGlossaryCreditLink" style="text-align:center; margin:2em !important; clear:both;">Glossary created using <a href="http://wordgallery-glossary.allstruck.net">WordGallery Glossary</a></div>';
			}
			$content .= '</div>';
			$content.= $alphabetSoup;
		}
	}
	
	return $content;
}
add_filter('the_content', 'wgGlossary_display_page');




// Add options panel to admin menu
add_action('admin_menu', 'wg_glossary_menu');
function wg_glossary_menu() {
	add_options_page(get_option('wgGlossary_fullname')." Options", get_option('wgGlossary_name'), 'manage_options', get_option('wgGlossary_url_slug'), 'wg_glossary_options');
}

// WG Glossary options display
function wg_glossary_options() {
	extract($GLOBALS);
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	echo '<div class="wrap">';
	echo '
		<div style="float:right; margin:37px; text-align:center;">
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="BTZPHELVUG6WW">
		<input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_US/i/scr/pixel.gif" width="1" height="1">
		</form>
		<br/><br/>
		<a href="http://wordpress.org/extend/plugins/wordgallery-glossary/">Please leave a rating!</a>
		</div>
		';
	echo '<h2>WordGallery Glossary Settings</h2>';
	echo '<span class="optionPagePluginAuthorCaption">Created by <strong><a href="http://www.iwantdavid.com">David William Monaghan</a></strong> of <strong><a href="http://allstruck.com">AllStruck</a></strong></span>';
	echo '<div class="optionPagePluginDetailsCaption">For more information about this plugin please visit the <strong><a href="http://wordgallery-glossary.allstruck.net">WordGallery Glossary plugin homepage</a>.</strong></div>';
	echo '<h3>Options</h3>';
	echo '<form method="post" action="options.php">';
	wp_nonce_field('update-options');
	echo '<p>
			<fieldset><label for="wgGlossary_page_to_override"><legend class="screen-reader-text"><span>Page to Override</span></legend><select name="wgGlossary_page_to_override" id="wgGlossary_page_to_override">';
			$selectedTrick = "" == get_option('wgGlossary_page_to_override')? " SELECTED " : "";
			echo "<option value=''$selectedTrick>---> Select A Page</option>";
			$selectedTrick = "disabled" == get_option('wgGlossary_page_to_override')? " SELECTED " : "";
			echo "<option value='disabled'$selectedTrick>NONE/DISABLED</option>";
				foreach(get_pages() as $page) {
					$id = $page->ID;
					$selectedTrick = $id == get_option('wgGlossary_page_to_override')? " SELECTED " : "";
					
					$title = $page->post_title;
					echo <<<END
					<option value="$id" $selectedTrick >$title</option>
END;
			}
			echo '</select>';
			echo ' Page to override (All of the Glossary terms will be displayed here in alphabetical order.)</label></fieldset>';

			echo '<p><fieldset><label for="wgGlossary_show_credit_link"><input type="checkbox" id="wgGlossary_show_credit_link" name="wgGlossary_show_credit_link" ';
			checked(true, get_option('wgGlossary_show_credit_link'));
			echo ' value="1" /> Show credit link (there really is no telling how amazing the outcome of this will be).</label></fieldset></p>';
			
			echo '<p><fieldset><label for="wgGlossary_ignore_excerpt"><input type="checkbox" id="wgGlossary_ignore_excerpt" name="wgGlossary_ignore_excerpt" ';
			checked(true, get_option('wgGlossary_ignore_excerpt'));
			echo ' value="1" /> Ignore excerpts.</label></fieldset></p>';
			
			echo '<p><fieldset><input type="checkbox" id="wgGlossary_show_read_more_link" name="wgGlossary_show_read_more_link" ';
			checked(true, get_option('wgGlossary_show_read_more_link'));
			echo ' value="1" /> ';
			echo '<label for="wgGlossary_show_read_more_link">Display "read more" link. </label></fieldset>';
			echo '<label for="wgGlossary_read_more_text">With this text:</label> <input type="text" id="wgGlossary_read_more_text" name="wgGlossary_read_more_text" value="'. get_option('wgGlossary_read_more_text') .'" /></p>';
			
			echo '<p><fieldset><label for="wgGlossary_use_jQuery"><input type="checkbox" id="wgGlossary_use_jQuery" name="wgGlossary_use_jQuery" ';
			checked(true, get_option('wgGlossary_use_jQuery'));
			echo ' value="1" /> ';
			echo'Use jQuery animated display.</label></fieldset> <label for="wgGlossary_jQuery_first_open">Start with item #: <input type="text" size="2" id="wgGlossary_jQuery_first_open" name="wgGlossary_jQuery_first_open" value="'. get_option('wgGlossary_jQuery_first_open') .'" />open on page load.</label></p>';
			
			echo '<p><fieldset><label for="wgGlossary_display_style"><select id="wgGlossary_display_style" name="wgGlossary_display_style">';
			$styleFiles = array();
			if ($handle = opendir(WP_PLUGIN_DIR . '/wordgallery-glossary/style')) {
			    while (false !== ($file = readdir($handle))) {
					if (ereg('.css$', $file)) {
				        array_push($styleFiles, $file);
					}
			    }
			    closedir($handle);
			}
			
			foreach ($styleFiles as $file) {
				echo '
				<option value="'. $file .'"';
				selected($file, get_option("wgGlossary_display_style"));
				echo '>'. $file .'</option>
				';
			}
			echo '<option '. selected('Custom-Style.php', get_option("wgGlossary_display_style")) .' value="Custom-Style.php">* CUSTOM-STYLE</option>';
			echo '</select>';
			echo ' Display Style</label></fieldset></p>';
			echo '<fieldset><label for="wgGlossary_custom_style"><span>Custom style (the following CSS rules will apply if you select CUSTOM-STYLE above):</span><br/>';
			echo '<span>If you want to reset the custom style back to the original helper values, erase all of the contents and save, <strong>twice</strong>!</span>';			
			echo '<textarea id="wgGlossary_custom_style" name="wgGlossary_custom_style" cols="75" rows="10">';
			if (get_option("wgGlossary_custom_style") == "") {
				echo '
/* == Set everything to defaults == */
#wgGlossaryItemList * { border:0px !important; text-decoration:none; padding:0 !important; margin:0 !important; }
#wgGlossaryItemList blockquote { margin:2em !important; }
#wgGlossaryItemList ul, #wgGlossaryItemList ol { margin-left:2em !important; }
#wgGlossaryItemList del { text-decoration: line-through !important; }
#wgGlossaryItemList ins { text-decoration: underline !important; color:#000 !important; background-color:#fff !important; }
#wgGlossaryItemList code { 
	display:block !important; border:3px !important; background-color:#000 !important; color:#00C700 !important; 
	opacity:.7; -moz-opacity:.7; -moz-border-radius: .5em; border-radius: .5em;
	margin: 2em 5em !important;
	padding: 1em !important;
}

/* == Title Text Colors == */
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemTitle { color: black !important; }
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemTitle a { color: black !important; }
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemTitle a:hover { color: black !important; }
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemTitle a:active { color: black !important; }
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemTitle a:visited { color: black !important; }



/* == Definition Text Colors == */
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemDefinition { color: white !important; }
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemDefinition strong { color:black !important; }
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemDefinition a { color: blue !important; }
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemDefinition a:hover { color: blue !important; }
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemDefinition a:active { color: blue !important; }
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemDefinition a:visited { color: blue !important; }


/* == Title Background Color == */
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemTitle { 
	background-color: white !important;
}

/* == Definition Background Color == */
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemDefinition { 
	background-color: gray !important;
}


/* == Title Borders and Corners == */
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemTitle {
	-moz-border-radius: 3em 3em 3em 3em !important;
	border-radius: 3em 3em 3em 3em !important;
	border:3.5px solid red !important;
}

/* == Definition Borders and Corners == */
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemDefinition {
	-moz-border-radius: 1em 1em 7em 1em !important;
	border-radius: 1em 1em 7em 1em !important;
}


/* == Title Margins and Paddings == */
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemTitle { 
	padding:10px !important;
	margin-bottom: 5px !important;
}

/* == Definition Margins and Paddings == */
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemDefinition {
	margin-bottom:7px !important;
	margin-left:17px !important;
	margin-top:-7px !important;
	margin-right:3px !important;
	padding:7px !important;
	padding-bottom:16px !important;
}


/* == Title Positioning == */
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemTitle { z-index: 10 !important; position: relative; }

/* == Definition Positioning == */
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemDefinition { z-index: 9 !important; position: relative; }

/* == Read More Link == */
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemDefinition a.wgGlossaryItemReadMoreLink {
	-moz-border-radius: 3em 3em 3em 3em !important;
	border-radius: 3em 3em 3em 3em !important;
	background-color: gray !important;
	margin-bottom:10px !important;
	margin-right:4em !important;
	color:pink !important;
	padding:1em !important;
}

/* == General == */
#wgGlossaryItemList {}
#wgGlossaryItemList .wgGlossaryItemWrapper {}
#wgGlossaryItemList .wgGlossaryItemWrapper .wgGlossaryItemTitle { clear:both; }



/* + and - sign before title if jQuery is enabled */
.wgGlossaryItemTitle:before { content : "(+) "; }
.wgGlossaryItemTitle.active:before { content: "(—) "; }

/* adds a colon to end of title when expanded using jQuery */
.wgGlossaryItemTitle a:after { content:""; }
.wgGlossaryItemTitle.active a:after { content:":"; }';
			} else {
				echo get_option("wgGlossary_custom_style");
			}
			echo '</textarea></label></fieldset>';
			
			echo '<input type="hidden" name="action" value="update" />
		    <input type="hidden" name="page_options" value="wgGlossary_page_to_override,wgGlossary_ignore_excerpt, wgGlossary_show_read_more_link, wgGlossary_read_more_text, wgGlossary_use_jQuery, wgGlossary_jQuery_first_open, wgGlossary_display_style, wgGlossary_custom_style, wgGlossary_show_credit_link" />';
		    
			echo '<p><input type="submit" class="button-primary" value="Save WordGallery Glossary Settings" name="wgGlossarySave" /></p>';
			echo '</p>';
			echo '</form>';
	
	
	if (isset($_POST["wgGlossarySave"])) {
		//update the page options
		update_option('wgGlossary_page_to_override',$_POST["wgGlossary_page_to_override"]);
		update_option('wgGlossary_read_more_text',$_POST["wgGlossary_read_more_text"]);
		update_option('wgGlossary_jQuery_first_open', $_POST["wgGlossary_jQuery_first_open"]);
		update_option('wgGlossary_display_style', $_POST["wgGlossary_display_style"]);
		update_option('wgGlossary_custom_style', $_POST["wgGlossary_custom_style"]);
		$options_names = array('wgGlossary_show_credit_link', 'wgGlossary_ignore_excerpt', 'wgGlossary_show_read_more_link', 'wgGlossary_use_jQuery');
		foreach($options_names as $option_name){
			if ($_POST[$option_name] == 1) {
				update_option($option_name,1);
			}
			else {
				update_option($option_name,0);
			}
		}
	}
}

// Add style as selected in options
wp_register_style( 'wgGlossaryStyle', WP_PLUGIN_URL . '/wordgallery-glossary/style/' . get_option("wgGlossary_display_style") );
wp_enqueue_style('wgGlossaryStyle');


// register WGGlossaryWidget widget
add_action('widgets_init', create_function('', 'return register_widget("WGGlossaryWidget");'));

// Alert Admin in case settings are not configured
add_action('admin_notices', 'admin_notices');

function admin_notices() {
	// Alert if display style is not set
	if (!ereg('.css$', get_option("wgGlossary_display_style")) && !ereg('.php$', get_option("wgGlossary_display_style"))) {
		echo '<div class="error"><p><strong>The WordGallery Glossary plugin is active but you need to select a style on the <a href="'. admin_url() . 'options-general.php?page=wg-glossary' .'">WG Glossary options page</a>.</strong></p></div>';
	}
	// Alert if override page is not set
	if (get_option("wgGlossary_page_to_override") == "") {
		echo '<div class="error"><p><strong>The WordGallery Glossary plugin is active but you do not have a page selected.</p>
		<p>Create and publish a blank page and select it on the <a href="'. admin_url() . 'options-general.php?page=wg-glossary' .'">options page</a>.</strong></p></div>';
	}
}


/**
 * Glossary Widget
 */
class WGGlossaryWidget extends WP_Widget {
    /** constructor */
    function WGGlossaryWidget() {
        parent::WP_Widget(false, $name = 'WG Glossary');	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
		$itemsTotal = apply_filters('number_of_items', $instance['number_of_items']);
		$itemsLinkToGlossaryPage = apply_filters('items_link_to_glossary_page', $instance['items_link_to_glossary_page']);
		$showFullGlossaryLinkOnBottom = apply_filters('show_full_glossary_link_on_bottom', $instance['show_full_glossary_link_on_bottom']);
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>
					<?php
					$glossary_item_index = get_children(array(
														'post_type'		=> 'glossary-term',
														'post_status'	=> 'publish',
														'orderby'		=> 'title',
														'order'			=> 'ASC',
														));
					if ($glossary_item_index){
						$itemsCount = count($glossary_item_index);
						$randomSeeds = array();
						if ($itemsTotal && $itemsTotal < $itemsCount) {
							$maxItems = $itemsTotal;
						} else {
							if ($itemsCount > 5) {
								$maxItems = 5;
							} else {
								$maxItems = $itemsCount;
							}
						}
						for ($i=0; $i<$maxItems;) {
							$randNum = rand(1, $itemsCount);
							if (in_array($randNum, $randomSeeds)) {
								// do nothing
							} else {
								array_push($randomSeeds, $randNum);
								// On to the next one
								$i++;
							}
						}
						$i = 1;
						echo "<ul>";
						foreach ($glossary_item_index as $item) {
							if (in_array($i, $randomSeeds)) {
								$gotoURL = ($itemsLinkToGlossaryPage == "on")? get_permalink(get_option("wgGlossary_page_to_override")) : get_permalink($item->ID);
								echo '<li><a href="'. $gotoURL .'">';
								echo $item->post_title;
								echo '</a></li>';
							}
							$i++;
						}
						echo "</ul>";
					}
					?>
              <?php if ($showFullGlossaryLinkOnBottom == "on") echo '<a class="wgGlossaryPageLink" style="float:right;margin:10px;color:red;" href="'. get_permalink(get_option("wgGlossary_page_to_override")) .'">View all...</a><br/>'; ?>
              <?php echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
	$instance['number_of_items'] = strip_tags($new_instance['number_of_items']);
	$instance['items_link_to_glossary_page'] = strip_tags($new_instance['items_link_to_glossary_page']);
	$instance['show_full_glossary_link_on_bottom'] = strip_tags($new_instance['show_full_glossary_link_on_bottom']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $title = esc_attr($instance['title']);
		$itemsTotal = esc_attr($instance['number_of_items']);
		$itemsLinkToGlossaryPage = esc_attr($instance['items_link_to_glossary_page']);
		$showFullGlossaryLinkOnBottom = esc_attr($instance['show_full_glossary_link_on_bottom']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label> 
			<br/><br/>
		  <label for="<?php echo $this->get_field_id('number_of_items'); ?>"><?php _e('Number of items:'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('number_of_items'); ?>" name="<?php echo $this->get_field_name('number_of_items'); ?>" type="text" value="<?php echo $itemsTotal; ?>" /></label>
			<br/><br/>
			<label for="<?php echo $this->get_field_id('items_link_to_glossary_page'); ?>">
			<input type="checkbox" id="<?php echo $this->get_field_id('items_link_to_glossary_page'); ?>" name="<?php echo $this->get_field_name('items_link_to_glossary_page'); ?>" <?php checked("on", $itemsLinkToGlossaryPage); ?> /> Link items to glossary page.</label>
			<br/><br/>
			<label for="<?php echo $this->get_field_id('show_full_glossary_link_on_bottom'); ?>">
			<input type="checkbox" id="<?php echo $this->get_field_id('show_full_glossary_link_on_bottom'); ?>" name="<?php echo $this->get_field_name('show_full_glossary_link_on_bottom'); ?>" <?php checked("on", $showFullGlossaryLinkOnBottom); ?> /> Show full glossary link on bottom.</label>
        </p>
        <?php 
    }

}

/**
 * Add a link to the settings page to the plugins list
 */
add_filter( 'plugin_action_links', 'wg_add_action_link', 10, 2 );

function wg_add_action_link( $links, $file ) {
	static $this_plugin;
	$this_plugin = 'wordgallery-glossary.php';
	if( empty($this_plugin) ) $this_plugin = $this->filename;
	if ( $file == $this_plugin ) {
		$settings_link = '<a href="' . $this->plugin_options_url() . '">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link );
	}
	return $links;
}

?>