<?php

	class Wordpress
	{
		public function beforeFilter($function_called)
		{
			if (get_option("amf_activation") != "yes") { return false; }
			
			global $wpdb;
			$table_name = $wpdb->prefix."amf_permissions";
			$role = $wpdb->get_results('SELECT role FROM '.$table_name.' WHERE method="'.$function_called.'"');
			
			//method unknown
			if (empty($role)) { return false; }
			
			$role = $role[0]->role;
			
			if ($role == "*") { return true; } //public
			else { return Authenticate::isUserInRole($role); } //private
			
			return false; //default
		}
		
		
		
		
		
		
		
		
		//***** usual public functions
		//*** posts
		public function get_post($id) { return get_post($id); }
		public function get_post_status($id) { return get_post_status($id); }
		
		
		public function get_single_post($id) { return wp_get_single_post($id); }
		public function get_recent_posts($num) { return wp_get_recent_posts($num); }
		public function get_posts($num = "", $offset = "", $category = "")
		{
			$args  = "numberposts=".$num;
			$args .= ($offset != "") ? "&offset=".$offset : "";
			$args .= ($category != "") ? "&category=".$category : "";
			
			return get_posts($args);
		}
		

		public function get_parents($id) { return get_post_ancestors($id); }
		public function get_children($num, $status = "any", $parent = 0)
		{
			$args = array('post_parent' => $parent,'numberposts' => $num,'post_status' => $status);
			return get_children($args);
		}
		
		public function get_next_post($same_category) { return get_next_post($same_category); }
		public function count_posts() { return wp_count_posts(); }
		
		
		public function get_post_custom($id) { return get_post_custom($id); }
		public function get_post_custom_keys($id) { return get_post_custom_keys($id); }
		public function get_post_custom_values($key, $id) { return get_post_custom_values($key, $id); }
		
		public function get_post_meta($id, $key) { return get_post_meta($id, $key); }
		
		
		
		
		
		//*** pages
		public function get_page($id) { return get_page($id); }
		public function get_pages($num, $offset = 0, $parent = 0)
		{
			$args = array("child_of" => $parent, "sort_order" => "ASC", "sort_column" => "ID", "number" => $num, "offset" => $offset);
			return get_pages($args);
		}
		
		public function get_page_link($id, $permalink = false) { return get_page_link($id, false, $permalink); }
		public function get_page_by_path($path) { return get_page_by_path($path); }
		public function get_page_by_title($title) { return get_page_by_title($title); }
		public function get_page_uri($id) { return get_page_uri($id); }
		public function get_all_page_ids() { return get_all_page_ids(); }
		public function is_page($id) { return is_page($id); }
		
		
		
		
		
		//*** bookmarks
		public function get_bookmark($id) { return get_bookmark($id); }
		public function get_bookmarks() { return get_bookmarks(); }
		
		
		
		
		
		//*** attachments
		public function get_attachment_url($id) { return wp_get_attachment_url($id); }
		public function get_attached_file($id) { return get_attached_file($id); }
		public function get_attachment_image_src($id, $thumb = false) { return wp_get_attachment_image_src($id, ($thumb ? "thumbnail" : "full")); }
		public function attachment_is_image($id) { return wp_attachment_is_image($id); } 
		
		
		
		
		
		//*** categories
		public function get_category($id) { return get_category($id); } 
		public function get_categories() { return get_categories(); } 
		public function get_cat_ID($name) { return get_cat_ID($name); } 
		public function get_cat_name($id) { return get_cat_name($id); } 
		public function get_category_link($id) { return get_category_link($id); } 
		public function get_all_category_ids() { return get_all_category_ids(); } 
		
		public function in_category($id, $post) { return in_category($id, $post); } 
		public function is_category($id) { return is_category($id); } 
		public function ancestor_of($cat1, $cat2) { return cat_is_ancestor_of($cat1, $cat2); } 
		
		
		
		
		
		//*** tags
		public function get_tag($id) { return get_tag($id); } 
		public function get_tag_link($id) { return get_tag_link($id); } 
		public function is_tag($id) { return is_tag($id); } 
		
		
		
		
		
		//*** feeds
		public function rss2($comments = false) { return do_feed_rss2($comments); } 
		public function feed_of_author($id) { return get_author_feed_link($id, ""); } 
		public function feed_of_category($id) { return get_category_feed_link($id); } 
		public function comment_link($id) { return get_comment_link($id); } 
		
		
		
		
		
		//*** users
		public function username_exists($name) { return username_exists($name); } 
		public function email_exists($email) { return email_exists($email); } 
		public function validate_username($name) { return validate_username($name); } 
		public function get_user_numposts($id) { return get_usernumposts($id); } 
		public function signon($login, $password, $remember = true, $cookie = true) 
		{
			if (user_pass_ok($login, $password))
			{
				$args = array('user_login' => $login, 'user_password' => $password, 'remember' => $remember);
				$signon = wp_signon($args, $cookie);
				
				Authenticate::login($login, implode(",", $signon->roles));
				
				return $signon;
			}
			
			return false;
		}
		public function is_user_logged_in() { return is_user_logged_in(); } 
		public function logout() { return wp_logout(); }
		
		
		
		
		
		//*** ping & trackbacks
		public function get_to_ping($id) { return get_to_ping($id); } 
		public function add_ping($id, $uri) { return add_ping($id, $uri); } 
		public function generic_ping($id) { return generic_ping($id); } 
		public function do_pings() { return do_all_pings(); } 
		public function do_trackbacks($id) { return do_trackbacks($id); } 		
		
		public function mail($to, $subject, $template, $headers, $attachments)
		{
			ob_start();
				include($template);
				$message = ob_get_contents();
			ob_end_clean();
			
			return wp_mail($to, $subject, $message, $headers, $attachments);
		}
		
		
		
		
		
		//*** comments
		public function get_comment($id) { return get_comment($id); } 
		public function get_comments($id, $status = "approve") 
		{
			$args = array('status' => $status, 'post_id' => $id);
			return get_comments($args);
		}
		public function get_lastcomment_modified() { return get_lastcommentmodified(); } 
		public function get_approved_comments($id) { return get_approved_comments($id); } 
		public function get_avatar($id_or_email) { return get_avatar($id_or_email); } 		
		
		public function new_comment($post, $author, $message, $email = '', $url = '', $ip = '', $agent = '', $user = '', $force_approvement = '0') 
		{
			$time = current_time('mysql', $gmt = 0);
			
			$args = array(
				'comment_post_ID' => $post,
				'comment_author' => $author,
				'comment_author_email' => $email,
				'comment_author_url' => $url,
				'comment_content' => $message,
				'comment_type' => '',
				'comment_parent' => 0,
				'user_ID' => $user,
				'comment_author_IP' => $ip,
				'comment_agent' => $agent,
				'comment_date' => $time,
				'comment_date_gmt' => $time,
				'comment_approved' => $force_approvement
			);
			
			return wp_new_comment($args);
		}
		
		public function get_comment_status($id) { return wp_get_comment_status($id); } 
		
		
		
		
		
		//*** misc
		public function generate_password($length) { return wp_generate_password($length, false); } 		
		public function notify_admin($id, $pass) { return wp_new_user_notification($id, $pass); } 

		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		//***** private functions with credentials
		//*** posts
		public function insert_post($title, $content, $user, $category = '', $published = true) 
		{
			$args = array(
			'post_title' => $title,
			'post_content' => $content,
			'post_author' => $user,
			'post_category' => $category,
			'post_status' => $published ?'publish' : 'draft', 
			'post_type' => 'post',
			'ping_status' => get_option('default_ping_status'), 
			'post_parent' => 0,
			'menu_order' => 0,
			'to_ping' =>  '',
			'pinged' => '',
			'post_password' => '',
			'guid' => '',
			'post_content_filtered' => '',
			'post_excerpt' => '',
			'import_id' => 0);

			return wp_insert_post($args);
		}
		public function publish_post($id) { return wp_publish_post($id); } 
		public function update_post($obj) { return wp_update_post($obj); } 	
		public function delete_post($id, $force = false) { return wp_delete_post($id, $force); } 
		public function add_post_meta($id, $key, $value, $unique) { return add_post_meta($id, $key, $value, $unique); } 
		public function update_post_meta($id, $key, $value) { return update_post_meta($id, $key, $value); } 
		public function delete_post_meta($id, $key) { return delete_post_meta($id, $key); } 
		
		
		
		
		
		//*** attachments
		public function update_attached_file($id, $path) { return update_attached_file($id, $path); } 
		public function delete_attachment($id) { return wp_delete_attachment($id); } 
		
		
		
		
		
		//*** categories
		public function create_category($name, $parent) { wp_create_category($cat_name, $parent); } 
		public function insert_category($id, $name, $desc = "", $nicename = "", $parent = "") 
		{
			$args = array('cat_ID' => $id, 'cat_name' => $name, 'category_description' => $desc, 'category_nicename' => $nicename, 'category_parent' => $parent);
			return wp_insert_category($args);
		}
		
		
		
		
		
		//*** users
		public function create_user($name, $password, $email) { return wp_create_user($name, $password, $email); } 
		public function get_currentuserinfo() { return get_currentuserinfo(); } 
		public function get_usermeta($id, $key) { return get_usermeta($id, $key); } 
		public function get_profile($field, $user) { return get_profile($field, $user); } 
		public function get_userdata($id) { return get_userdata($id); } 
		public function get_userdata_by_login($login) { return get_userdatabylogin($login); } 
		public function insert_user($id, $login, $password, $email, $nicename = "", $url = "", $nickname = "", $display_name = "", $first_name = "", $last_name = "", $description = "", $rich_editing = "", $user_registered = "", $role = "", $jabber = "", $aim = "", $yim = "") 
		{
			$args = array("ID" => $id, "user_login" => $login, "user_pass" => $password, "user_email" => $email, "user_nicename" => $nicename, "user_url" => $url, "nickname" => $nickname, "display_name" => $display_name, "first_name" => $first_name, "last_name" => $last_name, "description" => $description, "rich_editing" => $rich_editing, "user_registered" => $user_registered, "role" => $role, "jabber" => $jabber, "aim" => $aim, "yim" => $yim);
			return wp_insert_user($args);
		}
		public function update_usermeta($id, $key, $value) { return update_usermeta($id, $key, $value); } 
		public function delete_user($id, $reassign) { return wp_delete_user($id, $reassign); } 
		public function delete_usermeta($id, $key, $value) { return delete_usermeta($id, $key, $value); } 
		
		 
		
		
		
		//*** comments
		public function allow_comment($post, $message, $user = '', $author = '', $email = '', $url = '', $ip = '', $agent = '', $force_approvement = '0') 
		{
			$time = current_time('mysql', $gmt = 0);
			
			$args = array(
				'comment_post_ID' => $post,
				'comment_author' => $author,
				'comment_author_email' => $email,
				'comment_author_url' => $url,
				'comment_content' => $message,
				'comment_type' => '',
				'comment_parent' => 0,
				'user_ID' => $user,
				'comment_author_IP' => $ip,
				'comment_agent' => $agent,
				'comment_date' => $time,
				'comment_date_gmt' => $time,
				'comment_approved' => $force_approvement
			);
			
			return wp_allow_comment($args);
		}
		public function insert_comment($post, $message, $user = '', $author = '', $email = '', $url = '', $ip = '', $agent = '', $force_approvement = '0') 
		{
			$time = current_time('mysql', $gmt = 0);
			
			$args = array(
				'comment_post_ID' => $post,
				'comment_author' => $author,
				'comment_author_email' => $email,
				'comment_author_url' => $url,
				'comment_content' => $message,
				'comment_type' => '',
				'comment_parent' => 0,
				'user_ID' => $user,
				'comment_author_IP' => $ip,
				'comment_agent' => $agent,
				'comment_date' => $time,
				'comment_date_gmt' => $time,
				'comment_approved' => $force_approvement
			);
			
			return wp_insert_comment($args);
		}
		public function update_comment($post, $message, $user = '', $author = '', $email = '', $url = '', $ip = '', $agent = '', $force_approvement = '0') 
		{
			$time = current_time('mysql', $gmt = 0);
			
			$args = array(
				'comment_post_ID' => $post,
				'comment_author' => $author,
				'comment_author_email' => $email,
				'comment_author_url' => $url,
				'comment_content' => $message,
				'comment_type' => '',
				'comment_parent' => 0,
				'user_ID' => $user,
				'comment_author_IP' => $ip,
				'comment_agent' => $agent,
				'comment_date' => $time,
				'comment_date_gmt' => $time,
				'comment_approved' => $force_approvement
			);
			
			return update_comment($args);
		}
		public function delete_comment($id) { return wp_delete_comment($id); } 
		public function set_comment_status($id, $status) { return wp_set_comment_status($id, $status); } 
		public function filter_comment($post, $message, $user = '', $author = '', $email = '', $url = '', $ip = '', $agent = '', $force_approvement = '0') 
		{
			$time = current_time('mysql', $gmt = 0);
			
			$args = array(
				'comment_post_ID' => $post,
				'comment_author' => $author,
				'comment_author_email' => $email,
				'comment_author_url' => $url,
				'comment_content' => $message,
				'comment_type' => '',
				'comment_parent' => 0,
				'user_ID' => $user,
				'comment_author_IP' => $ip,
				'comment_agent' => $agent,
				'comment_date' => $time,
				'comment_date_gmt' => $time,
				'comment_approved' => $force_approvement
			);
			
			return wp_filter_comment($args);
		}
	}
	
?>