<?php
require_once('pannel.php');
require_once('bar.php');

global $wpdb;

define( 'SB_URL', get_option('siteurl') . '/wp-content');
define( 'SB_DIR', SB_URL.'/plugins/'.plugin_basename(dirname(__FILE__)));

$share =  array(
	'facebook' 		=> array('Facebook','facebook.png','http://www.facebook.com/share.php?u=__SB_LINK__&amp;t=__SB_TITLE__'),
	'yahoo' 		=> array('Yahoo!','yahoo.png','http://buzz.yahoo.com/submit/?submitUrl=__SB_LINK__&submitHeadline=Comment+avoir+de+la+chance%3F&submitSummary=__SB_TITLE__&submitAssetType=text'),
	'delicious' 	=> array('Delicious','delicious.png','http://del.icio.us/post?url=__SB_LINK__&amp;__SB_TITLE__=__SB_TITLE__'),
	"technorati" 	=> array('Technorati','technorati.png','http://technorati.com/faves?add=__SB_LINK__'),
	'digg' 			=> array('Digg','digg.png','http://digg.com/submit?phase=2&amp;url=__SB_LINK__&amp;__SB_TITLE__=__SB_TITLE__'),
	'twitter' 		=> array('Twitter','twitter.png','http://twitter.com/home?status=__SB_LINK__'),
	'email' 		=> array('Email','email.png','mailto:?subject=__SB_TITLE__&amp;body=__SB_LINK__'),
	'linkedin' 		=> array('LinkedIn','linkedin.png','http://www.linkedin.com/shareArticle?mini=true&amp;url=__SB_LINK__&amp;__SB_TITLE__=__SB_TITLE__&amp;source=BLOGNAME&amp;summary=__SB_TITLE__'),
	'friendfeed' 	=> array('FriendFeed','friendfeed.png','http://www.friendfeed.com/share?__SB_TITLE__=__SB_TITLE__&amp;link=__SB_LINK__'),
	'reddit' 		=> array('Reddit','reddit.png','http://reddit.com/submit?url=__SB_LINK__&amp;__SB_TITLE__=__SB_TITLE__'),
);

/* WP Actions */
add_action('admin_menu', 'add_wp_super_bar');
add_filter('the_content', 'replace_links');

/* Install */
register_activation_hook(__FILE__, 'wp_super_bar_install');

/* Add WP Super Bar */
function add_wp_super_bar() {
	add_options_page('WP Super Bar', 'WP Super Bar', 8, 'wp_super_bar', 'wp_super_bar_options');
}

/* Create empty page with WP Super Bar */
$url = $wpdb->get_var('SELECT link_url FROM '.$wpdb->prefix.'wp_super_bar WHERE link_uid="'.mysql_real_escape_string($_SERVER['QUERY_STRING']).'" LIMIT 1');
if ($url) {
	sb_show($url);
	exit;
} 
?>
