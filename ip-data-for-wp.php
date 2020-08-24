<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              ethereal-material.com
 * @since             1.0.0
 * @package           Ip_Data_For_Wp
 *
 * @wordpress-plugin
 * Plugin Name:       IP Data for WP
 * Plugin URI:        ethereal-material.com/wp-dev/ip-data-for-wp
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Scott Brown
 * Author URI:        ethereal-material.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ip-data-for-wp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.

//ADMIN MENU---------------------------------------------------------------------


add_action( 'admin_menu', 'ip_data_for_wp_add_admin_menu' );
add_action( 'admin_init', 'ip_data_for_wp_settings_init' );


function ip_data_for_wp_add_admin_menu(  ) { 
	add_menu_page( 'IP Data for WP - Ethereal Material', 'Geo IP Controller', 'manage_options', 'ip_data_for_wp', 'ip_data_for_wp_options_page', 'dashicons-location');
}

function ip_data_for_wp_settings_init(  ) { 
	register_setting( 'ip_data_for_wp_pluginPage', 'ip_data_for_wp_settings' );
	add_settings_section(
		'ip_data_for_wp_pluginPage_section', 
		__( '', 'ip_data_for_wp' ), 
		'ip_data_for_wp_settings_section_callback', 
		'ip_data_for_wp_pluginPage'
	);
       add_settings_field( 
		'ip_data_for_wp_ids', 
		__( 'IDs (comma separated)', 'ip_data_for_wp' ), 
		'ip_data_for_wp_ids_render', 
		'ip_data_for_wp_pluginPage', 
		'ip_data_for_wp_pluginPage_section' 
	);
}


function ip_data_for_wp_ids_render(  ) { 
	$options = get_option( 'ip_data_for_wp_settings' );
	?>
	<input type='text' name='ip_data_for_wp_settings[ip_data_for_wp_ids]' value='<?php echo $options['ip_data_for_wp_ids']; ?>'>
	<?php
}


function ip_data_for_wp_settings_section_callback(  ) { 
	echo __( '', 'ip_data_for_wp' );
}



function ip_data_for_wp_options_page(  ) { 

?>
<form action='options.php' method='post'>
		
<div id="post-body" class="metabox-holder columns-2">
<div id="post-body-content">

<h2>IP Data for WP - By Scott Brown</h2>


<div class="postbox" style="width:70%; padding:30px;">
<h2>Using the plugin</h2>

<p>This plugin allows you to input your IP Data API key and returns GeoIP data associated to allow you to modify content on your WP instance.</p>

<p><strong>Note: </strong>We offer a couple of shortcodes as explained in our documentation and will be growing this further in the near future.</p>

<p style="padding-top:20px;"><strong>Steps</strong></p>	
<p>1. Create an account at ipdata and subscribe to a tier that suits your traffic needs</p>
<p>2. Enter your API key in the field below</p>
<p>3. Save to bind the service to WP</p>
<p>4. Deploy your own solution or use our guides for advice!</p>
</div>


<div class="postbox" style="width:70%; padding:30px;">
<h2>Settings</h2>
<?php
settings_fields( 'ip_data_for_wp_pluginPage' );
do_settings_sections( 'ip_data_for_wp_pluginPage' );
submit_button();
?>
</div>

</form>

</div>
</div>

<?php

}



//GEO CONTENT-----------------------------------------------------------



//ADD GEO POPUP WP HEAD



add_action('wp_footer', 'ip_data_for_wp', -1000);

function ip_data_for_wp() {
	
	$var = "some text";
	$scripts = <<<EOT
EOT;
	
	$ipdataapikey        = get_option('ip_data_for_wp_settings');
    $ipdataapikey_string = preg_replace('/\s+/', '', $ipdataapikey['ip_data_for_wp_ids']);
    $ipdataapikey_array  = explode(',', $ipdataapikey_string);
    $ipdataapikey_array  = array_filter($ipdataapikey_array);
	
	if (!empty($ipdataapikey_array)) {
        for ($i = 0; $i < count($ipdataapikey_array); ++$i) {
            
			$scripts .= <<<EOT
<script>
$.get('https://api.ipdata.co?api-key=' + '$ipdataapikey_array[$i]', function(response) {
    console.log(response.country_code);
}, 'jsonp');
</script>


EOT;
            
        }
    }
	
	echo $scripts;
}





function ip_data_for_wp_shortcodes($atts = [], $contents = null)
{
    
 return "<span class='ipdataforwp_".$atts['id']."'>".$contents."</span>";
	

}
add_shortcode('ipdataforwp', 'ip_data_for_wp_shortcodes');


function ip_data_for_wp_wrap_shortcodes($atts = [], $contents = null)
{
  if($atts['content'] !== "default"){  
  return "<span style='display:none;' class='ipdataforwp_".$atts['id']."_content_".$atts['content']."'>".$contents."</span>"; 
  }
  else{
	return "<span style='display:none;' class='ipdataforwp_".$atts['id']."_default'>".$contents."</span>";   
	  
  }	

}
add_shortcode('ipdataforwp', 'ip_data_for_wp_wrap_shortcodes');

?>