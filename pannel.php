<?php
/* WSB Option pannel */
function wp_super_bar_options()
{
	global $share, $wpdb;
	
	if (!$_GET['stats']) {
		$options = get_option('wp_super_bar');
	
		if ($_POST) {
			$options['white_list']		= explode("\n", $_POST['white_list']);
			foreach ($options['white_list'] as &$white) $white = trim($white);

			$options['logo_url']		= $_POST['logo_url'];
			$options['share']			= $_POST['share'];
			$options['txt']['click'] 	= stripslashes($_POST['txt_click']);
			$options['txt']['share'] 	= stripslashes($_POST['txt_share']);
			$options['txt']['origin'] 	= stripslashes($_POST['txt_origin']);
			$options['css']				= stripslashes($_POST['css']);
			$options['powered']			= (bool)$_POST['powered'];

			update_option("wp_super_bar", $options);
		}

		$share_html = '';
		foreach ($share as $key => $type_share) {
			if (in_array($key, $options['share'])) {
				$checked = 'checked="checked"';
			} else {
				$checked = '';
			}		

			$share_html .= '<div style="line-height: 24px"><input '.$checked.' style="vertical-align:bottom;" type="checkbox" name="share[]" id="'.$key.'" value="'.$key.'" /> <label for="'.$key.'"> <img src="'.SB_DIR.'/img/'.$type_share[1].'" style="height: 16px; width: 16px; vertical-align: middle; border-bottom: 1px solid #AAA;" /> '.$type_share[0].'</label></div>';
		}

		if ($options['powered']) {
			 $powered = 'checked="checked"';
		}

		$result = '
				<div class="wrap">
					<div id="icon-options-general" class="icon32"></div>
					<h2>WP Super Bar - Global Options</h2>
					<strong>Settings</strong> - <a href="?page=wp_super_bar&stats=1">Stats</a> - <a href="http://www.lezard-spock.com/wp-super-bar" target="_blank">Plugin URL</a>
					 <form method="post" action="">
						<table class="form-table">
							<tr>
								<th scope="row"><label for="white_list">Whitelist</label></th>
								<td>
									<textarea style="width: 300px; height: 100px;" name="white_list" id="white_list">'.implode("\n", $options['white_list']).'</textarea>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="logo_url">Logo URL (empty = no logo)</label></th>
								<td>
									<input style="width: 300px;" id="logo_url" name="logo_url" value="'.$options['logo_url'].'" />
								</td>
							</tr>
							<tr>
								<th scope="row">Share with</th>
								<td>
									'.$share_html.'
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="css">CSS Hack</label></th>
								<td id="template">
									<textarea class="codepress css" style="width: 300px; height: 100px;" name="css" id="css">'.($options['css']).'</textarea>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="txt_origin">Origin text</label></th>
								<td>
									<input style="width: 300px;" id="txt_origin" name="txt_origin" value="'.($options['txt']['origin']).'" />
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="txt_share">Share text</label></th>
								<td>
									<input style="width: 300px;" id="txt_share" name="txt_share" value="'.($options['txt']['share']).'" />
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="txt_click">Click text</label></th>
								<td>
									<input style="width: 300px;" id="txt_click" name="txt_click" value="'.($options['txt']['click']).'" />
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="powered">Powered by</label></th>
								<td>
									<input type="checkbox" name="powered" value="1" id="powered" '.$powered.' />
								</td>
							</tr>
						</table>
						<p class="submit">
							<input type="submit" name="Submit" class="button-primary" value="'.__('Enregister', 'moodlight').'" />
						</p>
					</form>
			</div>
		';
	} else {
		$wpdb->query('SELECT *  FROM '.$wpdb->prefix.'wp_super_bar ORDER BY link_clicks DESC LIMIT 20 ');

		$stats = '';
		foreach ($wpdb->last_result as $v) {
			$stats .= '<div style="border-bottom: 1px solid #cecece; line-height: 20px; margin-bottom: 3px; width: 770px;"><div style="font-size: 10px; width: 650px; float: left;"><a target="_blank" href="'.$v->link_url.'">'.$v->link_url.'</a></div><div style="width: 70px; float: left; font-size: 10px;"><a target="_blank" href="/?'.$v->link_uid.'">'.$v->link_uid.'</a></div><div style="width: 50px; float: left; font-weight: bold; text-align: center;">'.$v->link_clicks.'</div><div style="clear: both"></div></div>';
		}
		$result = '
				<div class="wrap">
					<div id="icon-options-general" class="icon32"></div>
					<h2>WP Super Bar - Stats</h2>
					<a href="?page=wp_super_bar">Settings</a> - <strong>Stats</strong> - <a href="http://www.lezard-spock.com/wp-super-bar" target="_blank">Plugin URL</a>
					<br /><br />
					<div style="border-bottom: 1px solid #AAA; line-height: 20px; margin-bottom: 3px; width: 770px;"><div style="width: 650px; float: left;"><strong>URL</strong></div><div style="width: 70px; float: left"><strong>UID</strong></div><div style="width: 50px; float: left; text-align: center;"><strong>Views</strong></div><div style="clear: both"></div></div>
					'.$stats.'				
				</div>
		';
	}
	echo $result;
}
?>
