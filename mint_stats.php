<?php
/*
WordPress Info
----------------------------------------------------------------------------
Plugin Name: ShiftThis | Mint Stats
Plugin URI: http://www.shiftthis.net/wordpress-mint-stats-plugin/
Description: Enable Mint Statistics for your site with Mint embedded into your WP Admin area
Version: 1.0
Author: ShiftThis.net
Author URI: http://www.shiftthis.net


License
-------
GNU General Publics License
http://www.gnu.org/copyleft/gpl.html
*/

//-------------------------------------------------------------------------
stm_check_config();
if ( isset($_POST['stm_submit_options']) ) 
		add_action('init', 'stm_options_submit'); //Update Options 

// Load Options
	$stm_config = get_option('stm_config');
	
	// Determine plugin filename
	$stm_scriptname = basename(__FILE__);

function stm_MintStats(){
	global $stm_config;
$mintdir = get_settings('siteurl').$stm_config['path'];
echo '<iframe id="mintframe" width="99%" height="'.$stm_config['height'].'px" frameborder="0" scrolling="auto" src="'.$mintdir.'"></iframe>';


}
function stm_MintPages() {
	global $stm_config;
	$title = $stm_config['title'];
	$ul = $stm_config['level'];
	if($stm_config['embed'] == 'ON'){
	    add_menu_page($title, $title, $ul, __FILE__, 'stm_MintStats');
	}
	add_options_page('Mint Stats', 'Mint Stats', 6, __FILE__, 'stm_Options');
}
add_action('admin_menu', 'stm_MintPages');

if ($stm_config['location'] == "footer") {
	add_action('wp_footer', 'stm_mintjs');
} else {
	add_action('wp_head', 'stm_mintjs');
}

function stm_Options(){
	$stm_config = get_option('stm_config');
	global $wpdb, $table_prefix, $php;

// Default options configuration page
	if ( !isset($_GET['error']) && current_user_can('level_10') ) {
		?>
		<div class="wrap">
		  	<h2>Mint Stats Options</h2>
		  	<form method="post" action="<?=$_SERVER['REQUEST_URI']?>&amp;updated=true">
		    	<input type="hidden" name="stm_submit_options" value="true" />
				<p class="submit"><input name="Submit" value="Update Options &raquo;" type="submit"></p>
				<table class="optiontable"> 
					<tbody>
						<tr valign="top"> 
							<th scope="row"><label for="embed">Mint Admin Page:</label></th> 
							<td><select name="embed" id="embed"><option value="ON" <?php if ( $stm_config['embed'] == "ON" ){echo "selected='selected'";}?>>ON</option><option value="OFF" <?php if ( $stm_config['embed'] == "OFF" ){echo "selected='selected'";}?>>OFF</option></select> <p>Enable Mint Page in the WordPress Admin Menu <small>(May require a refresh after saving to view changes)</small></p></td> 
						</tr> 
						<tr valign="top"> 
							<th scope="row"><label for="title">Mint Menu Title:</label></th> 
							<td><input type="text" name="title" id="title" value="<?php echo $stm_config['title']; ?>" /></td> 
						</tr> 
						<tr valign="top"> 
							<th scope="row"><label for="level">Mint User Level:</label></th> 
							<td><select name="level" id="level">
							<option value="10" <?php if ( $stm_config['level'] == "10" ){echo "selected='selected'";}?>>Administrator</option>
							<option value="7" <?php if ( $stm_config['level'] == "7" ){echo "selected='selected'";}?>>Editor</option>
							<option value="2" <?php if ( $stm_config['level'] == "2" ){echo "selected='selected'";}?>>Author</option>
							<option value="1" <?php if ( $stm_config['level'] == "1" ){echo "selected='selected'";}?>>Contributor</option>
							<option value="0" <?php if ( $stm_config['level'] == "0" ){echo "selected='selected'";}?>>Subscriber</option>
							</select> <p>Set the User Role for the who can see the Mint Admin Menu Page.</p></td> 
						</tr> 
						<tr valign="top"> 
							<th scope="row"><label for="enable">Mint Logging Status:</label></th> 
							<td><select name="enable" id="enable"><option value="ON" <?php if ( $stm_config['enable'] == "ON" ){echo "selected='selected'";}?>>ON</option><option value="OFF" <?php if ( $stm_config['enable'] == "OFF" ){echo "selected='selected'";}?>>OFF</option></select></td> 
						</tr> 
						<tr valign="top"> 
							<th scope="row"><label for="path">Mint Directory:</label></th> 
							<td><input type="text" name="path" id="path" value="<?php echo $stm_config['path'];?>" /><br />
<p>If your domain is http://www.shiftthis.net and Mint is installed at http://www.shiftthis.net/mint then your directory is "/mint".</p></td> 
						</tr>
						<tr valign="top"> 
							<th scope="row"><label for="location">Script Location:</label></th> 
							<td><label><input type="radio" name="location" id="location" value="head" <?php if ( $stm_config['location'] == "head" ){echo "checked='checked'";}?> /> Head</label><br />
								<label><input type="radio" name="location" id="location" value="footer" <?php if ( $stm_config['location'] == "footer" ){echo "checked='checked'";}?> /> Footer</label><br />
<p>Head is the recommended choice, but if you have any problems Footer may work for you. Make sure your WordPress theme includes  either the <strong>wp_head()</strong> or <strong>wp_footer()</strong> functions or this plug-in will not work.</p></td> 
						</tr>
						<tr valign="top"> 
							<th scope="row"><label for="height">Mint iFrame Height (px):</label></th> 
							<td><input type="text" style="text-align:right;" name="height" id="height" value="<?php echo $stm_config['height'];?>" />px<br />
<p>This will set the height of your Mint iFrame in the Admin Menu Page.  I recommend setting this to the height of your browser window less the top navigation.  This will allow the Mint Pane shortcuts at the top to work properly.</p></td> 
						</tr>
				</tbody>
				</table>
			    <p class="submit">
			      	<input type="submit" name="Submit" value="Update Options &raquo;" />
			    </p>
			</form>
		</div>
		<?

	} // End If

}

function stm_mintjs() {
	global $stm_config;
	if ($stm_config['enable'] != 'OFF') {
		$mint = get_option('siteurl').$stm_config['path']."/?js";
		echo "<script src='".$mint."' type='text/javascript' language='javascript'></script>\n";
	}
}

function stm_check_config() {

	if ( !$option = get_option('stm_config') ) {

		// Default Options
		$option['embed'] = 'ON';
		$option['title'] = 'Mint';
		$option['level'] = '10';
		$option['enable'] = 'ON';
		$option['path'] = '/mint';
		$option['location'] = 'head';
		$option['height'] = '400';

		update_option('stm_config', $option);

	}


}

function stm_options_submit() {


	if ( current_user_can('level_10') ) {

		//options page
		$option['level'] = $_POST['level'];
		$option['title'] = $_POST['title'];
		$option['embed'] = $_POST['embed'];
		$option['enable'] = $_POST['enable'];
		$option['path'] = $_POST['path'];
		$option['location'] = $_POST['location'];
		$option['height'] = $_POST['height'];
		
		update_option('stm_config', $option);

	}

}
?>