<?php 
$api_key = get_option('clickdesign_api');
$readonly = !empty($api_key) ? 'readonly' : '';

$url = 'https://api.wordpress.org/core/version-check/1.7/';
$response = wp_remote_get($url);

$json = $response['body'];
$obj = json_decode($json);

echo '<div class="cd-wrapper">
	<div class="cd-wrapper-inner">
		<div class="main-screen">
			<a href="javascript:;" class="cd-logo">
				<img src="'.CLICKDESIGNS_PLUGIN_URL.'/assets/images/logo.svg">
			</a>
			<h3>'.esc_html__('Welcome to ClickDesigns').'</h3>';
			if(empty($api_key)){
					echo '<div class="cd_btns">
						<a href="javascript:;" id="cd_api_true" class="cd-api-key-btn">'.esc_html__('Do you have an API key?', 'clickdesigns').'</a>
						<a href="'.esc_url('https://clickdesigns.com/wp/').'" target="_blank" id="cd_api_false" class="cd-api-key-btn">'.esc_html__('Don\'t have an API key, get a free trial.', 'clickdesigns').'</a>
					</div>';
			}      
		echo ' </div>';
		$show = !empty($api_key) ? 'block' : 'none';
		echo '<div class="cd_settings_div cd_settings_view" style="display: '.esc_attr($show).'">
		<h4 class="cd-title">'.esc_html__('ClickDesigns Settings', 'clickdesigns').'</h4>
			<div class="cd-main-div">
				<label>'.esc_html__('Enter the API key below to connect with your ClickDesigns account. After the API connection, you will see your created designs under Image Library while creating a post, or pages.', 'clickdesigns').'</label>   
				<div class="cd-api-div">         
					<input type="text" id="cd_api_key_id" value="'.esc_html($api_key).'" class="cd-api-key" '.esc_attr($readonly).'>';
					if(!empty($api_key)){
						echo '<a href="javascript:;" id="cd_api_key_remove" class="cd-api-key-btn">'.esc_html__('Reset', 'clickdesigns').'</a>';
					} else {
						echo '<a href="javascript:;" id="cd_api_key_add" class="cd-api-key-btn">'.esc_html__('Submit', 'clickdesigns').'</a>';
					}                
				echo '</div>
			</div>
		</div>';
		global $wp_version;
		$cversion = $obj->offers[0]->current;
		if($wp_version != $cversion){
			echo '<p class="cd_note"><b>'.esc_html__('Note : ', 'clickdesigns').'</b>'.esc_html__('Please update your WordPress version to '.esc_html($obj->offers[0]->current).'.', 'clickdesigns').'</p>';
		}
	echo '</div>
</div>';