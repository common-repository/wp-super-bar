<?php
/*
Plugin Name: WP Super Bar
Plugin URI: http://www.lezard-spock.com/wp-super-bar
Description: Simple WordPress bar for outgoing links.
Version: 0.0.2
Author: Gasquez Florian
Author URI: http://www.lezard-spock.com
*/

require_once('headers.php');

/* Check link validity */
function sb_is_link($link)
{
	$options = get_option('wp_super_bar');
	$result  = false;

	foreach ($options['white_list'] as $link_wl) {
		if (strstr($link, $link_wl) || !strstr($link, 'http://')) {
			$result = true;
		}
	}

	return $result;
}

/* Update Link DB */
/* Update Link DB */
function db_check_link($uri)
{
	global $wpdb;
	$key_link = $wpdb->get_var('SELECT link_uid FROM '.$wpdb->prefix.'wp_super_bar WHERE link_url="'.$uri.'" LIMIT 1');

	if (!$key_link) {
		$characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j','k','l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '-', '_', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		$length		= 2;
		$count		= 0;
		$try		= 42;
		do {
			srand((float) microtime() * 10000000);
			$tmp_hash = array_rand($characters, $length);
			$tmp_conv = "";
			
			foreach ($tmp_hash as $v) {
				$tmp_conv .= $characters[$v];
			}
			if ($count > $try) {
				$length++;
			}
			$count++;
			
		} while ($wpdb->get_var('SELECT id FROM '.$wpdb->prefix.'wp_super_bar WHERE link_uid="sb_'.$tmp_conv.' LIMIT 1"'));
		$key_link = 'sb_'.$tmp_conv;
		$wpdb->query('INSERT INTO '.$wpdb->prefix.'wp_super_bar(link_url, link_uid, link_clicks) VALUES("'.$uri.'", "'. $key_link.'", 0)');
	}

	return $key_link;
}

/* Replace link in post content */
function replace_links($content) {
	$options = get_option('wp_super_bar');

	$content_tmp = explode('class="socials', $content);
	preg_match_all("/<a\s*[^>]*>(.*)<\/a>/siU", $content_tmp[0], $matches);
	$links = $matches[0];


	foreach ($links as $link) {
		$uri      = sb_get_attribute('href',$link);
		$key_link = db_check_link($uri);
		if (!sb_is_link($link)) {
			$content = str_replace('href="'.$uri.'"', 'href="'.get_option('home').'/?'.$key_link.'"',$content);
		}
	}
	return $content;
}

/* WP Super Bar Install */
function wp_super_bar_install()
{
	global $wpdb;

	$table_name = $wpdb->prefix . 'wp_super_bar';
		
	if ($wpdb->get_var('show tables like "'.$table_name.'"') != $table_name) {
		$wpdb->query('
			CREATE TABLE '.$table_name.' (
				id smallint(11) NOT NULL auto_increment,
				link_url text NOT NULL,
				link_clicks int(12) NOT NULL,
				link_uid text NOT NULL,
				link_title text NOT NULL,
				PRIMARY KEY  (id)
			);
		');
	}

	if (!get_option('wp_super_bar')) {
		$options = array (
			'share'      => array('facebook', 'twitter', 'google', 'mail'),
			'view'  	 => 'Vues: ',
			'white_list' => array(get_option('siteurl')),
		 	'txt' 		 => array('origin' => 'Link origin', 'click' => 'click', 'share' => 'Share'),
			'css'		 => ''
		);
		add_option("wp_super_bar", $options);
	}
}

/* Get html attribute */
function sb_get_attribute($attrib, $tag){
  $re = '/' . preg_quote($attrib) . '=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is';

  if (preg_match($re, $tag, $match)) {
	 return $match[2];
  }

  return false;
}
?>
