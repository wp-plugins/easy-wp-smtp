<?php
function easy_wp_smtp_admin(){
	add_options_page('Easy WP SMTP Options', 'Easy WP SMTP','manage_options', __FILE__, 'easy_wp_smtp_options_page');
}

function easy_wp_smtp_options_page()
{
    global $ewpsOptions, $phpmailer;
    $smtp_debug = "";
    $exceptionmsg = "";
    $full_debug = false;
    if(isset($_POST['easy_wp_smtp_update']))
    {
        $ewpsOptions = array();
        $ewpsOptions["from"] = trim(stripslashes($_POST['easy_wp_smtp_from']));
        $ewpsOptions["fromname"] = trim(stripslashes($_POST['easy_wp_smtp_fromname']));
        $ewpsOptions["host"] = trim(stripslashes($_POST['easy_wp_smtp_host']));
        $ewpsOptions["smtpsecure"] = trim($_POST['easy_wp_smtp_smtpsecure']);
        $ewpsOptions["port"] = trim(stripslashes($_POST['easy_wp_smtp_port']));
        $ewpsOptions["smtpauth"] = trim($_POST['easy_wp_smtp_smtpauth']);
        $ewpsOptions["username"] = trim(stripslashes($_POST['easy_wp_smtp_username']));
        $ewpsOptions["password"] = trim(stripslashes($_POST['easy_wp_smtp_password']));
        $ewpsOptions["debug"] = (isset($_POST['easy_wp_smtp_enable_debug'])) ? trim($_POST['easy_wp_smtp_enable_debug']) : "";
        $ewpsOptions["deactivate"] = (isset($_POST['easy_wp_smtp_deactivate'])) ? trim($_POST['easy_wp_smtp_deactivate']) : "";
        update_option("easy_wp_smtp_options",$ewpsOptions);
        if(!is_email($ewpsOptions["from"])){
                echo '<div id="message" class="updated fade"><p><strong>From</strong> field must contain a valid email address</p></div>';
        }
        elseif(empty($ewpsOptions["host"])){
                echo '<div id="message" class="updated fade"><p><strong>Host</strong> field cannot be left empty</p></div>';
        }
        else{
                echo '<div id="message" class="updated fade"><p>Options saved</p></div>';
        }
    }
    if(isset($_POST['easy_wp_smtp_test']))
    {
        $to = trim($_POST['easy_wp_smtp_to']);
        $subject = trim($_POST['easy_wp_smtp_subject']);
        $message = trim($_POST['easy_wp_smtp_message']);
        $failed = 0;
        $empty_fields = false;
        if(empty($to) || empty($subject) || empty($message))
        {
            echo '<div id="message" class="updated fade"><p>You must fill in the <strong>To</strong>, <strong>Subject</strong> and <strong>Message</strong> fields to send a test email</p></div>';
            $empty_fields = true;
        }
        if(!$empty_fields)
        {
            $full_debug = true;
            ob_start();
            try
            {
                    $result = wp_mail($to,$subject,$message);
            }
            catch(phpmailerException $e)
            {
                    $failed = 1;
                    $exceptionmsg = $e->errorMessage();
            }
            $smtp_debug = ob_get_clean();
            if(!empty($exceptionmsg))
            {
                echo '<div id="message" class="updated fade"><p>Exception thrown: '.$exceptionmsg.'</p></div>';
            }
            if(!$failed)
            {
                if($result==TRUE)
                {
                    echo '<div id="message" class="updated fade"><p>Email sent</p></div>';
                }
                else
                {
                    echo '<div id="message" class="updated fade"><p>Email could no be sent</p></div>';
                }
            }
        }
    }
?>
<div class="wrap">
<?php screen_icon(); ?>    
<h2>
Easy WP SMTP v<?php echo EASY_WP_SMTP_PLUGIN_VERSION; ?>
</h2>
    <div id="poststuff"><div id="post-body">
<div class="postbox">
<h3><label for="title">General Settings</label></h3>
<div class="inside">
<form action="" method="post" enctype="multipart/form-data" name="easy_wp_smtp_form">
    <p>Please check the <a href="http://wp-ecommerce.net/?p=2197" target="_blank">documentation</a> before you configure the general settings</p>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			From Email Address
		</th>
		<td>
			<label>
				<input type="text" name="easy_wp_smtp_from" value="<?php echo $ewpsOptions["from"]; ?>" size="43" />
			</label>
                        <p>The email address that will be used to send emails to your recipients</p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			From Name
		</th>
		<td>
			<label>
				<input type="text" name="easy_wp_smtp_fromname" value="<?php echo $ewpsOptions["fromname"]; ?>" size="43" />
			</label>
                        <p>The name your recipients will see as part of the "from" or "sender" value when they receive your message</p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			SMTP Host
		</th>
		<td>
			<label>
				<input type="text" name="easy_wp_smtp_host" value="<?php echo $ewpsOptions["host"]; ?>" size="43" />
			</label>
                        <p>Your outgoing mail server (example: smtp.gmail.com)</p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			Type of Encryption
		</th>
		<td>
			<label>
				<input name="easy_wp_smtp_smtpsecure" type="radio" value=""<?php if ($ewpsOptions["smtpsecure"] == '') { ?> checked="checked"<?php } ?> />
				None
			</label>
			&nbsp;
			<label>
				<input name="easy_wp_smtp_smtpsecure" type="radio" value="ssl"<?php if ($ewpsOptions["smtpsecure"] == 'ssl') { ?> checked="checked"<?php } ?> />
				SSL
			</label>
			&nbsp;
			<label>
				<input name="easy_wp_smtp_smtpsecure" type="radio" value="tls"<?php if ($ewpsOptions["smtpsecure"] == 'tls') { ?> checked="checked"<?php } ?> />
				TLS
			</label>
                        <p>For most servers SSL is the recommended option</p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			SMTP Port
		</th>
		<td>
			<label>
				<input type="text" name="easy_wp_smtp_port" value="<?php echo $ewpsOptions["port"]; ?>" size="43" />
			</label>
                        <p>The port that will be used to relay outbound mail to your mail server (example: 465)</p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			SMTP Authentication
		</th>
		<td>
			<label>
				<input name="easy_wp_smtp_smtpauth" type="radio" value="no"<?php if ($ewpsOptions["smtpauth"] == 'no') { ?> checked="checked"<?php } ?> />
				No
			</label>
			&nbsp;
			<label>
				<input name="easy_wp_smtp_smtpauth" type="radio" value="yes"<?php if ($ewpsOptions["smtpauth"] == 'yes') { ?> checked="checked"<?php } ?> />
				Yes
			</label>
                        <p>This option should always be checked "Yes".</p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			Username
		</th>
		<td>
			<label>
				<input type="text" name="easy_wp_smtp_username" value="<?php echo $ewpsOptions["username"]; ?>" size="43" />
			</label>
                        <p>The username that you use to login to your mail server (example: abc123@gmail.com)</p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			Password
		</th>
		<td>
			<label>
				<input type="password" name="easy_wp_smtp_password" value="<?php echo $ewpsOptions["password"]; ?>" size="43" />
			</label>
                        <p>The password that you use to login to your mail server</p>
		</td>
	</tr>
    <tr valign="top">
        <th scope="row">
            Enable SMTP Debug
        </th>
        <td>
            <label>
                <input type="checkbox" name="easy_wp_smtp_enable_debug" value="yes" <?php if($ewpsOptions["debug"]=='yes') echo 'checked="checked"'; ?> />
                If enabled the SMTP debug output will be printed on the screen. This option is very useful if you are having issues with sending emails.
            </label>
        </td>
    </tr>
        <!--
	<tr valign="top">
		<th scope="row">
			Delete Options
		</th>
		<td>
			<label>
				<input type="checkbox" name="easy_wp_smtp_deactivate" value="yes" <?php if($ewpsOptions["deactivate"]=='yes') echo 'checked="checked"'; ?> />
				Automatically Delete options when you deactivate the plugin
			</label>
		</td>
	</tr>
        -->
</table>

<p class="submit">
<input type="hidden" name="easy_wp_smtp_update" value="update" />
<input type="submit" class="button-primary" name="Submit" value="<?php _e('Save Changes'); ?>" />
</p>

</form>
</div></div>

<div class="postbox">
<h3><label for="title">Testing & Debugging Settings</label></h3>
<div class="inside">
<form action="" method="post" enctype="multipart/form-data" name="wp_smtp_testform">
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			To:
		</th>
		<td>
			<label>
				<input type="text" name="easy_wp_smtp_to" value="" size="43" />
			</label>
                        <p>Enter the email address of the recipient </p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			Subject:
		</th>
		<td>
			<label>
				<input type="text" name="easy_wp_smtp_subject" value="" size="43" />
			</label>
                        <p>Enter a subject for your message</p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			Message:
		</th>
		<td>
			<label>
				<textarea type="text" name="easy_wp_smtp_message" value="" cols="45" rows="5"></textarea>
			</label>
                        <p>Write your message</p>
		</td>
	</tr>
</table>
<p class="submit">
<input type="hidden" name="easy_wp_smtp_test" value="test" />
<input type="submit" class="button-primary" value="Send Test Email" />
</p>
</form>
</div></div>
<?php
    if(!empty($smtp_debug))
    {
        ?>
        SMTP Debug:
        <p>
        <textarea type="text" cols="50" rows="20"><?php echo $smtp_debug;?></textarea>
        </p>
        <?php
    }
    if($full_debug)
    {
        ?>
        Full Debug:
        <p>
        <textarea type="text" cols="50" rows="20"><?php var_dump($phpmailer);?></textarea>
        </p>
        <?php
    }
?>
    </div></div>
</div>
<?php 
}
add_action('admin_menu', 'easy_wp_smtp_admin');
?>