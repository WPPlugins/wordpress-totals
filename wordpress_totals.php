<?php
/*
Plugin Name: Wordpress Totals
Plugin URI: http://vasthtml.com
Description: Totals block:  how many users, posts, and comments your site has.
Version: 1.2
Author: VastHTML
Author URI: http://lucidcrew.com/
*/

function vasthtml_total_users() {
    global $wpdb;
	echo $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->users;");
}

function vasthtml_total_posts() {
    global $wpdb;
	echo $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post';");
}

function vasthtml_total_comments($show) {
    global $wpdb;
	echo $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1';");
}

function widget_vasthtml_totals_register() {
	function widget_vasthtml_totals($args) {
		global $wpdb;
		extract($args);
		$the_title = get_option('vasthtml_totals_title');
		$widget_title = empty($the_title) ? __('Wordpress Totals','calendar') : $the_title;

		$the_members = get_option('vasthtml_totals_totalusers');
		$users_var = empty($the_members) ? "We have %s members." : $the_members; $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->users;");
		$total_users = str_replace("%s", $count, $users_var);

		$the_posts = get_option('vasthtml_totals_totalposts');
		$posts_var = empty($the_posts) ? "We have %s posts." : $the_posts; $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post';");
		$total_posts = str_replace("%s", $count, $posts_var);

		$the_comments = get_option('vasthtml_totals_totalcomments');
		$comments_var = empty($the_comments) ? "We have %s comments." : $the_comments; $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1';");
		$total_comments = str_replace("%s", $count, $comments_var);

		echo $before_widget;
		echo $before_title . $widget_title . $after_title;
		echo "<ul>";
		if (get_option('vasthtml_totals_totalusers')) { echo "<li>" . $total_users . "</li>"; }
		if (get_option('vasthtml_totals_totalposts')) { echo "<li>" . $total_posts . "</li>"; }
		if (get_option('vasthtml_totals_totalcomments')) { echo "<li>" . $total_comments . "</li>"; }
		echo "</ul>";
		echo $after_widget;
	}

	function widget_vasthtml_totals_control() {
		$widget_title = get_option('vasthtml_totals_title');
		$total_users = get_option('vasthtml_totals_totalusers');
		$users_title = is_null($total_users) ? "We have %s members." : $total_users;
		$total_posts = get_option('vasthtml_totals_totalposts');
		$posts_title = is_null($total_posts) ? "We have %s posts." : $total_posts;
		$total_comments = get_option('vasthtml_totals_totalcomments');
		$comments_title = is_null($total_comments) ? "We have %s comments." : $total_comments;
		if (isset($_POST['vasthtml_totals_title'])) {
			update_option('vasthtml_totals_title',strip_tags($_POST['vasthtml_totals_title']));
		}
		if (isset($_POST['vasthtml_totals_totalusers'])) {
			update_option('vasthtml_totals_totalusers',strip_tags($_POST['vasthtml_totals_totalusers']));
		}
		if (isset($_POST['vasthtml_totals_totalposts'])) {
			update_option('vasthtml_totals_totalposts',strip_tags($_POST['vasthtml_totals_totalposts']));
		}
		if (isset($_POST['vasthtml_totals_totalcomments'])) {
			update_option('vasthtml_totals_totalcomments',strip_tags($_POST['vasthtml_totals_totalcomments']));
		}
?>
    <p>
       <label for="vasthtml_totals_title"><?php _e('Title','wordpress_totals'); ?>:<br />
       <input class="widefat" type="text" id="vasthtml_totals_title" name="vasthtml_totals_title" value="<?php echo $widget_title; ?>"/></label>
    </p>
    <p>
       <label for="vasthtml_totals_totalusers"><?php _e('Text for Total Members','wordpress_totals'); ?>:<br />
       <input class="widefat" type="text" id="vasthtml_totals_totalusers" name="vasthtml_totals_totalusers" value="<?php echo $users_title; ?>"/></label>
	   <i>Example:</i> We have %s members.
    </p>
    <p>
       <label for="vasthtml_totals_totalposts"><?php _e('Text for Total Posts','wordpress_totals'); ?>:<br />
       <input class="widefat" type="text" id="vasthtml_totals_totalposts" name="vasthtml_totals_totalposts" value="<?php echo $posts_title; ?>"/></label>
	   <i>Example:</i> We have %s posts.
    </p>
    <p>
       <label for="vasthtml_totals_totalcomments"><?php _e('Text for Total Comments','wordpress_totals'); ?>:<br />
       <input class="widefat" type="text" id="vasthtml_totals_totalcomments" name="vasthtml_totals_totalcomments" value="<?php echo $comments_title; ?>"/></label>
	   <i>Example:</i> We have %s comments.
    </p>
	<p>
	   You can use %s are the variable for the counter. Make blank if you do not wish to show that particular counter at all.
	</p>
    <?php
	}
	wp_register_sidebar_widget( 'Wordpress_Totals', __('Wordpress Totals','wordpress_totals'), 'widget_vasthtml_totals', array('description' => __('Displays Your Sites Totals','wordpress_totals')) );
	wp_register_widget_control( 'Wordpress_Totals', __('Wordpress Totals','wordpress_totals'), 'widget_vasthtml_totals_control');
}
add_action('init', widget_vasthtml_totals_register);

