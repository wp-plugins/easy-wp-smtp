<?php
/*
Plugin Name: Easy WP SMTP
Version: 1.0.7
Plugin URI: http://wp-ecommerce.net/?p=2197
Author: wpecommerce
Author URI: http://wp-ecommerce.net/
Description: Send email via SMTP from your WordPress Blog
*/
define('EASY_WP_SMTP_PLUGIN_VERSION', "1.0.7");
$ewpsOptions = get_option("easy_wp_smtp_options");

function easy_wp_smtp($phpmailer){
	global $ewpsOptions;
	if( !is_email($ewpsOptions["from"]) || empty($ewpsOptions["host"]) ){
		return;
	}
	$phpmailer->Mailer = "smtp";
	$phpmailer->From = $ewpsOptions["from"];
	$phpmailer->FromName = $ewpsOptions["fromname"];
	$phpmailer->Sender = $phpmailer->From; //Return-Path
	//$phpmailer->AddReplyTo($phpmailer->From,$phpmailer->FromName); //Reply-To
	$phpmailer->Host = $ewpsOptions["host"];
	$phpmailer->SMTPSecure = $ewpsOptions["smtpsecure"];
	$phpmailer->Port = $ewpsOptions["port"];
	$phpmailer->SMTPAuth = ($ewpsOptions["smtpauth"]=="yes") ? TRUE : FALSE;
	if($phpmailer->SMTPAuth){
		$phpmailer->Username = $ewpsOptions["username"];
		$phpmailer->Password = $ewpsOptions["password"];
	}
    if($ewpsOptions["debug"]=="yes")
    {
        $phpmailer->SMTPDebug = 2;
    }
}
add_action('phpmailer_init','easy_wp_smtp');

function easy_wp_smtp_activate(){
	$ewpsOptions = array();
	$ewpsOptions["from"] = "";
	$ewpsOptions["fromname"] = "";
	$ewpsOptions["host"] = "";
	$ewpsOptions["smtpsecure"] = "";
	$ewpsOptions["port"] = "";
	$ewpsOptions["smtpauth"] = "yes";
	$ewpsOptions["username"] = "";
	$ewpsOptions["password"] = "";
    $ewpsOptions["debug"] = "";
	$ewpsOptions["deactivate"] = "";
	add_option("easy_wp_smtp_options",$ewpsOptions);
}
register_activation_hook( __FILE__ , 'easy_wp_smtp_activate' );

if($ewpsOptions["deactivate"]=="yes"){
	register_deactivation_hook( __FILE__ , create_function('','delete_option("easy_wp_smtp_options");') );
}

function easy_wp_smtp_settings_link($action_links,$plugin_file){
	if($plugin_file==plugin_basename(__FILE__)){
		$ewps_settings_link = '<a href="options-general.php?page=' . dirname(plugin_basename(__FILE__)) . '/easy_wp_smtp_admin.php">' . __("Settings") . '</a>';
		array_unshift($action_links,$ewps_settings_link);
	}
	return $action_links;
}
add_filter('plugin_action_links','easy_wp_smtp_settings_link',10,2);

if(is_admin()){require_once('easy_wp_smtp_admin.php');}

?>