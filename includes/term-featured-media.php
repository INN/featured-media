<?php
/**
 * Attach the post Featured Media dialog to terms, using the term meta functions from Largo v0.5.5.3/this plugin
 */

/**
 * Add the "Set Featured Media" button in the term edit page
 *
 * @since 0.5.4
 * @see largo_term_featured_media_enqueue_post_editor
 */
function largo_add_term_featured_media_button( $context = '' ) {
	// Post ID here is the id of the post that Largo uses to keep track of the term's metadata. See largo_get_term_meta_post.
	$post_id = largo_get_term_meta_post( $context->taxonomy, $context->term_id );

	$has_featured_media = largo_has_featured_media($post_id);
	$language = (!empty($has_featured_media))? 'Edit' : 'Set';
	$featured = largo_get_featured_media($post_id);

	?>
	<tr class="form-field">
		<th scope="row" valign="top"><?php _e('Term banner image', 'largo'); ?></th>
		<td>
			<p><a href="#" id="set-featured-media-button" class="button set-featured-media add_media" data-editor="content" title="<?php echo $language; ?> Featured Media"><span class="dashicons dashicons-admin-generic"></span> <?php echo $language; ?> Featured Media</a> <span class="spinner" style="display: none;"></span></p>
			<p class="description">This image will be displayed on the top of the term's archive page.</p>
			<input type="hidden" id="post_ID" value="<?php echo $post_id ?>" />
			<input type="hidden" id="featured_image_id" value="<?php echo $featured['attachment'] ;?>" />

			<?php # echo get_the_post_thumbnail($post_id); ?>
		</td>
	</tr>
	<?php
}
add_action( 'edit_category_form_fields', 'largo_add_term_featured_media_button');
add_action( 'edit_tag_form_fields', 'largo_add_term_featured_media_button');

/**
 * Enqueue wordpress post editor on term edit page
 *
 * @param string $hook the page this is being called upon.
 * @since 0.5.4
 * @see largo_term_featured_media_button
 */
function largo_term_featured_media_enqueue_post_editor($hook) {
	if (!in_array($hook, array('edit.php', 'edit-tags.php')))
		return;

	wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'largo_term_featured_media_enqueue_post_editor', 1);

/**
 * Removes the embed-code, video and gallery media types from the term featured media editor
 *
 * @param array $types array of media types that can be used with the featured media editor
 * @since 0.5.4
 * @global $post Used to determine whether or not this button is being called on a post or on something else.
 */
function largo_term_featured_media_types($types) {
	global $post;
	if ( isset( $types['image'] ) && is_object($post) && $post->post_type == '_term_meta' ) {
		$ret =  array('image' => $types['image']);
		return $ret;
	}
	return $types;
}
add_filter('largo_default_featured_media_types', 'largo_term_featured_media_types', 10, 1);

