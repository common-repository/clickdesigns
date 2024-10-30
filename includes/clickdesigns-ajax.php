<?php
namespace ClassClickDesigns;
/**
* If this file is called directly, abort.
*/
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
* ClickDesigns ajax class
*/
class ClickDesignsAjax{
	
	//ClickDesigns data construct
    public function __construct() {
		// Add API ajax function
		add_action( 'wp_ajax_nopriv_clickdesigns_add_api', array($this, 'clickdesigns_add_api'));
		add_action( 'wp_ajax_clickdesigns_add_api', array($this, 'clickdesigns_add_api'));		
		
		// Remove API ajax function
		add_action( 'wp_ajax_nopriv_clickdesigns_remove_api', array($this, 'clickdesigns_remove_api'));
		add_action( 'wp_ajax_clickdesigns_remove_api', array($this, 'clickdesigns_remove_api'));
		
		// Media tab ajax function
		add_action( 'wp_ajax_nopriv_clickdesigns_tab_media', array($this, 'clickdesigns_tab_media'));
		add_action( 'wp_ajax_clickdesigns_tab_media', array($this, 'clickdesigns_tab_media'));
		
		// Media tab images 
		add_action( 'wp_ajax_nopriv_clickdesigns_api_images', array($this, 'clickdesigns_api_images'));
		add_action( 'wp_ajax_clickdesigns_api_images', array($this, 'clickdesigns_api_images'));
		
		// Media tab designs images 
		add_action( 'wp_ajax_nopriv_clickdesigns_get_designs_images_tab_one', 'clickdesigns_get_designs_images_tab_one');
		add_action( 'wp_ajax_clickdesigns_get_designs_images_tab_one', array($this, 'clickdesigns_get_designs_images_tab_one'));
		
		// Media tab bundles images 
		add_action( 'wp_ajax_nopriv_clickdesigns_get_bundles_images_tab_two', array($this, 'clickdesigns_get_bundles_images_tab_two'));
		add_action( 'wp_ajax_clickdesigns_get_bundles_images_tab_two', array($this,'clickdesigns_get_bundles_images_tab_two'));
		
		// Media tab package images 
		add_action( 'wp_ajax_nopriv_clickdesigns_get_package_images_tab_three', array($this,'clickdesigns_get_package_images_tab_three'));
		add_action( 'wp_ajax_clickdesigns_get_package_images_tab_three', array($this,'clickdesigns_get_package_images_tab_three'));
		
		// Search form ajax
		add_action( 'wp_ajax_nopriv_clickdesigns_searchform', array($this,'clickdesigns_searchform'));
		add_action( 'wp_ajax_clickdesigns_searchform', array($this,'clickdesigns_searchform'));
		
		// Load more
		add_action( 'wp_ajax_nopriv_clickdesigns_loadmore', array($this,'clickdesigns_loadmore'));
		add_action( 'wp_ajax_clickdesigns_loadmore', array($this,'clickdesigns_loadmore'));
		
		// images upload media
		add_action( 'wp_ajax_nopriv_clickdesigns_upload_media', array($this,'clickdesigns_upload_media'));
		add_action( 'wp_ajax_clickdesigns_upload_media', array($this,'clickdesigns_upload_media'));
		
		// User image
		add_action( 'wp_ajax_nopriv_clickdesigns_user_media', array($this,'clickdesigns_user_media'));
		add_action( 'wp_ajax_clickdesigns_user_media', array($this,'clickdesigns_user_media'));
	}
	
	/* 
	* Add API ajax function
	*/
	public function clickdesigns_add_api(){
		$key = sanitize_text_field($_POST['key']);
		if(!empty($key)){
			$license_up = update_option("clickdesign_api", $key);	
			$data = array('status' => 'true', 'msg'=> 'Api Key Added Successfully');
		} else {
			$data = array('status' => 'false', 'msg'=> 'Invaild Api Key');
		}
		echo json_encode($data);
		wp_die();
	}

