<?php
/*
Plugin Name: Simple Alert Plugin
Plugin URI: 
Description: Simple alert on frontend selected pages or post 
Version: 1.0
Author: Tushar Patel
License: 
Text Domain: 
*/

$simple_alert = new SimpleAlertPlugin();
class SimpleAlertPlugin {
 
    public function __construct() {
 
        add_action( 'admin_menu', array( $this,'simple_alert_plugin_create_menu' ) );
		add_action( 'admin_init', array( $this,'register_simple_alert_plugin_settings' ) );		
		add_action( 'wp_footer', array( $this, 'simple_alert_code_footer' ) );			
        add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts_styles' ) );
		//add_action( 'admin_enqueue_scripts', array( $this, 'load_custom_wp_admin_style' )); 
 
    }
	//Create menu under settings menu
	public function simple_alert_plugin_create_menu() {	
		
		add_options_page(__('Simple Alert', 'simple-alert'), __('Simple Alert', 'simple-alert'), 'manage_options', 'simple-alert', array( $this,'simple_alert_plugin_settings_page' ) );
			
	}
	//Register settings
	public function register_simple_alert_plugin_settings() {
		
		register_setting( 'simple-alert-plugin-settings-group', 'alert_text' );		
		register_setting( 'simple-alert-plugin-settings-group', 'selected_post_type');
		register_setting( 'simple-alert-plugin-settings-group', 'list_of_posts');
	}
	
	
	// Add Code to wp_footer()
	public function simple_alert_code_footer() { 

		$post_id = get_the_ID();
		$post_type = get_post_type();
		
		$selected_post = get_option('selected_post_type');
		$list_of_posts = get_option('list_of_posts');
						
		if (!is_admin() && in_array($post_type,$selected_post) && in_array($post_id,$list_of_posts[$post_type] ) && !is_home()){ ?>	
		
		<div id="hidden" style="display:none;">
			<?php echo get_option('alert_text');	?>
		</div>
			
		<?php }
	
	}
	public function register_plugin_scripts_styles() 
	{
		$post_id = get_the_ID();
		$post_type = get_post_type();
		
		$selected_post = get_option('selected_post_type');
		$list_of_posts = get_option('list_of_posts');
			
		if (!is_admin() && in_array($post_type,$selected_post) && in_array($post_id,$list_of_posts[$post_type] ) && !is_home())
		{
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-my', get_option('siteurl').'/wp-content/plugins/simple-alert-plugin/js/jquery.js');
			wp_enqueue_style( 'jquery.fancybox', get_option('siteurl').'/wp-content/plugins/simple-alert-plugin/css/jquery.fancybox.css');
			wp_enqueue_script('jquery.fancybox', get_option('siteurl').'/wp-content/plugins/simple-alert-plugin/js/jquery.fancybox.js');
			
		}
	}
	//Settings page
	public function simple_alert_plugin_settings_page() { ?>
        
        <div class="wrap">
        <h1>Your Plugin Name</h1>
        <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('.select_posts').click(function(){
                
                var inputValue = jQuery(this).attr("value");
                jQuery("." + inputValue).toggle();
            });
        });
        </script>
        <style>
        .select_posts_h{display:none;}
        .select_posts_d{display:block;}
        </style>
        <form method="post" action="options.php">
            <?php settings_fields( 'simple-alert-plugin-settings-group' ); ?>
            <?php do_settings_sections( 'simple-alert-plugin-settings-group' ); ?>
            <table class="form-table">
                <tr valign="top">
                	<th scope="row">Alert Text</th>
                	<td><input type="text" name="alert_text" class="regular-text" value="<?php echo esc_attr( get_option('alert_text') ); ?>" /></td>
                </tr>                 
                <tr valign="top">
                    <th scope="row">Select Posts/Pages </th>
                    <td>
                        <?php 
                        foreach ( get_post_types( array('public' => true)) as $post_name ) {
                            if($post_name == 'attachment') continue;
                            
                            $selected_post = get_option('selected_post_type');
                            $list_of_posts = get_option('list_of_posts');
                            
                            $chk =  !empty($selected_post) && in_array($post_name,$selected_post) ? 'checked' : '';
                             
                            echo '<fieldset><label for="'.$post_type.'_public"><input class="select_posts" name="selected_post_type[]" type="checkbox"  '.$chk.' id="selected_'.$post_name.'" value="'.$post_name.'">'.ucwords($post_name).'</label></fieldset>';
                            
                            $cl =  !empty($selected_post) && in_array($post_name,$selected_post) ? ' select_posts_d ' : ' select_posts_h ';
                            
                            echo '<select name="list_of_posts['.$post_name.'][]" id="list_of_'.$post_name.'" multiple="multiple" class="regular-text '.$post_name.$cl.'">
                                    <option value="0">— Select '. $post_name.' —</option>';
                                    $args = array( 'posts_per_page' => -1, 'order'=> 'ASC', 'orderby' => 'title','post_status' => 'publish','post_type' => $post_name );
                                    $postslist = get_posts( $args );
                                    foreach ( $postslist as $post ) :                                    
                                        $sel =  !empty($list_of_posts[$post_name]) &&  in_array($post->ID,$list_of_posts[$post_name] ) ? 'selected ' : '';				  
                                        echo '<option value="'.$post->ID.'" '.$sel.'>'.$post->post_title.'</option>';
                                    endforeach;                         
                            echo '</select><hr />';
                            
                        } ?>
                    </td>
                </tr>                
             </table>            
            <?php submit_button(); ?>        
        </form>
        </div>
    <?php } 
}