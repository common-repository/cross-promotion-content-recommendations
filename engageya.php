<?php
/*
	Plugin Name: Cross-Promotion Content Recommendations by Engageya
	Plugin URI: http://www.Engageya.com/
	Description: Displays recommendations to your blog and other blogs, at the foot of each of your posts - for enhanced user retention as well as new engaged traffic, for free
	Version: 2.3.1
	Author:  Engageya
*/

$engageya_params = "";
$engageya_dbnurls = array();
$engageya_dbnanchors = array();
$engageya_already_shown = false;

function engageya_need_to_place_widget($content)
{
	return (is_single() || engageya_show_recommendations($content)) && (!is_feed() && !is_page());
}

function engageya_get_url_contents($url){
	$engageya_crl = curl_init();
        curl_setopt($engageya_crl,CURLOPT_COOKIE,session_name()."=".session_id().";");
	$engageya_timeout = 5;
        $ckfile = tempnam ("/tmp", "CURLCOOKIE");
        curl_setopt ($engageya_crl, CURLOPT_COOKIEJAR, $ckfile); 
        curl_setopt ($engageya_crl, CURLOPT_COOKIEFILE, $ckfile);
	curl_setopt ($engageya_crl, CURLOPT_URL,$url);
	curl_setopt ($engageya_crl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($engageya_crl, CURLOPT_CONNECTTIMEOUT, $engageya_timeout);
	$http_response = curl_exec($engageya_crl);
	curl_close($engageya_crl);
	return $http_response;
}

function engageya_get_url_contents2($url){
	$ckfile = tempnam ("/tmp", "CURLCOOKIE");
        
        /* STEP 2. visit the homepage to set the cookie properly */
        $ch = curl_init ($url);
        curl_setopt ($ch, CURLOPT_COOKIEJAR, $ckfile); 
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec ($ch);

        /* STEP 3. visit cookiepage.php */
        $ch = curl_init ($url);
        curl_setopt ($ch, CURLOPT_COOKIEFILE, $ckfile); 
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        return curl_exec ($ch);
}

function send_log($msg)
{
	$url = urlencode(get_bloginfo('wpurl'));
	$msg = urlencode($msg);
	$ip  = urlencode($_SERVER['REMOTE_ADDR']);
	engageya_get_url_contents("http://www.engageya.com/wordpress/error/?url=" . $url . "&msg=" . $msg . "&ip=" . $ip);
}

function engageya_add_widget($content) 
{
	try
	{
		global $engageya_already_shown;
		if (engageya_need_to_place_widget($content))
		{
			global $post, $engageya_dbnurls , $engageya_dbnanchors;
			$linkToPost = get_permalink($post->ID);		
		
			if  (!$engageya_already_shown)
			{
				$engageya_already_shown = true;
				array_push($engageya_dbnurls , $linkToPost);
				array_push($engageya_dbnanchors, "spark_static_widget_" . $post->ID);
			}
			if (strpos($content, '<div id="spark_static_widget_') === false)
			{
				$content .= '<div id="spark_static_widget_'. $post->ID .'"></div>';		
			}	
		}
	}
	catch (Exception $e)
	{
		if($engageya_number_of_logs > 0)
			send_log("Exception in engageya_add_widget: " . $e->getMessage());
	}
	return $content;
}

 function engageya_add_widget_script($content)
{
	//do not write tag on homepages, or pages with no dbnanchors
	global $engageya_already_shown;
	if (!$engageya_already_shown) {
		return $content;
	}

	global $engageya_dbnurls , $engageya_dbnanchors;
	
	$grazeit_script = '<script type="text/javascript" id="wordpress_grazit_script">		
		var dbnurls = [];
		var dbnanchors = [];
';
	for($i = 0; $i < sizeof($engageya_dbnurls ); $i++)
		$grazeit_script .= "		dbnurls.push('" . $engageya_dbnurls [$i] . "');\n";
	for($i = 0; $i < sizeof($engageya_dbnanchors); $i++)
		$grazeit_script .= "		dbnanchors.push('" . $engageya_dbnanchors[$i] . "');\n";
	$grazeit_script .= '		var script = document.createElement("script");
		script.setAttribute("src", dbn_protocol+"widget.engageya.com/sprk.1.0.2.js");
		script.setAttribute("type", "text/javascript");
		script.setAttribute("id", "grazit_script");
		document.getElementById("wordpress_grazit_script").parentNode.appendChild(script);
</script><div id="eng_force_layout0">'.get_option("engageya_params_layout_type_id").'</div>
';
	echo get_option("engageya_params_opt");
	echo $grazeit_script;
}

function engageya_initialize_widget() {

	global $engageya_params;
	if(!get_option("engageya_params_opt"))
	{
		if($engageya_params == ""){
			$engageya_params = engageya_get_url_contents("http://www.engageya.com/wordpress/register/?ver=" . engageya_get_version() .  "&url=" . urlencode (get_bloginfo('wpurl')));
                        
                        $_params = json_decode($engageya_params,true);
                        
                        $engageya_token = $_params["token"];
                        $engageya_params = $_params["script"];
                        $engageya_user_id = $_params["userid"];
		}
		update_option("engageya_params_opt", $engageya_params);
                update_option("engageya_params_token", $engageya_token);
                update_option("engageya_params_user_id", $engageya_user_id);
	}
	if (!get_option("engageya_params_layout_type_id"))
	{
		update_option("engageya_params_layout_type_id", 21);
	}
        
         
}


function engageya_get_version() {
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file = basename( ( __FILE__ ) );
	return $plugin_folder[$plugin_file]['Version'];
}

function engageya_register_custom_menu_page() {
    $plugin_folder = "cross-promotion-content-recommendations";
    $options = array(
        'page_title' => 'Engageya',
        'menu_title' => 'Engageya',
        'capability' => 'manage_options',
        'position'   => 99,
        'icon_url'   => plugins_url($plugin_folder.'/img/menu-logo.png')
    );

    add_menu_page($options['page_title'], $options['menu_title'], $options["capability"], 'eng_main', 'eng_main_menu',   $options['icon_url'], $options['position']);
    add_submenu_page('eng_main', 'Engageya', 'Dashboard','manage_options', 'eng_main');
    add_submenu_page('eng_main', 'Layouts', 'Layouts', 'manage_options', 'eng_my_plugin_options','eng_my_plugin_options');

}

function eng_main_menu(){
	include "engageya_admin.php";
}

function eng_my_plugin_options() {
	include "engageya_layouts.php";
}
include "templates.php";
engageya_add_all_templates();

add_action('wp_ajax_engageya_update_layout', 'engageya_update_layout');

function engageya_update_layout() {
	$selectedLayout = $_POST['data'];
	update_option("engageya_params_layout_type_id", $selectedLayout);
	echo $selectedLayout;
	exit();
}

add_filter('the_content', 'engageya_add_widget');
add_action('init', 'engageya_initialize_widget');
add_action('wp_footer', 'engageya_add_widget_script');
add_action('admin_menu', 'engageya_register_custom_menu_page');