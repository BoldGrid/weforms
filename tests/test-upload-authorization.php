<?php
/**
 * Upload authorization.
 *
 * @package weForms
 */

/**
 * A guest upload has to name one real field before any attachment exists.
 */
class WeForms_Upload_Authorization_Test extends WP_UnitTestCase {

	/**
	 * Published form.
	 *
	 * @var int
	 */
	private $form_id;

	/**
	 * Image field ID.
	 *
	 * @var int
	 */
	private $image_field_id;

	/**
	 * Accept local files created by the test.
	 *
	 * @param bool $accept Whether a local file may be checked.
	 *
	 * @return bool
	 */
	public function accept_local_file( $accept ) {
		unset( $accept );

		return true;
	}

	/**
	 * Install a published form with one image field and one file field.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		add_filter( 'weforms_upload_accept_local_file', array( $this, 'accept_local_file' ) );

		$this->form_id = $this->create_form( 'publish' );
		$this->image_field_id = $this->create_field(
			$this->form_id,
			array(
				'template' => 'image_upload',
				'name'     => 'photo',
				'max_size' => 100,
			)
		);
		$this->create_field(
			$this->form_id,
			array(
				'template'  => 'file_upload',
				'name'      => 'document',
				'max_size'  => 100,
				'extension' => array( 'pdf' ),
			)
		);
	}

	/**
	 * Remove the local-file test hook.
	 *
	 * @return void
	 */
	public function tear_down() {
		remove_filter( 'weforms_upload_accept_local_file', array( $this, 'accept_local_file' ) );
		parent::tear_down();
	}

	/**
	 * Create a contact form with settings that can be submitted.
	 *
	 * @param string $status Post status.
	 *
	 * @return int
	 */
	private function create_form( $status ) {
		$form_id = self::factory()->post->create(
			array(
				'post_type'   => 'wpuf_contact_form',
				'post_status' => $status,
				'post_title'  => 'Upload form',
			)
		);

		update_post_meta( $form_id, 'wpuf_form_settings', array() );

		return $form_id;
	}

	/**
	 * Create one stored form field.
	 *
	 * @param int   $form_id Form ID.
	 * @param array $field   Field properties.
	 *
	 * @return int
	 */
	private function create_field( $form_id, $field ) {
		return self::factory()->post->create(
			array(
				'post_type'    => 'wpuf_input',
				'post_status'  => 'publish',
				'post_parent'  => $form_id,
				'post_content' => maybe_serialize( $field ),
			)
		);
	}

	/**
	 * Write a temporary file.
	 *
	 * @param string $contents File contents.
	 *
	 * @return string
	 */
	private function temp_file( $contents ) {
		$path = wp_tempnam( 'weforms-upload' );
		file_put_contents( $path, $contents );

		return $path;
	}

