<?php
/*
* Plugin Name: Doppler Form
* Plugin URI: http://www.fromdoppler.com
* Description: Integrate Doppler with Wordpress
* Version: 1.0
* License: GPL v2
* Author: Doppler
*/

require_once( plugin_dir_path(__FILE__ ).'lib/doppler-client.php');

/**
 * 
 * Add new textdomain (Required to l10n)
 * 
 **/
function 	dplr_load_textdomain() {
  $result = load_plugin_textdomain( 'doppler-form', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
 
}

add_action( 'plugins_loaded', 'dplr_load_textdomain' );

/**
 * Add scripts of plugin
 */
function dplr_add_js_admin(){

    wp_enqueue_script( 
    	'chsosen-plugin-jquery',
    	plugins_url( '/js/chosen.jquery.min.js', __FILE__ ), 
    	array('jquery')
    );

    wp_enqueue_script( 
    	'dplr_plugin_script', 
    	plugins_url( '/js/doppler-form.js', __FILE__ ), 
    	array('chsosen-plugin-jquery', 'jquery') 
    );
    
}

add_action('admin_init', 'dplr_add_js_admin');


function dplr_check_widget() {

   $settings = get_option('dplr_settings');
   $api_key = '';
   if($settings != null && array_key_exists('dplr_option_apikey', $settings))
   		$api_key = $settings['dplr_option_apikey'];

   	$args = array(
   		'url' => plugin_dir_url(__FILE__),
   		'api_key' => $api_key
   		);
   wp_enqueue_script( 'dplr_scripts_front', plugins_url( '/js/doppler-form-front.js', __FILE__ ), array('jquery'));
   wp_localize_script('dplr_scripts_front', 'dplr_plugin_settings', $args );
}

add_action( 'init', 'dplr_check_widget' );


function dplr_add_plugin_css(){
	wp_enqueue_style('chsosen-plugin-jquery-style', plugins_url( '/css/chosen.min.css', __FILE__ ));
	wp_enqueue_style('doppler-styles', plugins_url( '/css/styles.css', __FILE__ ));

}
add_action('admin_init', 'dplr_add_plugin_css');


/**
 * Generate the plugin menu
 */
function dplr_menu() {
	add_options_page( 'Doppler Form Plugin', __('Doppler Form', 'doppler-form') , 'manage_options', 'doppler_settings_page', 'dplr_landing' );
	add_action( 'admin_init', 'dplr_register_settings' );
}

add_action( 'admin_menu', 'dplr_menu' );

/**
 * 
 * Register Plugin
 * 
 **/
function dplr_register_settings(){
	register_setting('dplr_plugin_options', 'dplr_settings', 'dplr_settings_validate');
}





function dplr_settings_validate($args){
	$args['dplr_option_apikey'] = trim($args['dplr_option_apikey']);
	$action = $args['action'];
	switch ($action) {
		case 'disconnect':
			$args['dplr_option_apikey'] = "";
			$args['action']  = "";
			return $args;
			break;
	}

	if(empty($args['dplr_option_apikey'])){
		$message = __("Ouch! The field is empty.", "doppler-form");;
		$type = 'error';
	}else{
		$dplClient = new DopplerClient($args['dplr_option_apikey']);


		$result = $dplClient->user->testApiConnection();
		$testConnection = $result->{'Client.TestApiConnectionResult'};

		$message = "";
		$type = 'error';


		if($testConnection){
			 $message = 'Successfully updated';
			 $type = 'updated';
		} else{
			// unset($args['dplr_option_apikey']);
			$message = __("Ouch! Enter a valid API Key.", "doppler-form");
			$type = 'error';
		}
	}
	add_settings_error(
		'doppler_settings_page',
		'form_validation_message',
		$message,
		$type);

	return $args;
}
 
/**
 * This function will be called to output the content for the admin page
 */
 
function dplr_landing() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	} 

	$options = get_option( 'dplr_settings', array(
							'dplr_option_apikey' => '',
							'dplr_option_lists'  => array() 
							) );
	$connected = false;
	$errors = false;	
	if($options['dplr_option_apikey'] != ''){
		$connected = dplr_checkAPIConnection($options['dplr_option_apikey']);
	}

	$messages = get_settings_errors('doppler_settings_page');
	$errorMessage = '';
	if(isset($messages[0]) && $messages[0]['type'] == 'error') {
		$error = true;
		$errorMessage = $messages[0]['message'];
	}

	?>
	


	<section class="dplr_container">
		<div class="wrap">
			<a href="http://www.fromdoppler.com" target="_blank"><img id="dplr_logo" src="<?php echo plugins_url( '/images/doppler.png', __FILE__ ); ?>" alt="Doppler"></a>
			<h1><?php _e("Connect your Subscription Form with Doppler", "doppler-form" ); ?></h1>
			<p ><?php _e("Send emails directly from your Wordpress Form to your Doppler Lists! All you need to do is enter your API Key to link them up. Don’t know where to find it?","doppler-form") ;?>
				<a href='http://help.fromdoppler.com/en/api-interfaz-de-programacion-de-aplicaciones/?utm_source=wordpress&utm_medium=blog&utm_campaign=plugin' target='_blank'><?php _e("Read this post", "doppler-form"); ?>

			</a><?php _e("(Remember that this benefit is only available for paid accounts).", "doppler-form");?></p>
			<?php
			if($connected){ ?>
			<div class="disconnect_box">
				<form method="POST" action="options.php" >
					<?php settings_fields('dplr_plugin_options'); ?>
					<input type="hidden" name="dplr_settings[action]" value="disconnect" /> 
					<span><i><?php _e("Your account is connected", "doppler-form"); ?></i><button class="button button--small button--fourth"><?php _e("Disconnect", "doppler-form"); ?></button></span>
				</form>
			</div>
			<div class="updated_message">
				<img width="54" src="<?php  echo plugins_url( '/images/check.png', __FILE__ ); ?>" alt="">
				<p style="color:#65BF92; font-family: 'Proxima Nova', Arial; font-size: 16px; line-height:21px;"><i>
					<?php _e("The process was a success! Your API Key is officially connected.", "doppler-form"); ?>
				</i></p>
				<img width="860" src="<?php  echo plugins_url( '/images/bar.png', __FILE__ ); ?>" style="margin: 30px 0 5px 0;" alt="">
				<p style="color: #525843; font-size:16px; line-height: 21px; font-family: 'Proxima Nova', Arial; max-width:860px; width: 100%; display: inline-block;">
					<?php _e("You're almost there! Go to the tab", "doppler-form"); ?> <img width="18px" src="<?php  echo plugins_url( '/images/icon.png', __FILE__ ); ?>" alt=""><?php _e("<b>Appearance>Widgets>Doppler Form</b> and choose the Lists where your new Subscribers will be saved", "doppler-form"); ?>
				</p>
			</div>
			<?php } else {?>
			
			<div class="dplr_form_wrapper" >
				<form method="POST" action="options.php" id="dplr_apikey_options" class="<?php echo $error?'error':''; ?>">
					<?php settings_fields('dplr_plugin_options'); ?>
					<label class="lblform"><i><?php _e("Enter your API Key.", "doppler-form");?></i></label>
					<label for="apikey-input">
						<input  onfocus="this.placeholder = ''" onblur="this.placeholder = '<?php _e("Enter your API Key.", "doppler-form");?>'" type="text" placeholder="<?php _e("Enter your API Key.", "doppler-form");?>" name="dplr_settings[dplr_option_apikey];"  autocomplete="off" value="<?php echo $options['dplr_option_apikey'];?>" /> 
						<button class="dplr_submit" ><?php _e("CONNECT", "doppler-form"); ?></button>
					</label>
				</form>
				<div class="errorMessageBox <?php echo $error?'error':''; ?>"><?php echo $errorMessage; ?></div>
				<div class="loader">Loading...</div>
			</div>
			<?php } ?>
			<p class="copyright" >
				&copy; 2015 <?php _e('Doppler is a product by', 'doppler-form'); ?> <a href="http://www.makingsense.com" target="blank"><img id="ms_logo" src="<?php echo plugins_url( '/images/MS_logo.svg', __FILE__ ); ?>" alt="Making Sense"></a>
			</p>
		</div>
	</section>

	<?php
}

