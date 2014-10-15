<?php
/*
   Plugin Name: Typekitty
   Plugin URI: https://github.com/stephansmith/typekitty
   Description: Minimal Typekit Plugin for WordPress
   Version: 1.0
   Author: Stephan Smith
   Author URI: http://stephan-smith.com
   License: GPL2
   */

class Typekitty {

	/**
	 * Holds the values to be used in the fields callbacks
	 */
	public $options;
	

	public function __construct()
	{
		
		
		// Set class property
		$this->options = get_option( 'typekitty' );
		
		add_action( 'wp_head', array( $this, 'hook_javascript' ) );
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}
	
	
	public function get_typekitty() {
		return get_option( 'typekitty' );
	}


	public $typekitty_sections = array(
		array(
			'id'=>'typekitty_general',
			'fields' => array(
				array(
					'name'=>'kitId',
					'label'=>'Kit ID'
				)
			)
		)
	);

	public function add_plugin_page() {
		add_options_page(
			'Typekitty',
			'Typekitty',
			'manage_options',
			'typekitty',
			array( $this, 'create_admin_page' )
		);
	}


	public function create_admin_page() {
		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		?>
		<div class="wrap">
			<h2>Typekitty</h2>
			<form method="post" action="options.php">
				<?php
					// This prints out all hidden setting fields
					settings_fields( 'typekitty_group' );
					do_settings_sections( 'typekitty' );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function page_init() {
		register_setting(
			'typekitty_group', // Option group
			'typekitty' // Option name
		);

		foreach ( $this->typekitty_sections as $section ) {
			add_settings_section(
				$section['id'], // ID
				$section['label'], // Title
				array( $this, 'print_section_info' ), // Callback
				'typekitty' // Page
			);
		}
	}

	public function print_section_info( $arg ) {

		foreach ( $this->typekitty_sections as $typekitty_section ) {

			if ( $typekitty_section['id'] == $arg['id'] ) {

				foreach ( $typekitty_section['fields'] as $field ) {
					add_settings_field(
						$field['name'],
						$field['label'],
						array( $this, 'print_field' ),
						'typekitty',
						$typekitty_section['id'],
						$field
					);
				}
			}

		}
		return;
	}

	public function print_field( $args ) {

		$options = $this->options;

		printf(
			'<input id="' . $args['name'] . '" class="regular-text" type="text" name="typekitty[' . $args['name'] . ']" value="%s" />',
			isset( $options[ $args['name'] ] ) ? esc_attr( $options[ $args['name'] ]) : ''
		);

		return;
	}
	
	public function hook_javascript() {
		
		$options = $this->options;
	
		$output = '<script>(function(d) { var config = {  kitId: "' . $options['kitId'] . '", scriptTimeout: 3000 }, h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src="//use.typekit.net/"+config.kitId+".js";tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s) })(document);</script>';
		
		echo $output;
	}


}

$Typekitty = new Typekitty();