<?php
if ( !class_exists('TistaScrollDisplay' ) ):
class TistaScrollDisplay {
	
	public function __construct() {
		add_action( 'wp_head', array($this, 'scroll_display') );
		
		//admin scrollbar enable
		$adminscroll = esc_attr( $this->jmra_get_option('adminscroll','toption',''));
		switch($adminscroll){
			case 'yes':
				add_action( 'admin_head', array($this, 'scroll_display') );
			break;
			case 'no': 
			break;
			default:
				add_action( 'admin_head', array($this, 'scroll_display') );
		}
    }
	/**
     * Display scrollbar in frontend 
     *
     * @return array
     */
	function scroll_display() {
		$cursorcolor = esc_attr( $this->jmra_get_option('cursorcolor','toption','#1e73be'));
		$cursorwidth = esc_attr( $this->jmra_get_option('cursorwidth','toption','10px'));
		$cursorborderradius = esc_attr( $this->jmra_get_option('cursorborderradius','toption','4px'));
		$autohidemode = esc_attr( $this->jmra_get_option('autohidemode','toption','false'));
		$cursorborder = esc_attr( $this->jmra_get_option('cursorborder','toption','1px solid #fff'));
		$scrollspeed = esc_attr( $this->jmra_get_option('scrollspeed','toption','60'));
		$horizrailenabled = esc_attr( $this->jmra_get_option('horizrailenabled','toption','true'));
		$touchbehavior = esc_attr( $this->jmra_get_option('touchbehavior','toption','false'));

	?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery("html").niceScroll({
				cursorcolor:"<?php echo $cursorcolor; ?>",
				cursorwidth:"<?php echo $cursorwidth; ?> ",
				cursorborderradius:"<?php echo $cursorborderradius;  ?>",
				autohidemode:<?php echo $autohidemode; ?>,
				cursorborder:"<?php echo $cursorborder; ?>",
				scrollspeed:"<?php echo $scrollspeed; ?>",
				horizrailenabled:<?php echo $horizrailenabled; ?>,
				touchbehavior:<?php echo $touchbehavior; ?>,
				});
			});
		</script>
	<?php
	}
	/**
     * Get the value of a settings field
     *
     * @param string  $option  settings field name
     * @param string  $section the section name this field belongs to
     * @param string  $default default text if it's not found
     * @return string
    */
    function jmra_get_option( $option, $section, $default = '' ) {
        $options = get_option( $section );
        if ( isset( $options[$option] ) ) {
            return $options[$option];
        }else{
        return $default;
		}
    }
}
endif;