/**
 * Check the connection with the API
 * @var $APIKey String  
 * @return boolean 
 */
function dplr_checkAPIConnection($APIKey){
	if(empty($APIKey)) return;

	$dplClient = new DopplerClient($APIKey);
	$result = $dplClient->user->testApiConnection();
	return $result->{'Client.TestApiConnectionResult'};
}

/**
 * --------------------
 *       Widget
 * --------------------
 */
class Dplr_Subscription_Widget extends WP_Widget{


	var $dopplerClient;
	// constructor
	function Dplr_Subscription_Widget() {

		$widget_ops = array('classname' => 'dplr_subscription_widget', 'description' => __("Select the Lists you want to send your Subscribers to.", "doppler-form") );
		$this->WP_Widget('Dplr_Subscription_Widget', __('Doppler Form', "doppler-form"), $widget_ops);

		$settings = get_option('dplr_settings');
		if($settings != null && array_key_exists('dplr_option_apikey', $settings)){
			$this->dopplerClient = new DopplerClient($settings['dplr_option_apikey']);
			//wp_die(var_dump($this->dopplerClient->user->testApiConnection()));
		}

	}
	 
	// widget output
	function widget( $args, $instance ) {
		extract($args);
		
		$title = apply_filters('widget_title', $instance['title']);

		

		

		$selectedLists = isset($instance['selected_lists']) && $instance['selected_lists'] != ''? implode(',' ,$instance['selected_lists']) : false;
		if($selectedLists){
			echo $before_widget;
			if(!empty($title)){
				echo $before_title . $title . $after_title;
			}
		echo '<div class="thanksMessage" style="display:none">'.__("Thanks for Subscribing", 'doppler-form').'</div>'
			.'<form class="dplr_wdg_form">'
			.'	<div>'
			.'		<input name="email" required="true" type="email" placeholder="'.__('Email …', 'doppler-form').'"  />'
			.'		<input name="lists" type="hidden" value="'.$selectedLists.'" /><br>'
			.'	</div>'
			.'	<div style="padding-top: 0.6em;">'
			.'		<input type="submit" value="'.__("Send", 'doppler-form').'" />'
			.'	</div>'
			.'</form>';

		echo $after_widget;
}



	}

