<?php
global $blogvault;
global $bvNotice;
global $bvPtnAdminPage;
$bvNotice = '';
$bvPtnAdminPage = 'ptn-automated-migration';

if (!function_exists('bvPtnAdminUrl')) :
	function bvPtnAdminUrl($_params = '') {
		global $bvPtnAdminPage;
		if (function_exists('network_admin_url')) {
			return network_admin_url('admin.php?page='.$bvPtnAdminPage.$_params);
		} else {
			return admin_url('admin.php?page='.$bvPtnAdminPage.$_params);
		}
	}
endif;

if (!function_exists('bvAddStyleSheet')) :
	function bvAddStyleSheet() {
		wp_register_style('form-styles', plugins_url('form-styles.css',__FILE__ ));
		wp_enqueue_style('form-styles');
	}
add_action( 'admin_init','bvAddStyleSheet');
endif;

if (!function_exists('bvPtnAdminInitHandler')) :
	function bvPtnAdminInitHandler() {
		global $bvNotice, $blogvault;
		global $sidebars_widgets;
		global $wp_registered_widget_updates;

		if (!current_user_can('activate_plugins'))
			return;

		if (isset($_REQUEST['bvnonce']) && wp_verify_nonce($_REQUEST['bvnonce'], "bvnonce")) {
			if (isset($_REQUEST['blogvaultkey'])) {
				if ((strlen($_REQUEST['blogvaultkey']) == 64)) {
					$keys = str_split($_REQUEST['blogvaultkey'], 32);
					$blogvault->updatekeys($keys[0], $keys[1]);
					bvActivateHandler();
					$bvNotice = "<b>Activated!</b> blogVault is now backing up your site.<br/><br/>";
					if (isset($_REQUEST['redirect'])) {
						$location = $_REQUEST['redirect'];
						wp_redirect("https://webapp.blogvault.net/migration/".$location);
						exit();
					}
				} else {
					$bvNotice = "<b style='color:red;'>Invalid request!</b> Please try again with a valid key.<br/><br/>";
				}
			}
		}

		if ($blogvault->getOption('bvActivateRedirect') === 'yes') {
			$blogvault->updateOption('bvActivateRedirect', 'no');
			wp_redirect(bvPtnAdminUrl());
		}
	}
	add_action('admin_init', 'bvPtnAdminInitHandler');
endif;

if (!function_exists('bvPtnAdminMenu')) :
	function bvPtnAdminMenu() {
		global $bvPtnAdminPage;
		add_menu_page('Pantheon Migrate', 'Pantheon Migrate', 'manage_options', $bvPtnAdminPage, 'bvPtnMigrate');
	}
	if (function_exists('is_multisite') && is_multisite()) {
		add_action('network_admin_menu', 'bvPtnAdminMenu');
	} else {
		add_action('admin_menu', 'bvPtnAdminMenu');
	}
endif;

if ( !function_exists('bvSettingsLink') ) :
	function bvSettingsLink($links, $file) {
		if ( $file == plugin_basename( dirname(__FILE__).'/blogvault.php' ) ) {
			$links[] = '<a href="'.bvPtnAdminUrl().'">'.__( 'Settings' ).'</a>';
		}
		return $links;
	}
	add_filter('plugin_action_links', 'bvSettingsLink', 10, 2);
endif;

