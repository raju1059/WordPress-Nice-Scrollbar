<?php
/*
Plugin Name:  T Scrollbar
Text Domain: tit
Plugin URI:http://raju_ahmed.wordpress.org
Description: This plugin can change the color, width,height of scrollbar  in the wordpress  website.
Version: 2.1
Author: Raju Ahmed
Author URI:http://raju_ahmed.wordpress.org
License: GPLv2 or later
*/
require_once dirname( __FILE__ ) . '/class-setting.php';
require_once dirname( __FILE__ ) . '/class-disply.php';
/**
 * WordPress scrollbar API
 *
 * @author Raju Ahmed
 */
if ( !class_exists('TistaScrollAPI' ) ):
class TistaScrollAPI {
	
    private $api;
    public $display;
	
	function __construct() {
        $this->api = new TistaScrollSetting;
        $this->display = new TistaScrollDisplay;
        add_action( 'plugins_loaded', array($this, 'texdomain') );
        add_action( 'admin_menu', array($this, 'adminmenu') );
		add_action( 'admin_init', array($this, 'jmra_set_section') );		
    }
	/**
     * Load Textdomain
     *
     * @return prameter
     */
	function texdomain() {
		$lang = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
		load_plugin_textdomain('tit', false, $lang);
	}
	/**
     * Register admin menu
     *
     * @return array
     */
	function adminmenu(  ) { 
		$menu_page = add_menu_page( __( 'T Scrollbar','tit' ), __( 'T Scrollbar','tit' ), 'read', 'tscrollbar', array($this, 'custom_form'));
		
		add_action('admin_print_scripts-' . $menu_page, array($this, 'custom_style'));
	}
	/**
     * Load custom style 
     *
     * @return array
     */
	function custom_style(){
		wp_register_style('tcss', plugins_url( '/css/options_page.css' , __FILE__ ) );
		wp_enqueue_style( 'tcss' );
	}
	function jmra_set_section() {
		
		//set the settings);
        $this->api->set_fields( $this->jmra_field() );

        //initialize settings
        $this->api->jmra_setion();
    }
	/**
     * Register form field
     *
     * @return array
     */
	function jmra_field(){
		
		$arg = array(
			array(
				'name'=>'cursorcolor',
				'label'=> __( 'Color', 'tit' ),
				'desc'=> __( 'You can change the color of scrollbar.', 'tit' ),
				'type'=>'color',
				'default'=>'#81d742  ',
			),
			array(
				'name'=>'cursorwidth',
				'label'=> __( 'Width', 'tit' ),
				'desc'=> __( 'You can change the scrollbar width  here.The default scrollbar width is 10px.', 'tit' ),
				'default'=>'10px',
				'type'=>'text',
				'size'=>'',
			),
			array(
				'name'=>'cursorborderradius',
				'label'=> __( 'Border', 'tit' ),
				'desc'=> __( 'You can change the scrollbar border radius .The default border radius is 4px.', 'tit' ),
				'default'=>'4px',
				'type'=>'text',
				'size'=>'',
			),
			array(
				'name'=>'cursorborder',
				'label'=> __( 'Border style', 'tit' ),
				'desc'=> __( 'You can change the scrollbar border style . The default border style is 1px solid #fff.', 'tit' ),
				'default'=>'1px solid #fff',
				'type'=>'text',
				'size'=>'',
			),
			array(
				'name'=>'scrollspeed',
				'label'=> __( 'Speed', 'tit' ),
				'desc'=>__( 'You can change the scrollbar speed  here.The default scrollbar speed value is 60.', 'tit' ),
				'default'=>'60',
				'type'=>'text',
				'size'=>'',
			),
			array(
				'name'=>'autohidemode',
				'label'=> __( 'Automode', 'tit' ),
				'desc'=> __( 'You can change the Scrollbar auto hide. Default option is disable auto  mode hide.', 'tit' ),
				'default'=>'false',
				'type'=>'radio',
				'options' => array(
                        'true' => __( 'Enable Auto Mode Hide', 'tit' ),
                        'false' => __( 'Disable Auto Mode Hide', 'tit' ),
                    )
			),
			array(
				'name'=>'horizrailenabled',
				'label'=> __( 'Horizontal', 'tit' ),
				'desc'=> __( 'You can enable the horizontal scrollbar from here. Default is enable.', 'tit' ), 
				'type'=>'radio',
				'default'=>'false',
				'options' => array(
                        'true' => __( 'Enable horizontal visibility', 'tit' ),
                        'false' => __( 'Disable Horizontal visibility', 'tit' ),
                    )
			),
			array(
				'name'=>'touchbehavior',
				'label'=> __( 'Touch behaviour', 'tit' ),
				'desc'=> __( 'You can change the scrollbar touch behaviour. The default is Disable Touch Behaviour.', 'tit' ),
				'type'=>'radio',
				'default'=>'false',
				'options' => array(
                        'true' => __( 'Enable Touch Behaviour', 'tit' ),
                        'false' =>  __( 'Disable Touch Behaviour', 'tit' ),
                    )
			),
			array(
				'name'=>'adminscroll',
				'label'=> __( 'Backend', 'tit' ),
				'desc'=> __( 'If you like to disable scrollbar in admin area select no.Default yes. ', 'tit' ),
				'type'=>'select',
				'default'=>'yes',
				'size'=>'webcode-scrollbar-inputfield',
				'options' => array(
                        'yes' => __( 'YES, I LIKE', 'tit' ),
                        'no' => __( 'NO, DISLIKE', 'tit' ),
                    )
			),
						
		);
		return $arg;
	}
	/**
     * Load custom form 
     *
     * @return array
     */
	function custom_form(  ) { 	
			$this->api->show_forms();
	}
}
endif;
$titapi = new TistaScrollAPI();