	/* 
	* Remove API ajax function
	*/
	public function clickdesigns_remove_api(){
		$key = update_option("clickdesign_api", '');
		if(empty($key) || $key == 'null'){
			$data = array('status' => 'true', 'msg'=> 'Api Key Removed Successfully');
		} else {
			$data = array('status' => 'false', 'msg'=> 'Something Is Wrong');
		}
		echo json_encode($data);
		wp_die();
	}
	
	/* 
	* Media tab ajax function
	*/
	public function clickdesigns_tab_media(){
		$key = get_option('clickdesign_api');
		if(!empty($key)){
			$data = array('status' => 'true');
		} else {
			$data = array('status' => 'false');
		}
		echo json_encode($data);
		wp_die();
	}
	
	/* 
	* Media tab images 
	*/
	public function clickdesigns_api_images(){

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
		echo '<div class="cd-media-section">
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
		</div>';
		wp_die();
	}
	
	/* 
	* Media tab designs images 
	*/
	public function clickdesigns_get_designs_images_tab_one(){
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
		if ( is_wp_error( $response ) ) {
			// Error out.
			$keep_going = false;
		} else {
			$data = wp_remote_retrieve_body($response);
			$images = json_decode($data);
			$page = count($images->images);
			$load_btn = '';
			if($page == 20){
				$load_btn = 'true';
			}
			if($images->status == 200){
				if(!empty($images)){
					$html = '';
					foreach($images->images as $image){
						if(!empty($image->thumb) || !empty($image->preview)){
							$html .= '<div class="cd-gallery-grid">
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
					$data = array('status' => 'true', 'page' => $load_btn, 'html'=> $html);
				}
			} else {
				$html ='Images Not Found !';
				$data = array('status' => 'false', 'page' => 'false', 'html'=> $html);
			}
		}
		echo json_encode($data);
		wp_die();
	}
	
	/* 
	* Media tab bundles images
	*/
	public function clickdesigns_get_bundles_images_tab_two(){
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
		$response = wp_remote_post('https://api.clickdesigns.com/v1/getMyBundles', $args);
		if ( is_wp_error( $response ) ) {
			// Error out.
			$keep_going = false;
		} else {
			$data = wp_remote_retrieve_body($response);
			$images = json_decode($data);
			$page = count($images->bundles);
			$load_btn = '';
			if($page == 20){
				$load_btn = 'true';
			}
			if($images->status == 200){
				if(!empty($images)){
					$html = '';
					foreach($images->bundles as $image){
						if(!empty($image->thumb) || !empty($image->preview)){
							$html .= '<div class="cd-gallery-grid">
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
					$data = array('status' => 'true', 'page' => $load_btn, 'html'=> $html);
				}
			} else {
				$html ='Images Not Found !';
				$data = array('status' => 'false', 'page' => 'false', 'html'=> $html);
			}
		}
		echo json_encode($data);
		wp_die();
	}
	
	/* 
	* Media tab package images
	*/
	public function clickdesigns_get_package_images_tab_three(){
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
		$response = wp_remote_post('https://api.clickdesigns.com/v1/getMyPackage', $args);
		if ( is_wp_error( $response ) ) {
			// Error out.
			$keep_going = false;
		} else {
			$data = wp_remote_retrieve_body($response);
			$images = json_decode($data);
			$page = count($images->packages);
			$load_btn = '';
			if($page == 20){
				$load_btn = 'true';
			}
			if($images->status == 200){
				if(!empty($images)){
					$html ='';
					foreach($images->packages as $image){
						if(!empty($image->thumb) || !empty($image->preview)){
							$html .= '<div class="cd-gallery-grid">
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
					$data = array('status' => 'true', 'page' => $load_btn, 'html'=> $html);
				}
			} else {
				$html ='Images Not Found !';
				$data = array('status' => 'false', 'page' => 'false', 'html'=> $html);
			}
		}
		echo json_encode($data);
		wp_die();
	}	
	
	/* 
	* Search form ajax
	*/
	public function clickdesigns_searchform(){
		$key = get_option('clickdesign_api');
		$tab = sanitize_text_field($_POST['tab']);
		$keyword = sanitize_text_field($_POST['keyword']);
		if(!empty($tab && !empty($key))){
			$api = $tab == 'Designs' ? 'getMyImage': ($tab == 'Bundles'? 'getMyBundles' : ($tab == 'Package'? 'getMyPackage' : '') );
			$args = array(
				'headers' => array(
				  'Apikey' => $key
				),
				'body' => array(
					'page' => '1',
					'show' => '20',
					'keyword' => $keyword
				),
			);
			$keep_going = true;
			$url =  'https://api.clickdesigns.com/v1/'.$api;
			$response = wp_remote_post($url, $args);
			if ( is_wp_error( $response ) ) {
				// Error out.
				$keep_going = false;
			} else {
				$data = wp_remote_retrieve_body($response);
				$images = json_decode($data);
				if($images->status == 200){
					if(!empty($images)){
						if($tab == 'Designs'){
							$page = count($images->images);
							$load_btn = '';
							if($page == 20){
								$load_btn = 'true';
							}
							$html ='';
							foreach($images->images as $image){
								if(!empty($image->thumb) || !empty($image->preview)){
									$html .= '<div class="cd-gallery-grid">
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
							$data = array('status' => 'true', 'page' => $load_btn, 'html'=> $html);
						} else if($tab == 'Bundles'){
							$page = count($images->bundles);
							$load_btn = '';
							if($page == 20){
								$load_btn = 'true';
							}
							$html ='';
							foreach($images->bundles as $image){
								if(!empty($image->thumb) || !empty($image->preview)){
									$html .= '<div class="cd-gallery-grid">
										<div class="cd-upload-image">
											<img src="'.esc_url($thumb[0]).'" class="img-fluid" />
											<div class="cd-action">
												<ul>
													<li>
														<a href="'.esc_url($image->thumb).'" class="cd-btn" target="_blank">Edit</a>
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
							$data = array('status' => 'true', 'page' => $load_btn, 'html'=> $html);
						} else {
							$page = count($images->packages);
							$load_btn = '';
							if($page == 20){
								$load_btn = 'true';
							}
							$html ='';
							foreach($images->packages as $image){
								if(!empty($image->thumb) || !empty($image->preview)){
									$html .= '<div class="cd-gallery-grid">
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
							$data = array('status' => 'true', 'page' => $load_btn, 'html'=> $html);
						}
					}
				} else {
					$html ='Images Not Found !';
					$data = array('status' => 'false', 'page' => 'false', 'html'=> $html);
				}
			}
		}
		echo json_encode($data);
		wp_die();
	}
	
	/* 
	* Load more
	*/
	public function clickdesigns_loadmore(){
		$key = get_option('clickdesign_api');
		$page = sanitize_text_field($_POST['page']);
		$keyword = sanitize_text_field($_POST['keyword']);
		$tab = sanitize_text_field($_POST['tab']);
		$pages = (int)$page + 1;

		if(!empty($page) && !empty($tab)){
			$api = $tab == 'Designs' ? 'getMyImage': ($tab == 'Bundles'? 'getMyBundles' : ($tab == 'Package'? 'getMyPackage' : '') );
			$args = array(
				'headers' => array(
				  'Apikey' => $key
				),
				'body' => array(
					'page' => $pages,
					'show' => '20',
					'keyword' => $keyword
				),
			);
			$keep_going = true;
			$url =  'https://api.clickdesigns.com/v1/'.$api;
			$response = wp_remote_post($url, $args);
			if ( is_wp_error( $response ) ) {
				// Error out.
				$keep_going = false;
			} else {
				$data = wp_remote_retrieve_body($response);
				$images = json_decode($data);
				if(!empty($images)){
					if($tab == 'Designs'){
						foreach($images->images as $image){
							if(!empty($image->thumb) || !empty($image->preview)){
								$thumb = explode('?', $image->thumb);
								$preview = explode('?', $image->preview);
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
					} else if($tab == 'Bundles'){
						foreach($images->bundles as $image){
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
					} else {
						foreach($images->packages as $image){
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
				}
			}
		}
		wp_die();
	}
	
	/* 
	* Images upload media
	*/
	function clickdesigns_upload_media(){
		$image = sanitize_url($_POST['image']);
		$format = sanitize_text_field($_POST['format']);    
		$img_title = sanitize_text_field($_POST['title']);
		$newimg = str_replace('https://cdn2.clickdesigns.com', 'https://clkdgn2.imgix.net/', $image);
		$new_image = $newimg.'?fm='.$format;
		$title = $img_title.'.'.$format;
		if(!empty($title) && !empty($image)){
			$site = $new_image;
			$tmp = download_url( $site ); 
			$file_array = array(
				'name' => $title,
				'tmp_name' => $tmp
			);
			if ( is_wp_error( $tmp ) ) {
				@unlink( $file_array['tmp_name'] );
				return $tmp;
			}
			$post_id = '0'; 
			$id = media_handle_sideload( $file_array, $post_id );
			if ( is_wp_error( $id ) ) {
				@unlink( $file_array['tmp_name'] );
				return $id;
			}
			$value = wp_get_attachment_url( $id );
			$data = array('status' => 'true', 'id'=> $id, 'url'=> $value);
			echo json_encode($data);
			wp_die();
		}
		wp_die();
	}	
	
	/* 
	* User Image
	*/
	public function clickdesigns_user_media(){
		$key = get_option('clickdesign_api');
		$tab = sanitize_text_field($_POST['tab']);
		$keyword = sanitize_text_field($_POST['keyword']);
		$user = sanitize_text_field($_POST['user']);
		if(!empty($tab) && !empty($key) && !empty($user)){
			$api = $tab == 'Designs' ? 'getMyImage': ($tab == 'Bundles'? 'getMyBundles' : ($tab == 'Package'? 'getMyPackage' : '') );
			$html = ''; 
			$count = '';
			if($user == 'All'){
				$args = array(
					'headers' => array(
					  'Apikey' => $key
					),
					'body' => array(
						'page' => 1,
						'show' => '20',
						'keyword' => $keyword,
					)
				);
			} else {
				$args = array(
					'headers' => array(
					  'Apikey' => $key
					),
					'body' => array(
						'page' => 1,
						'show' => '20',
						'keyword' => $keyword,
						'agencyUserID' => $user
					)
				);
			}
			$keep_going = true;
			$url =  'https://api.clickdesigns.com/v1/'.$api;
			$response = wp_remote_post($url, $args);
			if ( is_wp_error( $response ) ) {
				// Error out.
				$keep_going = false;
			} else {
				$data = wp_remote_retrieve_body($response);
				$images = json_decode($data);
				if($images->status == 200){
					$count = count($images->images);
					if(!empty($images)){
						if($tab == 'Designs'){
							foreach($images->images as $image){
								if(!empty($image->thumb) || !empty($image->preview)){
									$html.= '<div class="cd-gallery-grid">
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
						} else if($tab == 'Bundles'){
							foreach($images->bundles as $image){
								if(!empty($image->thumb) || !empty($image->preview)){
									$html.= '<div class="cd-gallery-grid">
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
						} else {
							foreach($images->packages as $image){
								if(!empty($image->thumb) || !empty($image->preview)){
									$html.= '<div class="cd-gallery-grid">
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
					}
				} else {
					$html.= '<p>Designs Not Found</p>';
				}
			}
		}
		$data = array('item' => $count, 'html'=> $html);
		echo json_encode($data);
		wp_die();
	}
}
$Ajax = new ClickDesignsAjax();

