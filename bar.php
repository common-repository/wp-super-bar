<?php
/* Create WP Super Bar Page */
function sb_show($url)
{
	global $wpdb, $share;
	$options = get_option('wp_super_bar');
	$wpdb->query('UPDATE '.$wpdb->prefix.'wp_super_bar SET link_clicks=(link_clicks+1) WHERE link_url="'.$url.'"');

	if (strstr($options['logo_url'], 'http://')) {
		$img = '<a href="'.get_option('siteurl').'" title="'.get_option('blogname').'"><img class="logo_url" src="'.$options['logo_url'].'" alt="'.get_option('blogname').'" /></a>';
	}

	$title = $wpdb->get_var('SELECT title FROM '.$wpdb->prefix.'wp_super_bar WHERE link_url="'.$url.'" LIMIT 1');
	if (!$title) {
		$tmp_title = @file_get_contents($url);
		preg_match("#<title>(.*?)</title>#ism", $tmp_title, $matches);
		if ($matches[1]) {
			$title = $matches[1];
		} else {
			$title = '(No Title)';
		}
		$wpdb->query('UPDATE '.$wpdb->prefix.'wp_super_bar SET link_title="'.mysql_real_escape_string($title).'" WHERE link_url="'.$url.'"');
	}

	$share_links = '';
	foreach ($options['share'] as $link) {
		$datas_share    = $share[$link];
		$share_link 	= str_replace('__SB_LINK__', get_option('siteurl').$_SERVER['REQUEST_URI'], $datas_share[2]);
		$share_link 	= str_replace('__SB_TITLE__', urlencode($title), $share_link);
		$share_links 	.= '<a href="'.$share_link.'"><img class="share_img" src="'.SB_DIR.'/img/'.$datas_share[1].'" alt="'.$datas_share[0].'"/></a>';
	}

	if ($options['powered']) {
		$powered = '- <small>(Powered by <a href="http://www.lezard-spock.com/wp-super-bar">WP Super Bar</a>)</small>';
	}

	$result = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
		   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" id="facebook">
		<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<style type="text/css">
		  @import url('.SB_DIR.'/css/bar.css);
		  '.$options['css'].'
		</style>
		<title>'.$title.' | '.get_option('blogname').' </title>
		<script type="text/javascript" src="'.SB_DIR.'/js/mootools-1.2-core.js"></script>
		<script type="text/javascript" src="'.SB_DIR.'/js/mootools-1.2-more.js"></script>	
		<script type="text/javascript" src="'.SB_DIR.'/js/scripts.js"></script>
		</head>
		<body>
		<div id="bar">
			<div id="site_name">
				'.$img.' <span><a href="'.get_option('siteurl').'" title="'.get_option('blogname').'">'.get_option('blogname').'</a></span>
			</div>
			<div id="content">
				<strong>'.$title.'</strong> -  '.$wpdb->get_var('SELECT link_clicks FROM '.$wpdb->prefix.'wp_super_bar WHERE link_url="'.$url.'" LIMIT 1').' '.$options['txt']['click'].'
				<br />  '.$options['txt']['origin'].': <a href="'.$url.'">'.$url.'</a> 
			</div>
			<div id="share">
				<strong>'.$options['txt']['share'].':</strong> '.$powered.'<br />
				<div id="share_buttons">'.$share_links.'</div>
				<a href="'.$url.'"><img id="close" src="'.SB_DIR.'/img/close.png" /></a>
			</div>
		</div>
		<iframe id="iClear" class="iClear" src="'.$url.'"></iframe>
		</body>
		</html>
	';

	echo trim($result);
}
?>
