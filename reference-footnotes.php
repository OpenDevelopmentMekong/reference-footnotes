<?php
/*
Plugin Name: Reference Footnotes Editor Button
Description: The plugin was created to make the user easy to add the footnote as the reference at the end of the content. A button of Reference Footnote was added into the TinyMCE Editor toolbar to add any footnotes with the proper syntax. Thus, the plugin allows to add elegant footnotes quickly on your page or website.
Just click on the Reference Footnotes icon, then a form will be apeared which has a textarea where you can enter the reference of the content and then click the Insert button into the editor content with the proeper syntax: [ref]Texts entering or <a hreft="#">link</a>[ref].

This is also supported in Khmer language, and can add other langauge by using po files.
Version: 1.0.0
Author: ODC: Huy Eng
License: CC0
Text Domain: referent-notes-editor-button
Domain Path: languages/
Note: Based on Simple Footnotes Editor Button
*/
if(!class_exists('reference_footnotes_TinyMCE')) {

	class reference_footnotes_TinyMCE  {

		function __construct() {
			//add js or css
			add_action('wp_enqueue_scripts', array(&$this, 'add_script'), 11);

			add_action('init', array(&$this, 'init'));
            /*
			 * Register shortcode
			 */     //[ref]texts[ref]
			add_shortcode('ref', array(&$this, 'shortcode'));


			add_filter('img_caption_shortcode', array( &$this, 'nested_img_caption_shortcode' ), 1, 3);

			// Filter the_content
			add_filter( 'the_content', array( &$this, 'the_content' ), 12 );
		}

		function init() {
			add_filter('mce_external_plugins', array($this, 'add_buttons_reference_footnotes'));
			add_filter('mce_buttons', array($this, 'register_buttons_reference_footnotes'));
			 load_plugin_textdomain( 'reference-footnotes', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n' );
			if((current_user_can('edit_posts') || current_user_can('edit_pages')) && 'true' == get_user_option('rich_editing')) {
				wp_enqueue_script( array ( 'wpdialogs' ) );
			 	wp_enqueue_style('wp-jquery-ui-dialog');
			    add_action('admin_footer', array($this, 'builder_reference_footnotes'));
			}
		}
		public function add_script(){
	       wp_enqueue_style("reference_footnotes_css", plugins_url("rfootnotes.css", __FILE__ ));
        }


		function add_buttons_reference_footnotes($plugins) {

			$plugins['referencefootnote'] = plugins_url( '/js/tinymce.js', __FILE__ );
			return $plugins;

		}

		function register_buttons_reference_footnotes($buttons) {

			$buttons[] = 'referencefootnote';
			return $buttons;

		}

		function builder_reference_footnotes() {
			?>
    			<div style="display:none;">
    			<form id="reference-footnotes" tabindex="-1">
    				<div style="margin: 1em">
    					<p class="howto"><?php _e( 'Enter the content of the reference footnote', 'reference-footnotes' ); ?></p>
    					<textarea id="reference-footnotes-content" rows="4" style="width: 95%; margin-bottom: 1em"></textarea>
    					<div class="submitbox" style="margin-bottom: 1em">
    						<div id="reference-footnotes-insert" class="alignright">
    							<input type="submit" value="<?php esc_attr_e( 'Insert', 'reference-footnotes' ); ?>" class="button-primary">
    						</div>
    						<div id="reference-footnotes-cancel">
    							<a class="submitdelete deletion" href="#"><?php _e( 'Cancel', 'reference-footnotes' ); ?></a>
    						</div>
    					</div>
    				</div>
    			</form>
    		</div>

			<?php
		} //end function

        function reference_footnotes( $content ) {
                global $id;

                if ( empty( $this->reference_footnotes[$id] ) )
                        return $content;
                $content .= '<div class="reference-footnote">';
				$content .= '<h4 id="reference-notes">'. __( 'References', 'reference-footnotes' ).'</h4>';
				$content .= '<ul id="reference-list">';
                foreach ( array_filter( $this->reference_footnotes[$id] ) as $num => $note ) {
                    $content .= '<li id="ref-' . $id . '-' . $num . '">
                                <a href="#return-note-' . $id . '-' . $num . '">' .sprintf( _n( '%s', '%s', $num, 'tereference-footnotesst' ), $num ).'</a>. '. do_shortcode( $note ) . '</li>';

                }

                $content .= '</ol></div>';
                return $content;
        }

		public function shortcode( $atts, $content = null ) {
                global $id;
                if ( null === $content )
                        return;
                if ( ! isset( $this->reference_footnotes[$id] ) )
                        $this->reference_footnotes[$id] = array( 0 => false );
                $this->reference_footnotes[$id][] = $content;
                $note = count( $this->reference_footnotes[$id] ) - 1;
                return '<sup><a class="reference_footnote" title="' . esc_attr( wp_strip_all_tags( $content ) ) . '" id="return-note-' . $id . '-' . $note . '" href="#ref-' . $id . '-' . $note . '">' . $note . '</a></sup>';
        }

		public function the_content( $content ) {
             if ( ! $GLOBALS['multipage'] )
                    return $this->reference_footnotes( $content );
             return $content;
    }

		public function nested_img_caption_shortcode($empty, $attr, $content = null) {
			  extract(
			    shortcode_atts(
			      array(
			      'id'    => '',
			      'align' => 'alignnone',
			      'width' => '',
			      'caption' => ''
			      ),
			      $attr,
			      'caption'
			    )
			  );

			  $caption = do_shortcode($caption);

			  if ( 1 > (int) $width || empty($caption) ):
			    return $content;
				endif;

			  if ( $id ):
					$id = 'id="' . esc_attr($id) . '" ';
				endif;

			  return '<div ' . $id . 'class="wp-caption ' . esc_attr($align) . '" style="width: ' . (10 + (int) $width) . 'px">'
			  . do_shortcode( $content ) . '<p class="wp-caption-text">' . $caption . '</p></div>';
		}

	}

}

if(class_exists('reference_footnotes_TinyMCE')) {

	$reference_footnotes_TinyMCE = new reference_footnotes_TinyMCE();

}
