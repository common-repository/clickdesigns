<?php 
namespace SettingClickDesigns;
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
// ClickDesigns data class 
class ClickDesignsSetting{
	
	//ClickDesigns data construct
    public function __construct() {
		// Admin menu hook
		add_action( 'admin_menu', array($this, 'clickdesigns_admin_menu'));
		
		// Enqueue CSS & Js file hook
		add_action( 'admin_enqueue_scripts', array($this, 'clickdesigns_assets'));
		
		// Enqueue Js file hook for elementor page builder
		add_action( 'elementor/editor/after_enqueue_scripts', array($this, 'clickdesigns_editor_elementor_scripts'));
		
		// Enqueue CSS file hook for elementor page builder
		add_action( 'elementor/editor/before_enqueue_styles', array($this, 'clickdesigns_editor_elementor_styles'));
		
		// Enqueue CSS & Js file hook
		add_action( 'wp_enqueue_scripts', array($this, 'clickdesigns_enqueue_assets'));
		
	}
	
	// Enqueue CSS & Js file
	public function clickdesigns_assets() {
		// Css
		wp_enqueue_style('toastr.min', CLICKDESIGNS_PLUGIN_URL . '/assets/css/toastr.min.css', array(), CLICKDESIGNS_VERSION, 'all');
		wp_enqueue_style('fonts', CLICKDESIGNS_PLUGIN_URL . '/assets/css/fonts.css', array(), CLICKDESIGNS_VERSION, 'all');
		wp_enqueue_style('clickdesigns-style', CLICKDESIGNS_PLUGIN_URL . '/assets/css/clickdesigns-style.css', array(), CLICKDESIGNS_VERSION, 'all');

		// js
		wp_enqueue_media();
		wp_enqueue_script( 'toastr', CLICKDESIGNS_PLUGIN_URL . '/assets/js/toastr.min.js', array(), CLICKDESIGNS_VERSION, true);
		wp_enqueue_script( 'clickdesigns-custom', CLICKDESIGNS_PLUGIN_URL . '/assets/js/clickdesigns-custom.js', array(), CLICKDESIGNS_VERSION, true);

		// ajax
		wp_localize_script( 'clickdesigns-custom', 'frontendajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
	}	
	
	// Elementor editor script
	public function clickdesigns_editor_elementor_scripts() {
		wp_enqueue_media();
		wp_register_script( 'clickdesigns-elemenor', plugins_url( '../assets/js/clickdesigns-custom.js', __FILE__ ) );
		wp_localize_script( 'clickdesigns-elemenor', 'frontendajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
		wp_enqueue_script( 'clickdesigns-elemenor' );
	}
	
	// Elementor editor style
	public function clickdesigns_editor_elementor_styles() {
		wp_register_style( 'clickdesigns-elemenor', plugins_url( '../assets/css/clickdesigns-style.css', __FILE__ ) );
		wp_enqueue_style( 'clickdesigns-elemenor' );
	}
	
	// enqueue style & JS
	public function clickdesigns_enqueue_assets() {
		wp_enqueue_style('toastr.min', CLICKDESIGNS_PLUGIN_URL . '/assets/css/toastr.min.css', array(), CLICKDESIGNS_VERSION, 'all');
		wp_enqueue_style('clickdesigns-style', CLICKDESIGNS_PLUGIN_URL . '/assets/css/clickdesigns-style.css', array(), CLICKDESIGNS_VERSION, 'all');

		// js
		wp_enqueue_media();
		wp_enqueue_script( 'toastr', CLICKDESIGNS_PLUGIN_URL . '/assets/js/toastr.min.js', array(), CLICKDESIGNS_VERSION, true);
		
		wp_enqueue_script( 'clickdesigns-custom', CLICKDESIGNS_PLUGIN_URL . '/assets/js/clickdesigns-custom.js', array(), CLICKDESIGNS_VERSION, true);
 
		// ajax
		wp_localize_script( 'clickdesigns-custom', 'frontendajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
	}
	
	// Admin menu
	public function clickdesigns_admin_menu(){
		add_menu_page(
			esc_html__( ' ClickDesigns', 'clickdesigns' ),
			esc_html__( 'ClickDesigns', 'clickdesigns' ),
			'manage_options',
			'clickdesigns',
			array($this,'clickdesigns_main_page_callback'),
			CLICKDESIGNS_PLUGIN_URL.'/assets/images/ClickDesigns-Logo-small.png',
			6
		);
	}
	
	// Admin menu callback function
	public function clickdesigns_main_page_callback(){
		require CLICKDESIGNS_PLUGIN_PATH.'/includes/clickdesigns-stetting-page.php';  
	}	
}
$Setting = new ClickDesignsSetting();