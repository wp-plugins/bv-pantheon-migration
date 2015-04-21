<?php
global $blogvault;
global $bvNotice;
$bvNotice = "";

define('BVMIGRATEPLUGIN', true);

if (!function_exists('bvAddStyleSheet')) :
	function bvAddStyleSheet() {
		wp_register_style('form-styles', plugins_url('form-styles.css',__FILE__ ));
		wp_enqueue_style('form-styles');
	}
add_action( 'admin_init','bvAddStyleSheet');
endif;

if (!function_exists('bvPantheonAdminInitHandler')) :
	function bvPantheonAdminInitHandler() {
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
						wp_redirect("https://webapp.blogvault.net/dash/redir?q=".urlencode($location));
						exit();
					}
				} else {
					$bvNotice = "<b style='color:red;'>Invalid request!</b> Please try again with a valid key.<br/><br/>";
				}
			}
		}

		if ($blogvault->getOption('bvActivateRedirect') === 'yes') {
			$blogvault->updateOption('bvActivateRedirect', 'no');
			wp_redirect('admin.php?page=bv-pantheon-migrate');
		}
	}
	add_action('admin_init', 'bvPantheonAdminInitHandler');
endif;

if (!function_exists('bvPantheonAdminMenu')) :
	function bvPantheonAdminMenu() {
		add_menu_page('bV Pantheon', 'bV Pantheon', 'manage_options', 'bv-pantheon-migrate', 'bvPantheonMigrate');
	}
	add_action('admin_menu', 'bvPantheonAdminMenu');
endif;

if ( !function_exists('bvSettingsLink') ) :
	function bvSettingsLink($links, $file) {
		if ( $file == plugin_basename( dirname(__FILE__).'/blogvault.php' ) ) {
			$links[] = '<a href="' . admin_url( 'admin.php?page=bv-pantheon-migrate' ) . '">'.__( 'Settings' ).'</a>';
		}
		return $links;
	}
	add_filter('plugin_action_links', 'bvSettingsLink', 10, 2);
endif;