	/**
	 * A one-pixel GIF.
	 *
	 * @return string
	 */
	private function gif_bytes() {
		return base64_decode( 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' );
	}

	/**
	 * Count attachment posts.
	 *
	 * @return int
	 */
	private function attachment_count() {
		$posts = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		return count( $posts );
	}

	/**
	 * A matching image is accepted and stored by nobody in the authorizer.
	 *
	 * @return void
	 */
	public function test_matching_image_is_authorized_without_creating_an_attachment() {
		$before = $this->attachment_count();
		$path   = $this->temp_file( $this->gif_bytes() );
		$result = weforms_authorize_upload(
			$this->form_id,
			array( 'name' => 'photo' ),
			array(
				'name'     => 'photo.gif',
				'tmp_name' => $path,
				'error'    => 0,
				'size'     => filesize( $path ),
			)
		);

		$this->assertIsArray( $result );
		$this->assertSame( 'photo', $result['name'] );
		$this->assertSame( $before, $this->attachment_count() );
		$this->assertFileDoesNotExist( WP_CONTENT_DIR . '/uploads/photo.gif' );
	}

	/**
	 * A nested column field is a real upload field.
	 *
	 * @return void
	 */
	public function test_column_nested_upload_field_is_authorized() {
		$this->create_field(
			$this->form_id,
			array(
				'template'     => 'column_field',
				'name'         => 'columns',
				'inner_fields' => array(
					array(
						array(
							'template' => 'image_upload',
							'name'     => 'nested_photo',
							'max_size' => 100,
						),
					),
				),
			)
		);

		$path   = $this->temp_file( $this->gif_bytes() );
		$result = weforms_authorize_upload(
			$this->form_id,
			array( 'name' => 'nested_photo' ),
			array(
				'name'     => 'nested.gif',
				'tmp_name' => $path,
				'error'    => 0,
			)
		);

		$this->assertIsArray( $result );
	}

	/**
	 * A non-image file is judged by extension policy only.
	 *
	 * @return void
	 */
	public function test_non_image_uses_extension_policy_without_content_sniffing() {
		$path   = $this->temp_file( 'not-a-pdf' );
		$result = weforms_authorize_upload(
			$this->form_id,
			array( 'name' => 'document' ),
			array(
				'name'     => 'notes.pdf',
				'tmp_name' => $path,
				'error'    => 0,
			)
		);

		$this->assertIsArray( $result );
	}

	/**
	 * An image whose contents do not match the extension is rejected.
	 *
	 * @return void
	 */
	public function test_image_content_must_agree_with_extension() {
		$path   = $this->temp_file( 'not-an-image' );
		$before = $this->attachment_count();
		$result = weforms_authorize_upload(
			$this->form_id,
			array( 'name' => 'photo' ),
			array(
				'name'     => 'photo.jpg',
				'tmp_name' => $path,
				'error'    => 0,
			)
		);

		$this->assertWPError( $result );
		$this->assertSame( 'Upload could not be accepted.', $result->get_error_message() );
		$this->assertSame( $before, $this->attachment_count() );
	}

	/**
	 * Missing, unpublished, and ambiguous targets create nothing.
	 *
	 * @return void
	 */
	public function test_denied_requests_create_no_attachment() {
		$path   = $this->temp_file( $this->gif_bytes() );
		$file   = array(
			'name'     => 'photo.gif',
			'tmp_name' => $path,
			'error'    => 0,
		);
		$before = $this->attachment_count();

		$missing = weforms_authorize_upload( $this->form_id, array( 'name' => 'missing' ), $file );
		$this->assertWPError( $missing );

		$draft_id = $this->create_form( 'draft' );
		$this->create_field(
			$draft_id,
			array(
				'template' => 'image_upload',
				'name'     => 'photo',
				'max_size' => 100,
			)
		);
		$draft = weforms_authorize_upload( $draft_id, array( 'name' => 'photo' ), $file );
		$this->assertWPError( $draft );

		$this->create_field(
			$this->form_id,
			array(
				'template' => 'image_upload',
				'name'     => 'photo',
				'max_size' => 100,
			)
		);
		$ambiguous = weforms_authorize_upload( $this->form_id, array( 'name' => 'photo' ), $file );
		$this->assertWPError( $ambiguous );

		$php = weforms_authorize_upload(
			$this->form_id,
			array( 'name' => 'document' ),
			array(
				'name'     => 'shell.php',
				'tmp_name' => $path,
				'error'    => 0,
			)
		);
		$this->assertWPError( $php );

		$this->assertSame( $before, $this->attachment_count() );
	}

	/**
	 * A field without `max_size` accepts files up to the default limit only.
	 *
	 * @since 1.6.30
	 *
	 * @return void
	 */
	public function test_missing_max_size_is_capped_at_default() {
		$this->create_field(
			$this->form_id,
			array(
				'template'  => 'file_upload',
				'name'      => 'nolimit',
				'extension' => array( 'pdf' ),
			)
		);

		$limit  = weforms_upload_max_bytes( array( 'template' => 'file_upload' ) );
		$no_cap = static function () {
			return 100 * MB_IN_BYTES;
		};

		add_filter( 'upload_size_limit', $no_cap );

		$at_limit = weforms_authorize_upload(
			$this->form_id,
			array( 'name' => 'nolimit' ),
			array(
				'name'     => 'notes.pdf',
				'tmp_name' => $this->temp_file( str_repeat( 'a', $limit ) ),
				'error'    => 0,
			)
		);
		$over     = weforms_authorize_upload(
			$this->form_id,
			array( 'name' => 'nolimit' ),
			array(
				'name'     => 'notes.pdf',
				'tmp_name' => $this->temp_file( str_repeat( 'a', $limit + 1 ) ),
				'error'    => 0,
			)
		);

		remove_filter( 'upload_size_limit', $no_cap );

		$this->assertSame( 1024, Weforms_Upload_Controller::DEFAULT_MAX_SIZE_KB );
		$this->assertSame( 1024 * 1024, $limit );
		$this->assertSame( $limit, weforms_upload_max_bytes( array( 'max_size' => '' ) ) );
		$this->assertIsArray( $at_limit );
		$this->assertSame( 'nolimit', $at_limit['name'] );
		$this->assertWPError( $over );
		$this->assertContains( 'nolimit', weforms_wpuf_upload_allowed_field_names( array(), $this->form_id, 'nolimit' ) );
	}

	/**
	 * A non-numeric size limit fails closed.
	 *
	 * @since 1.6.30
	 *
	 * @return void
	 */
	public function test_non_numeric_max_size_fails_closed() {
		$this->create_field(
			$this->form_id,
			array(
				'template'  => 'file_upload',
				'name'      => 'badlimit',
				'max_size'  => 'abc',
				'extension' => array( 'pdf' ),
			)
		);

		$result = weforms_authorize_upload(
			$this->form_id,
			array( 'name' => 'badlimit' ),
			array(
				'name'     => 'notes.pdf',
				'tmp_name' => $this->temp_file( 'a' ),
				'error'    => 0,
			)
		);

		$this->assertSame( 0, weforms_upload_max_bytes( array( 'max_size' => 'abc' ) ) );
		$this->assertWPError( $result );
	}

	/**
	 * A zero size limit fails closed.
	 *
	 * @return void
	 */
	public function test_non_positive_max_size_fails_closed() {
		$this->create_field(
			$this->form_id,
			array(
				'template' => 'image_upload',
				'name'     => 'unlimited',
				'max_size' => 0,
			)
		);

		$result = weforms_authorize_upload(
			$this->form_id,
			array( 'name' => 'unlimited' ),
			array(
				'name'     => 'photo.gif',
				'tmp_name' => $this->temp_file( $this->gif_bytes() ),
				'error'    => 0,
			)
		);

		$this->assertWPError( $result );
	}

	/**
	 * The upload prefilter reports a generic error and does not store the file.
	 *
	 * @return void
	 */
	public function test_prefilter_rejects_a_weforms_upload_before_storage() {
		$uploader = new WeForms_Ajax_Upload();
		$path     = $this->temp_file( '<?php echo 1;' );
		$before   = $this->attachment_count();
		$_POST['form_id']  = $this->form_id;
		$_REQUEST['type']  = 'photo';
		$_REQUEST['nonce'] = wp_create_nonce( 'wpuf-upload-nonce' );

		$filtered = $uploader->reject_unverified_weforms_upload(
			array(
				'name'     => 'shell.php',
				'tmp_name' => $path,
				'error'    => 0,
				'size'     => filesize( $path ),
			)
		);

		unset( $_POST['form_id'], $_REQUEST['type'], $_REQUEST['nonce'] );

		$this->assertSame( 'Upload could not be accepted.', $filtered['error'] );
		$this->assertSame( $before, $this->attachment_count() );
	}

	/**
	 * WPUF receives only unique field names from an open weForms form.
	 *
	 * @return void
	 */
	public function test_wpuf_receives_only_verified_weforms_field_names() {
		$names = weforms_wpuf_upload_allowed_field_names( array( 'foreign' ), $this->form_id, 'photo' );

		$this->assertContains( 'photo', $names );
		$this->assertContains( 'document', $names );
		$this->assertNotContains( 'foreign', $names );

		$draft_id = $this->create_form( 'draft' );
		$this->assertSame( array(), weforms_wpuf_upload_allowed_field_names( array( 'photo' ), $draft_id, 'photo' ) );

		$other = self::factory()->post->create(
			array(
				'post_type'   => 'post',
				'post_status' => 'publish',
			)
		);
		$this->assertSame( array( 'kept' ), weforms_wpuf_upload_allowed_field_names( array( 'kept' ), $other, 'kept' ) );
		$this->assertFalse( weforms_wpuf_upload_allow_insert_image( true, $this->form_id ) );
		$this->assertTrue( weforms_wpuf_upload_allow_insert_image( true, $other ) );
	}

	/**
	 * REST validation fails closed and does not create media.
	 *
	 * @return void
	 */
	public function test_rest_validation_fails_closed() {
		$before  = $this->attachment_count();
		$request = new WP_REST_Request( 'POST', '/weforms/v1/uploads/' . $this->form_id . '/files' );
		$request->set_param( 'form_id', $this->form_id );
		$request->set_param( 'field_id', 0 );
		$request->set_file_params(
			array(
				'file' => array(
					'name'     => 'shell.php',
					'tmp_name' => $this->temp_file( '<?php' ),
					'error'    => 0,
					'size'     => 5,
				),
			)
		);

		do_action( 'rest_api_init' );
		$controller = new Weforms_Upload_Controller();
		$result     = $controller->is_file_validate( $request );

		$this->assertWPError( $result );
		$this->assertSame( 'Upload could not be accepted.', $result->get_error_message() );
		$this->assertSame( $before, $this->attachment_count() );
	}

	/**
	 * A published form with no stored settings rejects uploads instead of throwing.
	 *
	 * @return void
	 */
	public function test_form_without_settings_meta_fails_closed() {
		$form_id = self::factory()->post->create(
			array(
				'post_type'   => 'wpuf_contact_form',
				'post_status' => 'publish',
				'post_title'  => 'No settings',
			)
		);
		$field_id = $this->create_field(
			$form_id,
			array(
				'template' => 'image_upload',
				'name'     => 'photo',
				'max_size' => 100,
			)
		);
		$before   = $this->attachment_count();
		$path     = $this->temp_file( $this->gif_bytes() );
		$file     = array(
			'name'     => 'photo.gif',
			'tmp_name' => $path,
			'error'    => 0,
			'size'     => filesize( $path ),
		);

		$this->assertSame( '', get_post_meta( $form_id, 'wpuf_form_settings', true ) );
		$this->assertFalse( weforms_upload_form_is_open( $form_id ) );

		$result = weforms_authorize_upload( $form_id, array( 'name' => 'photo' ), $file );

		$this->assertWPError( $result );
		$this->assertSame( 'Upload could not be accepted.', $result->get_error_message() );
		$this->assertSame( array(), weforms_wpuf_upload_allowed_field_names( array( 'photo' ), $form_id, 'photo' ) );

		$request = new WP_REST_Request( 'POST', '/weforms/v1/uploads/' . $form_id . '/files' );
		$request->set_param( 'form_id', $form_id );
		$request->set_param( 'field_id', $field_id );
		$request->set_file_params( array( 'file' => $file ) );

		wp_set_current_user( self::factory()->user->create( array( 'role' => 'subscriber' ) ) );

		$controller = new Weforms_Upload_Controller();
		$validated  = $controller->is_file_validate( $request );
		$permission = $controller->upload_permissions_check( $request );

		$this->assertWPError( $validated );
		$this->assertSame( 'Upload could not be accepted.', $validated->get_error_message() );
		$this->assertWPError( $permission );
		$this->assertSame( 'rest_weforms_upload', $permission->get_error_code() );
		$this->assertSame( $before, $this->attachment_count() );
	}
}