	// save options from widget administration screen
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['selected_lists'] = esc_sql($new_instance['selected_lists']);

		return $instance;

	}
	 
	// display form fields on widget administration screen
	function form( $instance ) {
		
		$instance['title'] = array_key_exists('title', $instance) ?$instance['title']: '';
		$instance['selected_lists'] = array_key_exists('selected_lists', $instance) &&  empty($instance['selected_lists']) == false? $instance['selected_lists']:array();


		$subscriptionLists = get_object_vars($this->dopplerClient->user->getSubscribersLists())['Client.GetSubscribersListsResult']->subscriberslist;
		$subscriptionLists = count($subscriptionLists) >1 ? $subscriptionLists : array($subscriptionLists);
		 ?>		
		<!-- Widget title -->
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', "doppler-form");?>:</label>
			<input type="text"  name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id( 'title' );?>" class="widefat" value="<?php echo $instance['title'];?>" placeholder="<?php _e('Enter a descriptive title for your Form.','doppler-form'); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('selected_lists'); ?>"><?php _e('Subscription Lists', 'doppler-form');?>:</label>

		<!-- Widget form -->
		<select  style="width: 100% !important;" name="<?php echo $this->get_field_name( 'selected_lists' ) ?>[]" id="<?php echo $this->get_field_id('selected_lists'); ?>" multiple class="multiple-selec widefat" data-placeholder="<?php _e('Select the List that you want to import your Subscribers into.', 'doppler-form'); ?>">
            <option value=""></option>
            <?php foreach($subscriptionLists as $list ) {?>
            <option <?php echo in_array($list->SubscribersListID,$instance['selected_lists'])?'selected="selected"':'';  ?>  value="<?php echo $list->SubscribersListID; ?>"><?php echo $list->Name; ?></option>
            <?php }?>
          </select>
		</p>
		
		 <?php
	}

}

/**
 * -----------------------------------------------------------
 * 
 * The widget will be included only if the API Key is correct
 * 
 * -----------------------------------------------------------
 */
function dplr_profile_widget_init() {

	$options = get_option( 'dplr_settings', array(
							'dplr_option_apikey' => '',
							'dplr_option_lists'  => array() 
							) );
	if(isset($options['dplr_option_apikey']) && dplr_checkAPIConnection($options['dplr_option_apikey'])){
		register_widget('Dplr_Subscription_Widget');
	}
	
}
 

add_action('widgets_init', 'dplr_profile_widget_init');

/**
 * -----------------------------------------------------------
 * 
 * 						Uninstall plugin
 * 
 * -----------------------------------------------------------
 */

register_uninstall_hook( __FILE__, 'dplr_delete_option' );

function dplr_delete_option(){
	delete_option( 'dplr_settings' );
}

?>