if ( !function_exists('bvPantheonMigrate') ) :
	function bvPantheonMigrate() {
		global $blogvault, $bvNotice;
		$_error = NULL;
		if (isset($_GET['error'])) {
			$_error = $_GET['error'];
		}
?>
	<a href="http://blogvault.net/" style="float:right;padding: 1% 1% 0 0"><img src="<?php echo plugins_url('logo.png', __FILE__); ?>" /></a>
<?php
		echo '<h2 style="padding-top:1%;" class="nav-tab-wrapper" id="wpseo-tabs">';
		if ($_GET["tutorial"]) {
			echo '<a class="nav-tab" id="migrate-tab" href="'.admin_url("admin.php?page=bv-pantheon-migrate").'">Migrate</a>';
			echo '<a class="nav-tab nav-tab-active" id="infobox-tab" href="'.admin_url("admin.php?page=bv-pantheon-migrate&tutorial=true").'">Quick Tutorial</a>';
		} else {
			echo '<a class="nav-tab nav-tab-active" id="migrate-tab" href="'.admin_url("admin.php?page=bv-pantheon-migrate").'">Migrate</a>';
			echo '<a class="nav-tab" id="infobox-tab" href="'.admin_url("admin.php?page=bv-pantheon-migrate&tutorial=true").'">Quick Tutorial</a>';
		}
		echo '</h2>';
?>
<?php if ($_GET["tutorial"]) {
	// PUT TUTORIAL HERE
?>
	<h1>How to get Pantheon SFTP Credentials</h1>
	<p>blogVault requires SFTP credentials to copy files from your current site to the destination Pantheon site. This information can easily be retrieved from your Pantheon dashboard.<p>
	<ol>
		<li>Login to Pantheon Dashboard and select your site.</li>
		<li>Click on <i>Visit Development Site</i>. This will redirect you to your development site. Copy the url of the site and enter it in <strong><a href="<?php echo admin_url('admin.php?page=bv-pantheon-migrate') ?>">Pantheon URL</a></strong><br/>
			<img src="<?php echo plugins_url('pantheon-url.png', __FILE__); ?>"/>
		</li>
		<li>Click on <i>Connection Info</i> present at top of site status box. In the dropdown look for <strong>SFTP</strong> section.</br>
				<img src="<?php echo plugins_url('connection_info.png', __FILE__); ?>"/>
		</li>
		<li>
			Copy Host inside SFTP and paste it in <strong><a href="<?php echo admin_url('admin.php?page=bv-pantheon-migrate') ?>">SFTP Server Address</a></strong>
		</li>
		<li>
			Username can be directly copied from Username box under SFTP and pasted in <strong><a href="<?php echo admin_url('admin.php?page=bv-pantheon-migrate') ?>">SFTP Username</a></strong>.
		</li>
		<li>
			As clear from the picture above, password is same as your Pantheon dashboard password. Enter it under <strong><a href="<?php echo admin_url('admin.php?page=bv-pantheon-migrate') ?>">SFTP Password</a></strong>.
		</li>
	</ol>
<?php
} else {
	// PUT FORM HERE
?>
	<form rel="canonical" action="https://webapp.blogvault.net/home/api_signup" style="padding:0 2% 2em 1%;" method="post" name="signup">
	<h1>Migrate Site</h1>
	<p><font size="3">This Plugin makes it very easy to migrate your site to Pantheon</font></p>
<?php if ($_error == "email") { 
	echo '<div class="error" style="padding-bottom:0.5%;"><p>There is already an account with this email.</p></div>';
} else if ($_error == "blog") {
	echo '<div class="error" style="padding-bottom:0.5%;"><p>Could not create an account. Please contact <a href="http://blogvault.net/contact/">blogVault Support</a></p></div>';
} else if (($_error == "custom") && isset($_REQUEST['bvnonce']) && wp_verify_nonce($_REQUEST['bvnonce'], "bvnonce")) {
	echo '<div class="error" style="padding-bottom:0.5%;"><p>'.base64_decode($_GET['message']).'</p></div>';
}
?>
	<input type="hidden" name="bvsrc" value="wpplugin" />
	<input type="hidden" name="migrate" value="pantheon" />
	<input type="hidden" name="loc" value="MIGRATE3FREE" />
	<input type="hidden" name="type" value="sftp" />
	<input type="hidden" name="url" value="<?php echo $blogvault->wpurl(); ?>" />
	<input type="hidden" name="secret" value="<?php echo $blogvault->getOption('bvSecretKey'); ?>">
	<input type='hidden' name='bvnonce' value='<?php echo wp_create_nonce("bvnonce") ?>'>
	<div class="row-fluid">
		<div class="span5" style="border-right: 1px solid #EEE; padding-top:1%;">
			<label id='label_email'>Email</label>
			 <div class="control-group">
				<div class="controls">
					<input type="text" id="email" name="email" value="<?php echo get_option('admin_email');?>">
				</div>
			</div>
			<label class="control-label" for="input02">Pantheon URL</label>
			<div class="control-group">
				<div class="controls">
					<input type="text" class="input-large" name="newurl">
				</div>
			</div>
			<label class="control-label" for="inputip">
				SFTP Server Address
				<span style="color:#82CC39">(of the destination server)</span>
			</label>
			<div class="control-group">
				<div class="controls">
					<input type="text" class="input-large" name="address">
					<p class="help-block"></p>
				</div>
			</div>
			<label class="control-label" for="input01">SFTP Username</label>
			<div class="control-group">
				<div class="controls">
					<input type="text" class="input-large" name="username">
					<p class="help-block"></p>
				</div>
			</div>
			<label class="control-label" for="input02">SFTP Password</label>
			<div class="control-group">
				<div class="controls">
					<input type="password" class="input-large" name="passwd">
				</div>
			</div>
		</div>
	</div>
	<input type='submit' value='Migrate' id='submitbutton'>
	</div>
</form>
<?php
}
	}
endif;