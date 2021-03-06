=== Custom Taxonomy Creator ===

Contributors: reflectionmedia, madalin.ungureanu
Donate link: http://www.cozmoslabs.com/wordpress-creation-kit/custom-taxonomy-creator/
Tags: taxonomy creator, custom taxonomy creator, custom taxonomy, taxonomy, custom taxonomy builder, taxonomy builder
Requires at least: 3.1
Tested up to: 3.5.1
Stable tag: 1.0.2

With WCK Taxonomy Creator you can create and edit custom taxonomies and attach them to post types.
 
== Description ==

**IMPORTANT: The plugin Custom Taxonomy Creator will no longer be supported or updated.**

**Custom Taxonomy Creator is now part of [WCK - Custom Fields and Custom Post Types Creator plugin](http://wordpress.org/extend/plugins/wck-custom-fields-and-custom-post-types-creator/ "download link"), which is fully compatible. [Download it](http://wordpress.org/extend/plugins/wck-custom-fields-and-custom-post-types-creator/ "download link") and install it instead of the current plugin.**

WCK Taxonomy Creator allows you to easily create and edit custom taxonomies for WordPress without any programming knowledge. It provides an UI for most of the arguments of register_taxonomy() function.

Features:

* Create and edit Custom Taxonomies from the Admin UI
* Advanced Labeling Options
* Attach the taxonomies to built in or custom post types

== Installation ==

1. Upload the wck-cptc folder to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Then navigate to WCK => Taxonomy Creator tab and start creating your taxonomies.

== Frequently Asked Questions ==

= How do I list the taxonomies in the frontend? =

If you want to have a custom list in your theme, then you can pass the taxonomy name into the the_terms() function in the Loop, like so:

`<?php the_terms( $post->ID, 'people', 'People: ', ', ', ' ' ); ?>`

That displays the list of People attached to each post.

= How do I query by taxonomy in the frontend? =

Creating a taxonomy generally automatically creates a special query variable using WP_Query class, which we can use to retrieve posts based on. For example, to pull a list of posts that have �Bob� as a �person� taxomony in them, we will use:

`<?php $query = new WP_Query( array( 'person' => 'bob' ) ); ?>`

== Screenshots ==
1. Taxonomy Creator UI: screenshot-1.jpg
2. Taxonomy listing: screenshot-2.jpg

== Changelog ==

= 1.0.2 =
* Important notice: This plugin will no longer be supported or updated. Use [WCK - Custom Fields and Custom Post Types Creator plugin](http://wordpress.org/extend/plugins/wck-custom-fields-and-custom-post-types-creator/) instead.

= 1.0.1 =
* Compatible with WordPress 3.5