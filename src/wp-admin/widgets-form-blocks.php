<?php
/**
 * The block-based widgets editor, for use in widgets.php.
 *
 * @package WordPress
 * @subpackage Administration
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Flag that we're loading the block editor.
$current_screen = get_current_screen();
$current_screen->is_block_editor( true );

$block_editor_context = new WP_Block_Editor_Context();

$preload_paths = array(
	array( '/wp/v2/media', 'OPTIONS' ),
	'/wp/v2/sidebars?context=edit&per_page=-1',
	'/wp/v2/widgets?context=edit&per_page=-1&_embed=about',
);
block_editor_rest_api_preload( $preload_paths, $block_editor_context );

$editor_settings = get_block_editor_settings( array(), $block_editor_context );

wp_add_inline_script(
	'wp-edit-widgets',
	sprintf(
		'wp.domReady( function() {
			wp.editWidgets.initialize( "widgets-editor", %s );
		} );',
		wp_json_encode( $editor_settings )
	)
);

// Preload server-registered block schemas.
wp_add_inline_script(
	'wp-blocks',
	'wp.blocks.unstable__bootstrapServerSideBlockDefinitions(' . wp_json_encode( get_block_editor_server_block_settings() ) . ');'
);

wp_add_inline_script(
	'wp-blocks',
	sprintf( 'wp.blocks.setCategories( %s );', wp_json_encode( get_block_categories( 'widgets-editor' ) ) ),
	'after'
);

wp_enqueue_script( 'wp-edit-widgets' );
wp_enqueue_script( 'admin-widgets' );
wp_enqueue_style( 'wp-edit-widgets' );

/** This action is documented in edit-form-blocks.php */
do_action( 'enqueue_block_editor_assets' );

require_once ABSPATH . 'wp-admin/admin-header.php';

/**
 * Fires before the Widgets administration page content loads.
 *
 * @since 3.0.0
 */
do_action( 'widgets_admin_page' );
?>

<div id="widgets-editor" class="blocks-widgets-container"></div> 

<?php
require_once ABSPATH . 'wp-admin/admin-footer.php';
