<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public/partials
 */
?>
<script src="<?php echo MUP_PLUGIN_URL . 'public/js/script.js'; ?>"></script>
<link rel="stylesheet" href="<?php echo MUP_PLUGIN_URL . 'public/css/style.css'; ?>"/>
<nav class="newnaw nav-tab-wrapper woo-nav-tab-wrapper">
			<a href="admin.php?page=morkvaup_plugin" class="nav-tab  nav-tab-active ">Налаштування</a><a href="admin.php?page=morkvaup_invoice" class="nav-tab">Нове відправлення</a><a href="admin.php?page=morkvaup_invoices" class="nav-tab ">Мої відправлення</a><a href="admin.php?page=morkvaup_about" class="nav-tab ">Про плагін</a></nav>


<div class="container">
<div class="row">
	<h1><?php echo MUP_PLUGIN_NAME; ?></h1>
	<?php settings_errors(); ?>
	<hr>

<div class="settingsgrid">
	<div class="w70">
	<div class="tab-content">
		<div id="tab-1" class="tab-pane active">
			<form method="post" action="options.php">
				<?php
					settings_fields( 'morkvaup_options_group' );
					do_settings_sections( 'morkvaup_plugin' );
					submit_button();
				?>
			</form>

		</div>


		<div class="clear"></div>
	</div>
	</div>
	<div>
<?php include 'card.php' ; ?>
</div>
	<div class="clear"></div>
</div>
	</div>
</div>
</div>