function get_version_total_users(){
	$plugin_data = implode('', file(ABSPATH."wp-content/plugins/wordpress-totals/wordpress_totals.php"));
	if (preg_match("|Version:(.*)|i", $plugin_data, $version)) {
		$version = $version[1];
	}
	return $version;
}
add_action('admin_menu', 'total_users_info_page');

function wordpress_totals_dashboard_function() {
	  echo '<table class="widefat">
<tr class="alternate">
<td><strong>Total Users:</strong></td>
<td>
'; ?>
		<?php vasthtml_total_users(); ?>
        <?php echo '
</td>
</tr>
<tr class="alternate">
<td><strong>Total Posts:</strong></td>
<td>'; ?>
		<?php vasthtml_total_posts(); ?>
        <?php echo '</td>
</tr>
<tr class="alternate">
<td><strong>Total Comments:</strong></td>
<td>'; ?>
		<?php vasthtml_total_comments($show); ?>
        <?php echo '</td>
</tr>
<tr class="alternate">
			<td colspan="2">
			<span class="button" style="float:left"><a href="http://vasthtml.com" target="_blank">Vast HTML</a></span>
			<span class="button" style="float:right"><a href="http://erichamby.com" target="_blank">Eric Hamby</a></span>
			</td>
		</tr>

</table>';
} 

function wordpress_totals_widgets() {
	wp_add_dashboard_widget('wordpress_totals', 'Wordpress Totals', 'wordpress_totals_dashboard_function');	
} 

add_action('wp_dashboard_setup', 'wordpress_totals_widgets' );


function total_users_info_page(){
  add_menu_page('Wordpress Totals', 'WP Totals', 8, 'total_users_info', 'total_users_info', get_bloginfo('url').'/wp-content/plugins/wordpress-totals/images/chart.png');
}
function total_users_info() {
  echo '<div class="wrap">';
  echo '<div id="icon-index" class="icon32"><br /></div><h2>Donation Information</h2>';
  echo '<p>';
  echo '<table class="widefat">
    <thead>
      <tr>
        <th>Donations</th>
		<th><span style="float:right"><small>'.get_version_total_users().'</small></span></th>
      </tr>
    </thead>
		<tr class="alternate">
<td>If you like this plugin please help us and our efforts by giving a donation of any amount. <a href="http://vasthtml.com/donations/" target="_blank">Donations Page</a></td>
<td></td>
</tr>						
		</table>';
		echo '</p>';
  
  
echo '<div id="icon-themes" class="icon32"><br /></div><h2>'; ?>
		<?php bloginfo('name'); ?>
        <?php echo ' Stats</h2>';
		echo '<p>';
  echo '<table class="widefat">
    <thead>
      <tr>
        <th>'; ?>
		<?php bloginfo('description'); ?>
        <?php echo '
		</th>
		<th></th>
      </tr>
    </thead>
<tr class="alternate">
<td>Total Users:</td>
<td>
'; ?>
		<?php vasthtml_total_users(); ?>
        <?php echo '
</td>
</tr>
<tr class="alternate">
<td>Total Posts:</td>
<td>'; ?>
		<?php vasthtml_total_posts(); ?>
        <?php echo '</td>
</tr>
<tr class="alternate">
<td>Total Comments:</td>
<td>'; ?>
		<?php vasthtml_total_comments($show); ?>
        <?php echo "</td>
</tr>
<tr class='alternate'>
<td><div id='GraphTwo'></div></td>
</tr>
</table>";
echo '</p>';  
 
echo '<div id="icon-themes" class="icon32"><br /></div><h2>Wordpress Totals Information</h2>';
		echo '<p>';
  echo '<table class="widefat">
    <thead>
      <tr>
        <th>Vast HTML Plugins</th>
		<th><span style="float:right"><small>'.get_version_total_users().'</small></span></th>
      </tr>
    </thead>
		<tr class="alternate">
<td>Plugin Name:</td>
<td>Wordpress Totals</td>
</tr>

<tr class="alternate">
<td>Plugin Version:</td>
<td>'.get_version_total_users().'</td>
</tr>

<tr class="alternate">
<td>Build:</td>
<td>1000</td>
</tr>


<tr class="alternate">
<td>Author:</td>
<td><a href="http://erichamby.com" target="_blank">Eric Hamby</a></td>
</tr>

<tr class="alternate">
<td>Co Author:</td>
<td><a href="http://ben.cybergoth.nl" target="_blank">Ben</a></td>
</tr>

<tr class="alternate">
<td>Release Date:</td>
<td>9/14/2009</td>
</tr>

<tr class="alternate">
<td>FAQ:</td>
<td><a href="http://vasthtml.com/faq/" target="_blank">FAQ Page</a></td>
</tr>

<tr class="alternate">
<td>Donations:</td>
<td><a href="http://vasthtml.com/donations/" target="_blank">Donations Page</a></td>
</tr>

<tr class="alternate">
<td>Support Forums:</td>
<td><a href="http://vasthtml.com/support/" target="_blank">Vast HTML Support</a></td>
</tr>


	<tr class="alternate">
			<td colspan="2">
			<span class="button" style="float:left"><a href="http://vasthtml.com" target="_blank">Vast HTML</a></span>
			<span class="button" style="float:right"><a href="http://erichamby.com" target="_blank">Eric Hamby</a></span>
			</td>
		</tr>

							
		</table>';
		echo '</p>';
		
		
		
		
  echo '</div>';
}
?>
