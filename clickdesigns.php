<?php

/**
* The plugin bootstrap file
*
* This file is read by WordPress to generate the plugin information in the plugin
* admin area. This file also includes all of the dependencies used by the plugin,
* registers the activation and deactivation functions, and defines a function
* that starts the plugin.
*
* @link              #
* @since             1.8.0
* @package           ClickDesigns
*
* @wordpress-plugin
* Plugin Name:       ClickDesigns
* Plugin URI:        https://clickdesigns.com/wp/
* Description:       Get Stunning Logos, Boxshots, Covers, Reports, Digital Mockups, Product Bundles, Graphics & Illustrations For Your Webpages, Blogs & Sales Funnels Without a Designer.
* Version:           1.8.0
* Author:            ClickDesigns
* Author URI:        https://clickdesigns.com/
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:       clickdesigns
* Domain Path:       /languages
*/

/** 
* If this file is called directly, abort.
*/
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
* Currently plugin version.
*/
define( 'CLICKDESIGNS_VERSION', '1.8.0' );

define('CLICKDESIGNS_PLUGIN_PATH', dirname(__FILE__));
define('CLICKDESIGNS_PLUGIN_URL', plugins_url('', __FILE__));

/**
* The code that runs during plugin activation. * This action is documented in includes/class-test-activator.php
*/
function ClickDesigns_Activator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/clickdesigns-class-activator.php';
	ClickDesigns_Activator::activate();
}

// add the tab
add_filter('media_upload_tabs', 'ClickDesigns_upload_tab');
function ClickDesigns_upload_tab($tabs) {
    $tabs['Click_designs'] = esc_html__("ClickDesigns",'clickdesigns');
    return $tabs;
}

// call the new tab with wp_iframe
add_action('media_upload_Click_designs', 'ClickDesigns_new_iframe_form');
function ClickDesigns_new_iframe_form() {
	wp_iframe('ClickDesigns_load_data_form');
}
 
// the tab content
function ClickDesigns_load_data_form() {
    //echo media_upload_header(); // This function is used for print media uploader headers etc.
	$key = get_option('clickdesign_api');
        $args = array(
			'headers' => array(
			  'Apikey' => $key
			),
			'body' => array(
				'page' => '1',
				'show' => '20',
				'keyword' => ''
			),
		);
		$keep_going = true;
		$response = wp_remote_post('https://api.clickdesigns.com/v1/getMyImage', $args);

		echo '<div class="cd_media_wrapper">
		      <div class="cd-media-section">
			   <div class="cd-top-div">
				<div class="cd-media-tab">
					<a href="javascript:;" class="cd-btn cd-api-buttons active" id="cd_get_designs" data-types="'.esc_attr__('Designs', 'clickdesigns').'" data-keys="">'.esc_html__('My Designs', 'clickdesigns').'</a>
					<a href="javascript:;" class="cd-btn cd-api-buttons" id="cd_get_bundles" data-types="'.esc_attr__('Bundles', 'clickdesigns').'" data-keys="">'.esc_html__('Bundles', 'clickdesigns').'</a>
					<a href="javascript:;" class="cd-btn cd-api-buttons" id="cd_get_package" data-types="'.esc_attr__('Package', 'clickdesigns').'" data-keys="">'.esc_html__('Custom', 'clickdesigns').'</a>
				</div>
				<div class="cd-data-filter">
					<a href="javascript:void(0);" class="cds-btn cds_btn_refresh"><img src="'.CLICKDESIGNS_PLUGIN_URL.'/assets/images/refresh.svg"> Refresh</a>
					<div class="cd_image_type">
						<select class="cd_user_list">
							<option value="'.esc_html__('All', 'clickdesigns').'">'.esc_html__('Filter By Team (All)', 'clickdesigns').'</option>';
							$userargs = array(
								'headers' => array(
								'Apikey' => $key
								),
							);
							$return = true;
							$user_response = wp_remote_post('https://api.clickdesigns.com/v1/getAgencyUser', $userargs);
							if ( is_wp_error($user_response) ) {
								// Error out.
								$return = false;
							} else {
								$user_data = wp_remote_retrieve_body($user_response);
								$user_list = json_decode($user_data);
								if($user_list->status == 200){
									foreach($user_list->agency_users as $users){
										echo '<option value="'.esc_attr($users->id).'" data-id="">'.esc_html($users->name).'</option>';
									}									
								}
							}             
						echo '</select>
						<select class="cd_images">
							<option value="png">'.esc_html__('PNG', 'clickdesigns').'</option>
							<option value="jpg">'.esc_html__('JPG', 'clickdesigns').'</option>
							<option value="webp">'.esc_html__('WEBP', 'clickdesigns').'</option>                        
						</select>
					</div>
					<div class="cd-media-tab-search">
						<form>
							<input type="text" placeholder="'.esc_html__(' Enter keyword(s) and click search', 'clickdesigns').'" id="cd_search_field" class="cd-search">
							<a href="javascript:;" id="cd_search_submit" class="cd-search-btn">'.esc_html__('Search', 'clickdesigns').'</a>
						</form>
					</div>
				</div>
			</div>
			<div class="cd-bottom-div">
				<div class="cd-media-images">
					<div class="cd-media-images-ajax row">';
						if ( is_wp_error( $response ) ) {
							// Error out.
							$keep_going = false;
						} else {
							$data = wp_remote_retrieve_body($response);
							$images = json_decode($data);
							$count = count($images->images);
							if($images->status == 200){
								if(!empty($images)){
									foreach($images->images as $image){
										if(!empty($image->thumb) || !empty($image->preview)){
											echo '<div class="cd-gallery-grid">
												<div class="cd-upload-image">
													<img src="'.esc_url($image->thumb).'" class="img-fluid" />
													<div class="cd-action">
														<ul>
															<li>
																<a href="'.esc_url($image->edit_link).'" class="cd-btn" target="_blank">Edit</a>
															</li>
															<li>
																<a href="javascript:void(0);" class="cd-btn cd-use-thumb" data-url="'.esc_url($image->preview).'" data-id="'.esc_attr($image->title).'" >Use</a>
															</li>
														</ul>
													</div>
												</div>
											</div>';
										}
									}
								}
							} else {
								$html ='Images Not Found !';
								$data = array('status' => 'false', 'page' => 'false', 'html'=> $html);
							}
						}
					echo '</div>';
					if($count <= 20){
						echo '<div class="load_btn">
							<a href="javascript:;" data-page=1 data-count='.$count.' id="cd_load_more">'.esc_html__('Load More', 'clickdesigns').'</a>
						</div>';
					}
				echo '</div>
			</div>
		</div><div class="cd-loader" style="display: none;"><img src="https://cdn1.clickdesigns.com/images/loader.gif"></div></div>';
		
} 
/**
* The code that runs during plugin deactivation.
* This action is documented in includes/class-test-deactivator.php
*/
function ClickDesigns_Deactivator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/clickdesigns-class-deactivator.php';
	ClickDesigns_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'ClickDesigns_Activator' );
register_deactivation_hook( __FILE__, 'ClickDesigns_Deactivator' );

require plugin_dir_path( __FILE__ ) . 'includes/clickdesigns-ajax.php';
require plugin_dir_path( __FILE__ ) . 'includes/clickdesigns-function.php';