<?php
/*
Plugin Name: Slackvite
Plugin URI: http://hbt.io/
Version: 1.0.1
Author: Harri Bell-Thomas
Author URI: http://hbt.io/
*/

class Slackvite {

	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;

	/**
	 * The array of templates that this plugin tracks.
	 */
	protected $templates;

	/**
	 * Returns an instance of this class.
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new Slackvite();
		}

		return self::$instance;

	}

	public $flash_message;

	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	private function __construct() {

		$this->templates = array();


		// Add a filter to the attributes metabox to inject template into the cache.
    	if ( version_compare( floatval($GLOBALS['wp_version']), '4.7', '<' ) ) { // 4.6 and older
        		add_filter(
            		'page_attributes_dropdown_pages_args',
            		array( $this, 'register_project_templates' )
        		);
    	} else { // Add a filter to the wp 4.7 version attributes metabox
        		add_filter(
            		'theme_page_templates', array( $this, 'add_new_template' )
        		);
    	}


		// Add a filter to the save post to inject out template into the page cache
		add_filter(
			'wp_insert_post_data',
			array( $this, 'register_project_templates' )
		);


		// Add a filter to the template include to determine if the page has our
		// template assigned and return it's path
		add_filter(
			'template_include',
			array( $this, 'view_project_template')
		);

		// action to process form posts
		add_action( 'init', array( $this, 'slackvite_invite_signup' ) );

		//create new top-level menu
		add_action('admin_menu', array( $this, 'slackvite_create_menu' ) );

		add_action( 'admin_init', array($this, 'register_slackvite_settings' ) );

		// Add your templates to this array.
		$this->templates = array(
			'templates/slackvite.php' => 'Slackvite',
		);

        add_action( 'wp_enqueue_scripts', array( $this, 'register_style') );
	}

	/**
     	 * Adds our template to the page dropdown for v4.7+
     	 *
     	 */
    	public function add_new_template( $posts_templates ) {
        	$posts_templates = array_merge( $posts_templates, $this->templates );
        	return $posts_templates;
    	}

	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 *
	 */

	public function register_project_templates( $atts ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list.
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		}

		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;

	}

	/**
	 * Checks if the template is assigned to the page
	 */
	public function view_project_template( $template ) {

		// Get global post
		global $post;

		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}

		// Return default template if we don't have a custom one defined
		if ( !isset( $this->templates[get_post_meta(
			$post->ID, '_wp_page_template', true
		)] ) ) {
			return $template;
		}

		$file = plugin_dir_path(__FILE__). get_post_meta(
			$post->ID, '_wp_page_template', true
		);

		// Just to be safe, we check if the file exist first
		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo $file;
		}

		// Return template
		return $template;

	}

    public function register_style() {
        wp_register_style( 'slackvite-styles',  plugin_dir_url( __FILE__ ) . 'templates/slackvite.css' );
        wp_enqueue_style( 'slackvite-styles' );
    }

    public function get_background_image() {
		return 'https://source.unsplash.com/random/2000x2000';
	}

	public function get_landing_page_url() {
		global $wp;
		return home_url(add_query_arg(array(),$wp->request));
	}

	public function slackvite_invite_signup() {
		if ( isset( $_POST['slackvite-email'] ) ) {

			$args = array(
				'body' => array(
					'email'	=> 	$_POST['slackvite-email'],
					'key'	=>	'80984f3d63fc57fafa5945b6c960d3150a987d8f'
				)
			);

			$response = wp_remote_post( 'http://localhost:8000/api/invite', $args);

			if ( 200 == $response['response']['code'] ) {
				$this->flash_message = '<strong>Success!</strong> ' . $response['body']['message'];
			} elseif ( 422 == $response['response']['code'] ) {
				$this->flash_message = '<strong>There was a problem:</strong> '.$response['body'];
			}

			if ( is_wp_error($response) ) {
				$this->flash_message = 'Well, something bad happened.';
			}
		}
	}

	function slackvite_create_menu() {
		add_submenu_page('options-general.php', 'Slackvite Settings', 'Slackvite Settings', 'administrator', __FILE__, array($this, 'slackvite_settings_page' ) , plugins_url('/images/icon.png', __FILE__) );
	}

	function slackvite_settings_page() { ?>
		<div class="wrap">
			<h1>Slackvite Plugin Settings</h1>

			<form method="post" action="options.php">
			    <?php settings_fields( 'slackvite-settings-group' ); ?>
			    <?php do_settings_sections( 'slackvite-settings-group' ); ?>
			    <table class="form-table">
			        <tr valign="top">
				        <th scope="row">Slackvite Team API Key</th>
				        <td><input type="text" name="slackvite_team_api_key" value="<?php echo esc_attr( get_option('slackvite_team_api_key') ); ?>" /></td>
			        </tr>
			    </table>

			    <?php submit_button(); ?>

			</form>
		</div>
	<?php }

	function register_slackvite_settings() {
		//register our settings
		register_setting( 'slackvite-settings-group', 'slackvite_team_api_key' );
	}
}
add_action( 'plugins_loaded', array( 'Slackvite', 'get_instance' ) );