if ( !function_exists('bvPtnMigrate') ) :
	function bvPtnMigrate() {
		global $blogvault, $bvNotice;
		$_error = NULL;
		if (array_key_exists('error', $_REQUEST)) {
			$_error = $_REQUEST['error'];
		}
?>
		<div class="logo-container" style="padding: 50px 0px 10px 20px">
			<a href="http://blogvault.net/" style="padding-right: 20px;"><img src="<?php echo plugins_url('logo.png', __FILE__); ?>" /></a>
		</div>

		<div id="wrapper toplevel_page_ptn-automated-migration">
			<form dummy=">" action="https://webapp.blogvault.net/home/migrate" style="padding:0 2% 2em 1%;" method="post" name="signup">
				<h1>Migrate Site to Pantheon</h1>
				<p><font size="3">This plugin makes it very easy to migrate your site to Pantheon</font></p>
<?php if ($_error == "email") { 
	echo '<div class="error" style="padding-bottom:0.5%;"><p>There is already an account with this email.</p></div>';
} else if ($_error == "blog") {
	echo '<div class="error" style="padding-bottom:0.5%;"><p>Could not create an account. Please contact <a href="http://blogvault.net/contact/">blogVault Support</a></p></div>';
} else if (($_error == "custom") && isset($_REQUEST['bvnonce']) && wp_verify_nonce($_REQUEST['bvnonce'], "bvnonce")) {
	echo '<div class="error" style="padding-bottom:0.5%;"><p>'.base64_decode($_REQUEST['message']).'</p></div>';
}
?>
				<input type="hidden" name="bvsrc" value="wpplugin" />
				<input type="hidden" name="migrate" value="pantheon" />
				<input type="hidden" name="type" value="sftp" />
				<input type="hidden" name="setkeysredirect" value="true" />
				<input type="hidden" name="url" value="<?php echo $blogvault->wpurl(); ?>" />
				<input type="hidden" name="secret" value="<?php echo $blogvault->getOption('bvSecretKey'); ?>">
				<input type='hidden' name='bvnonce' value='<?php echo wp_create_nonce("bvnonce") ?>'>
				<input type='hidden' name='serverip' value='<?php echo $_SERVER["SERVER_ADDR"] ?>'>
				<input type='hidden' name='adminurl' value='<?php echo bvPtnAdminUrl(); ?>'>
				<input type="hidden" name="multisite" value="<?php var_export($blogvault->isMultisite()); ?>" />
				<div class="row-fluid">
					<div class="span5" style="border-right: 1px solid #EEE; padding-top:1%;">
						<label id='label_email'>Email</label>
			 			<div class="control-group">
							<div class="controls">
								<input type="text" id="email" name="email" placeholder="ex. user@mydomain.com">
							</div>
						</div>
						<label class="control-label" for="input02">Destination Site URL</label>
						<div class="control-group">
							<div class="controls">
								<input type="text" class="input-large" name="newurl" placeholder="http://example.pantheon.io">
							</div>
						</div>
						<label class="control-label" for="inputip">
							SFTP Server Address
							<span style="color:#162A33">(of the destination server)</span>
						</label>
						<div class="control-group">
							<div class="controls">
								<input type="text" class="input-large" placeholder="ex. 123.456.789.101" name="address">
								<p class="help-block"></p>
							</div>
						</div>
						<label class="control-label" for="input01">SFTP Username</label>
						<div class="control-group">
							<div class="controls">
								<input type="text" class="input-large" placeholder="ex. installname" name="username">
								<p class="help-block"></p>
							</div>
						</div>
						<label class="control-label" for="input02">SFTP Password</label>
						<div class="control-group">
							<div class="controls">
								<input type="password" class="input-large" name="passwd">
							</div>
						</div>
<?php if (array_key_exists('auth_required_source', $_REQUEST)) { ?>
						<label class="control-label" for="input02" style="color:red">User <small>(for this site)</small></label>
						<div class="control-group">
							<div class="controls">
								<input type="text" class="input-large" name="httpauth_src_user">
							</div>
						</div>
						<label class="control-label" for="input02" style="color:red">Password <small>(for this site)</small></label>
						<div class="control-group">
							<div class="controls">
								<input type="password" class="input-large" name="httpauth_src_password">
							</div>
						</div>
<?php } ?>
<?php if (array_key_exists('auth_required_dest', $_REQUEST)) { ?>
            <label class="control-label" for="input02" style="color:red">Username <small>(for Pantheon Install)</small></label>
            <div class="control-group">
              <div class="controls">
                <input type="text" class="input-large" name="httpauth_dest_user">
              </div>
            </div>
            <label class="control-label" for="input02" style="color:red">Password <small>(for Pantheon Install)</small></label>
            <div class="control-group">
              <div class="controls">
                <input type="password" class="input-large" name="httpauth_dest_password">
              </div>
            </div>
<?php } ?>
					</div>
				</div>
				<input type='submit' value='Migrate'>
			</form>
		</div> <!-- wrapper ends here -->
<?php
	}
endif;