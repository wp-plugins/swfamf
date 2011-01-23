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
		global $wpdb;
		
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
				
			require_once(ABSPATH."wp-admin/includes/upgrade.php");
				
			require_once("swf_amf_defaults.php");
			foreach($defaults as $key => $value) { $wpdb->insert($table_name, array("method" => $key, "role" => $value)); }
		
			add_option("amf_activation", "yes", "", false);
			add_option("amf_debugmode", "no", "", false);
		}
		else { update_option("amf_activation", "yes"); }
	}
	
	function amf_uninstall() { update_option("amf_activation", "no"); }
	
	function amf_admin() { add_action("admin_menu", "amf_menu"); }
		include("swf_amf_defaults.php");
		
		global $wpdb;

		if (isset($_POST["id"]) && isset($_POST["role"]))
		{
			if ($_POST["id"] == "debugmode") { echo "debug"; update_option("amf_debugmode", $_POST["role"]); return; }
			$wpdb->query('UPDATE '.$table_name.' SET role="'.$_POST['role'].'" WHERE id='.$_POST['id']);
		}
		
		$data = $wpdb->get_results("SELECT * FROM ".$table_name, OBJECT);	
?>
		<style>
			{
				width:100%;
				margin-bottom:20px;
			}
			.amf_roles { width:70%; }
			
		
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
		
		<div id="amf_plugin">
			<?php $debugmode = get_option("amf_debugmode") == "yes"; ?>
			<a href="#" class="button" onclick="amf_debugmode(<?php echo !$debugmode; ?>)"><?php !$debugmode ? _e("activate debug mode") : _e("deactivate debug mode") ?></a>

		<div id="amf_infos">
			</p>
			<p>
				<?php _e("• The client side api is available in "); ?><a href="<?php echo get_bloginfo('wpurl')?>/wp-content/plugins/swf_amf/clientside/api.zip"><?php _e("the plugin's directory") ?></a>.<br/>
				<?php _e("• The debug mode enables the default "); ?><a href="<?php echo get_bloginfo('wpurl')?>/wp-content/plugins/swf_amf/system/browser/"><?php _e("amfphp service browser"); ?></a>.

		<form id="amf_form" action="" method="post">	
			<input id="form_role" type="hidden" name="role" value="" />
		</form>
		
		<table id="amf_roles" class="widefat">
					<th style="" class="manage-column buttons" id="swf_edit_col" scope="col"></th>
				</tr>
						
							<select id="amf_roles_<?php echo $item->id; ?>" class="amf_roles">
								<?php foreach($roles as $role) : ?>
									<option <?php if($role == $item->role) { echo 'selected="selected"'; } ?>><?php echo $role; ?></option> 
								<?php endforeach; ?>
							</select>
						</td>
						<td class="buttons">
							<a href="#" class="button" onclick="amf_update('<?php echo $item->id; ?>');"><?php _e("update"); ?></a>
						</td>
		
		</div>
<?php	
	}
	
	
	
	//ADMIN HOOKS
	register_activation_hook(__FILE__, "amf_install");
	register_deactivation_hook(__FILE__, "amf_uninstall");
	
	add_action("init", "amf_admin");
?>