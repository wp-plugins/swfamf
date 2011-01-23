<?php
	/*
	Plugin Name: swf amf
	Plugin URI: http://wordpress.org/#swf_amf
	Description: AMFPHP plugin integration with Wordpress services
	Author: Julien Barbay
	Version: 1
	Author URI: http://blog.martian-arts.org/
	*/
	
	function amf_install()
	{
		global $wpdb;		$table_name = $wpdb->prefix."amf_permissions";
		
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)		{			$sql = "CREATE TABLE ".$table_name." (				  id mediumint(9) NOT NULL AUTO_INCREMENT,				  method text NOT NULL,				  role text NOT NULL,				  UNIQUE KEY id (id)				);";
				
			require_once(ABSPATH."wp-admin/includes/upgrade.php");			dbDelta($sql);	
				
			require_once("swf_amf_defaults.php");
			foreach($defaults as $key => $value) { $wpdb->insert($table_name, array("method" => $key, "role" => $value)); }
		
			add_option("amf_activation", "yes", "", false);
			add_option("amf_debugmode", "no", "", false);
		}
		else { update_option("amf_activation", "yes"); }
	}
	
	function amf_uninstall() { update_option("amf_activation", "no"); }
	
	function amf_admin() { add_action("admin_menu", "amf_menu"); }	function amf_menu()	{ add_submenu_page("plugins.php", __("SWF Amf"), __("SWF Amf"), "manage_options", "swf-amf-config", "amf_panel"); }		function amf_panel()	{
		include("swf_amf_defaults.php");
		
		global $wpdb;		$table_name = $wpdb->prefix."amf_permissions";

		if (isset($_POST["id"]) && isset($_POST["role"]))
		{
			if ($_POST["id"] == "debugmode") { echo "debug"; update_option("amf_debugmode", $_POST["role"]); return; }
			$wpdb->query('UPDATE '.$table_name.' SET role="'.$_POST['role'].'" WHERE id='.$_POST['id']);
		}
		
		$data = $wpdb->get_results("SELECT * FROM ".$table_name, OBJECT);	
?>
		<style>			#amf_plugin { width:99%; }						#amf_title { margin: 24px 0px 18px 0px; }			#amf_title h2			{				float: left;				margin: -2px 10px 0px 0px;			}						#amf_title a { margin-top: -2px; }						#amf_roles
			{
				width:100%;
				margin-bottom:20px;
			}			#amf_roles tbody tr { height: 35px; }			#amf_roles tbody tr td { vertical-align: middle; }			#amf_roles .buttons { width: 45px; }			
			.amf_roles { width:70%; }
						#amf_infos { margin-bottom:20px; }						#amf_infos h3 { margin:0px 0px 0px 10px; }		</style>
		
		<script type="text/javascript">
			function amf_update(id)
			{
				document.getElementById("form_id").value = id;
				document.getElementById("form_role").value = document.getElementById("amf_roles_" + id).value;
				document.getElementById("amf_form").submit();
			}
			
			function amf_debugmode(value)
			{
				document.getElementById("form_id").value = "debugmode";
				document.getElementById("form_role").value = (value == 1) ? "yes" : "no";
				document.getElementById("amf_form").submit();
			}
		</script>
		
		<div id="amf_plugin">		<div id="amf_title">			<h2><?php _e("SWF Amf"); ?></h2>
			<?php $debugmode = get_option("amf_debugmode") == "yes"; ?>
			<a href="#" class="button" onclick="amf_debugmode(<?php echo !$debugmode; ?>)"><?php !$debugmode ? _e("activate debug mode") : _e("deactivate debug mode") ?></a>		</div>

		<div id="amf_infos">			<h3>• <?php _e("Infos"); ?></h3>			<p>				<?php _e("The plugin is simple. It provides a standard gateway for flash front theme development. It allows you to specify which role can access to functions of the wordpress class, so some sensible functions can still be accessible while being protected by the wordpress login routine."); ?>
			</p>
			<p>
				<?php _e("• The client side api is available in "); ?><a href="<?php echo get_bloginfo('wpurl')?>/wp-content/plugins/swf_amf/clientside/api.zip"><?php _e("the plugin's directory") ?></a>.<br/>
				<?php _e("• The debug mode enables the default "); ?><a href="<?php echo get_bloginfo('wpurl')?>/wp-content/plugins/swf_amf/system/browser/"><?php _e("amfphp service browser"); ?></a>.			</p>		</div>

		<form id="amf_form" action="" method="post">				<input id="form_id" type="hidden" name="id" value="" />
			<input id="form_role" type="hidden" name="role" value="" />
		</form>
		
		<table id="amf_roles" class="widefat">			<thead>				<tr>					<th style="" class="manage-column" id="amf_method_col" scope="col">AMF Method</th>					<th style="" class="manage-column" id="amf_roles_col" scope="col">Allowed from</th>					<th style="" class="manage-column buttons" id="swf_edit_col" scope="col"></th>				</tr>			</thead>			<tfoot>				<tr>					<th style="" class="manage-column" id="amf_method_col" scope="col">AMF Method</th>					<th style="" class="manage-column" id="amf_roles_col" scope="col">Allowed from</th>
					<th style="" class="manage-column buttons" id="swf_edit_col" scope="col"></th>
				</tr>			</tfoot>			<tbody>				<?php foreach($data as $item) : ?>								<tr id="amf_permission_<?php echo $item->id; ?>">						<td id="amf_method_<?php echo $item->id; ?>"><a href="http://wordpress.org/search/<?php echo $item->method; ?>"><?php echo $item->method; ?></a></td>						<td id="swf_role_<?php echo $item->id; ?>">
						
							<select id="amf_roles_<?php echo $item->id; ?>" class="amf_roles">
								<?php foreach($roles as $role) : ?>
									<option <?php if($role == $item->role) { echo 'selected="selected"'; } ?>><?php echo $role; ?></option> 
								<?php endforeach; ?>
							</select>
						</td>						
						<td class="buttons">
							<a href="#" class="button" onclick="amf_update('<?php echo $item->id; ?>');"><?php _e("update"); ?></a>
						</td>					</tr>								<?php endforeach; ?>			</tbody>		</table>
		
		</div>
<?php	
	}
	
	
	
	//ADMIN HOOKS
	register_activation_hook(__FILE__, "amf_install");
	register_deactivation_hook(__FILE__, "amf_uninstall");
	
	add_action("init", "amf_admin");
?>