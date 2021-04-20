<?php
if ( !class_exists('TistaScrollSetting' ) ):
class TistaScrollSetting {
	/**
     * Settings fields array
     *
     * @var array
     */
    private $settings_fields = array();
	/**
     * Singleton instance
     *
     * @var object
     */
    private static $_instance;
	
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'ttenqueue_scripts' ) );
        add_action( 'init', array( $this, 'scrollbar_script' ) );
	}
	/**
     * Enqueue scripts and styles
     */
    function ttenqueue_scripts() {
		wp_enqueue_script( 'jquery' );
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );		
	}
	/**
     * Load script for scrollbar
     *
     * @return array
     */
	function scrollbar_script() {
		wp_enqueue_script('scroll-scriipt',plugins_url( '/js/nicescroll.min.js' , __FILE__ ),array( 'jquery' ));
	}
	/**
     * Set settings fields
     *
     * @param array   $fields settings fields array
     */
    function set_fields( $fields ) {
        $this->settings_fields = $fields;

        return $this;
    }
    function add_field( $section, $field ) {
        $defaults = array(
            'name' => '',
            'label' => '',
            'desc' => '',
            'type' => 'text'
        );

        $arg = wp_parse_args( $field, $defaults );
        $this->settings_fields[$section][] = $arg;

        return $this;
    }
	/**
     * Error massege
     *
     * @return array
     */
	function tset_massege() {
		settings_errors();
	}
	/**
     * Register section and fields
     *
     * @return array
     */
	function jmra_setion() {
		//Register option
		register_setting( 'tformid', 'toption','sanitize_options' );
		
		//Register settings section
		add_settings_section('tset_section', __( 'Settings', 'tit' ), array($this, 'tset_massege'), 'tformid' );
		
		//Register settings fields
		foreach($this->settings_fields as $options){
                $type = isset( $options['type'] ) ? $options['type'] : 'text';
				$args = array(
                    'id' => $options['name'],
                    'desc' => isset( $options['desc'] ) ? $options['desc'] : '',
                    'label' => $options['label'],
                    'sec' => 'toption',
                    'size' => isset( $options['size'] ) ? $options['size'] : null,
                    'option' => isset( $options['options'] ) ? $options['options'] : '',
                    'std' => isset( $options['default'] ) ? $options['default'] : '',
                    'sanitize_callback' => isset( $options['sanitize_callback'] ) ? $options['sanitize_callback'] : '',
                );
				add_settings_field( $options['name'],$options['label'],array($this, 'callback_'.$type), 'tformid', 'tset_section',$args);
		}
	}
	/**
     * Displays a text field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_text( $args ) {

        $value = esc_attr( $this->tget_option( $args['id'], $args['sec'], $args['std'] ) );
        $size = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';

        $html = sprintf( '<input type="text" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['sec'], $args['id'], $value );
        $html .= sprintf( '<span class="description"> %s</span>', $args['desc'] );

        echo $html;
    }
	/**
     * Displays a multicheckbox a settings field
     *
     * @param array   $args settings field args
     */
    function callback_radio( $args ) {

        $value = $this->tget_option( $args['id'], $args['sec'], $args['std'] );
        $html = '';
        foreach ( $args['option'] as $key => $label ) {
            $html .= sprintf( '<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s"%4$s />', $args['sec'], $args['id'], $key, checked( $value, $key, false ) );
            $html .= sprintf( '<label for="%1$s[%2$s][%4$s]"> %3$s</label><br>', $args['sec'], $args['id'], $label, $key );
        }
        $html .= sprintf( '<span class="description"> %s</label>', $args['desc'] );

        echo $html;
    }
	/**
     * Displays a selectbox for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_select( $args ) {

        $value = esc_attr( $this->tget_option( $args['id'], $args['sec'], $args['std'] ) );
        $size = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';

        $html = sprintf( '<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['sec'], $args['id'] );
        foreach ( $args['option'] as $key => $label ) {
            $html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
        }
        $html .= sprintf( '</select>' );
        $html .= sprintf( '<span class="description"> %s</span>', $args['desc'] );
        echo $html;
    }
	/**
     * Displays a color picker field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_color( $args ) {
        $value = esc_attr( $this->tget_option( $args['id'], $args['sec'], $args['std'] ) );
        $size = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
        $html = sprintf( '<input type="text" class="%1$s-text jmra-color-field" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" />', $size, $args['sec'], $args['id'], $value, $args['std'] );
        $html .= sprintf( '<span class="description" style="display:block;"> %s</span>', $args['desc'] );
        echo $html;
    }
	
    /**
     * Sanitize callback for Settings API
     */
    function sanitize_options( $options ) {
        foreach( $options as $option_slug => $option_value ) {
            $sanitize_callback = $this->get_sanitize_callback( $option_slug );

            // If callback is set, call it
            if ( $sanitize_callback ) {
                $options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
                continue;
            }

            // Treat everything that's not an array as a string
            if ( !is_array( $option_value ) ) {
                $options[ $option_slug ] = sanitize_text_field( $option_value );
                continue;
            }
        }
        return $options;
    }

    /**
     * Get sanitization callback for given option slug
     *
     * @param string $slug option slug
     *
     * @return mixed string or bool false
     */
    function get_sanitize_callback( $slug = '' ) {
        if ( empty( $slug ) )
            return false;
        // Iterate over registered fields and see if we can find proper callback
        foreach( $this->settings_fields as $section => $options ) {
            foreach ( $options as $option ) {
                if ( $option['name'] != $slug )
                    continue;
                // Return the callback name
                return isset( $option['sanitize_callback'] ) && is_callable( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : false;
            }
        }
        return false;
    }
	/**
     * Get the value of a settings field
     *
     * @param string  $option  settings field name
     * @param string  $section the section name this field belongs to
     * @param string  $default default text if it's not found
     * @return string
    */
    function tget_option( $option, $section, $default = '' ) {
        $options = get_option( $section );
        if ( isset( $options[$option] ) ) {
            return $options[$option];
        }else{
        return $default;
		}
    }
	/**
     * Show form 
     *
     * @return array
     */
	function show_forms(){
			echo '<div class="wrap">';
			echo '<div class="welcome-panel">';
			echo '<div class="welcome-panel-content">';
				echo '<form action="options.php" method="POST" enctype="multipart/form-data"  class="form-webcode">';
							settings_fields('tformid' );
							do_settings_sections('tformid' );
							submit_button();						
				echo '</form>';
				$this->script();
			echo '</div>';
			echo '</div>';
			echo '</div>';
	}
	
    /**
     * Tabbable JavaScript codes & Initiate Color Picker
     *
     * This code uses localstorage for displaying active tabs
     */
    function script() {
        ?>
        <script>
            jQuery(document).ready(function($) {
                //Initiate Color Picker
                $('.jmra-color-field').wpColorPicker();
            });
        </script>
        <?php
    }
}
endif;