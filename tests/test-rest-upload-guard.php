<?php
/**
 * REST upload route must create a new file and must not edit media.
 *
 * @package weForms
 */

/**
 * Subscriber uploads cannot overwrite or delete another user's attachment.
 */
class WeForms_Rest_Upload_Guard_Test extends WP_UnitTestCase {

	/**
	 * Subscriber user ID.
	 *
	 * @var int
	 */
	private $subscriber_id;

	/**
	 * Administrator user ID.
	 *
	 * @var int
	 */
	private $admin_id;

	/**
	 * Temp files created for requests that never reach wp_handle_upload.
	 *
	 * @var string[]
	 */
	private $temp_files = array();

	/**
	 * Install tables and users.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		$_SERVER['HTTP_USER_AGENT'] = 'Firefox/120.0';

		/**
		 * PHPUnit cannot produce a real HTTP upload, so allow the local test
		 * file through is_uploaded_file(). Field, type, and size checks still run.
		 */
		add_filter( 'weforms_upload_accept_local_file', '__return_true' );

		weforms()->wpdb_table_shortcuts();
		weforms()->fields->get_fields();

		require_once WEFORMS_INCLUDES . '/class-installer.php';

		$installer = new WeForms_Installer();
		$installer->create_tables();

		$this->subscriber_id = self::factory()->user->create(
			array(
				'role' => 'subscriber',
			)
		);
		$this->admin_id      = self::factory()->user->create(
			array(
				'role' => 'administrator',
			)
		);

		rest_get_server();
	}

	/**
	 * Remove temp files left behind by rejected requests.
	 *
	 * @return void
	 */
	public function tear_down() {
		foreach ( $this->temp_files as $path ) {
			if ( is_string( $path ) && file_exists( $path ) ) {
				unlink( $path );
			}
		}

		$this->temp_files = array();

		remove_filter( 'weforms_upload_accept_local_file', '__return_true' );

		parent::tear_down();
	}

	/**
	 * Body keys that must not turn a create request into an update.
	 *
	 * @return array
	 */
	public function provide_attachment_update_params() {
		return array(
			'id'     => array( array( 'id' ) ),
			'post'   => array( array( 'post' ) ),
			'author' => array( array( 'author' ) ),
			'status' => array( array( 'status' ) ),
			'all'    => array( array( 'id', 'post', 'author', 'status' ) ),
		);
	}

	/**
	 * A request carrying attachment-update parameters is rejected.
	 *
	 * @dataProvider provide_attachment_update_params
	 *
	 * @param string[] $keys Parameter names to send.
	 *
	 * @return void
	 */
	public function test_attachment_update_params_are_rejected( $keys ) {
		list( $form_id, $field_id ) = $this->create_upload_field( 'image_upload' );
		$attachment_id              = $this->create_media( $this->admin_id );
		$page_id                    = self::factory()->post->create(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_author' => $this->admin_id,
			)
		);
		$before                     = $this->attachment_snapshot( $attachment_id );
		$count                      = $this->attachment_count();

		wp_set_current_user( $this->subscriber_id );

		$values = array(
			'id'     => $attachment_id,
			'post'   => $page_id,
			'author' => $this->admin_id,
			'status' => 'publish',
		);
		$extra  = array();

		foreach ( $keys as $key ) {
			$extra[ $key ] = $values[ $key ];
		}

		$request    = $this->upload_request( $form_id, $field_id, $extra );
		$controller = new Weforms_Upload_Controller();
		$permission = $controller->upload_permissions_check( $request );

		$this->assertWPError( $permission );

		$response = rest_do_request( $request );

		$this->assertTrue( $response->is_error() );
		$this->assertSame( $count, $this->attachment_count() );
		$this->assertSame( $before, $this->attachment_snapshot( $attachment_id ) );
		$this->assertFalse( current_user_can( 'edit_post', $attachment_id ) );
	}

	/**
	 * An unknown field ID is rejected and does not skip type checks.
	 *
	 * @return void
	 */
	public function test_unknown_field_id_is_rejected() {
		list( $form_id, $field_id ) = $this->create_upload_field( 'image_upload' );
		$text_id                    = self::factory()->post->create(
			array(
				'post_type'    => 'wpuf_input',
				'post_status'  => 'publish',
				'post_parent'  => $form_id,
				'post_content' => maybe_serialize(
					array(
						'template' => 'text_field',
						'name'     => 'subject',
						'label'    => 'Subject',
					)
				),
			)
		);
		$draft_id                   = self::factory()->post->create(
			array(
				'post_type'    => 'wpuf_input',
				'post_status'  => 'draft',
				'post_parent'  => $form_id,
				'post_content' => maybe_serialize(
					array(
						'template' => 'image_upload',
						'name'     => 'hidden_photo',
						'label'    => 'Hidden',
						'max_size' => '1024',
					)
				),
			)
		);
		$attachment_id              = $this->create_media( $this->admin_id );
		$before                     = $this->attachment_snapshot( $attachment_id );
		$count                      = $this->attachment_count();

		wp_set_current_user( $this->subscriber_id );

		$controller = new Weforms_Upload_Controller();

		foreach ( array( $text_id, $draft_id, 99999999 ) as $unknown_id ) {
			$request    = $this->upload_request( $form_id, $unknown_id );
			$permission = $controller->upload_permissions_check( $request );

			$this->assertWPError( $permission );

			$response = rest_do_request( $request );

			$this->assertTrue( $response->is_error() );
		}

		$php_request = $this->upload_request( $form_id, $field_id );
		$php_request->set_file_params(
			array(
				'file' => $this->png_upload( 'payload.php' ),
			)
		);

		$this->assertWPError( $controller->is_file_validate( $php_request ) );
		$this->assertTrue( rest_do_request( $php_request )->is_error() );

		$oversized = $this->upload_request( $form_id, $field_id );
		$path      = $oversized->get_file_params()['file']['tmp_name'];

		file_put_contents( $path, str_repeat( 'a', 2048 ) );
		clearstatcache( true, $path );
		$oversized->set_file_params(
			array(
				'file' => array(
					'name'     => 'photo.png',
					'type'     => 'image/png',
					'tmp_name' => $path,
					'error'    => 0,
					'size'     => 10,
				),
			)
		);

		$field = get_post( $field_id );

		wp_update_post(
			array(
				'ID'           => $field_id,
				'post_content' => maybe_serialize(
					array(
						'template' => 'image_upload',
						'name'     => 'photo',
						'label'    => 'Photo',
						'max_size' => '1',
					)
				),
			)
		);
		unset( $field );

		$this->assertWPError( $controller->is_file_validate( $oversized ) );
		$this->assertTrue( rest_do_request( $oversized )->is_error() );
		$this->assertSame( $count, $this->attachment_count() );
		$this->assertSame( $before, $this->attachment_snapshot( $attachment_id ) );
	}

	/**
	 * A real image field creates a new attachment.
	 *
	 * @return void
	 */
	public function test_image_upload_creates_new_attachment() {
		list( $form_id, $field_id ) = $this->create_upload_field( 'image_upload' );

		$this->assert_new_upload( $form_id, $field_id );
	}

	/**
	 * An image field with no size limit uses the field default and still uploads.
	 *
	 * @return void
	 */
	public function test_image_upload_without_max_size_creates_new_attachment() {
		$form_id  = $this->create_form();
		$field_id = self::factory()->post->create(
			array(
				'post_type'    => 'wpuf_input',
				'post_status'  => 'publish',
				'post_parent'  => $form_id,
				'post_content' => maybe_serialize(
					array(
						'template' => 'image_upload',
						'name'     => 'photo',
						'label'    => 'Photo',
					)
				),
			)
		);

		$this->assert_new_upload( $form_id, $field_id );
	}

	/**
	 * A field with no size limit still caps uploads at the default limit.
	 *
	 * @return void
	 */
	public function test_upload_without_max_size_is_capped_at_default() {
		$form_id  = $this->create_form();
		$field_id = self::factory()->post->create(
			array(
				'post_type'    => 'wpuf_input',
				'post_status'  => 'publish',
				'post_parent'  => $form_id,
				'post_content' => maybe_serialize(
					array(
						'template' => 'image_upload',
						'name'     => 'photo',
						'label'    => 'Photo',
					)
				),
			)
		);
		$count    = $this->attachment_count();
		$no_cap   = static function () {
			return 100 * MB_IN_BYTES;
		};

		add_filter( 'upload_size_limit', $no_cap );

		wp_set_current_user( $this->subscriber_id );

		$request = $this->upload_request( $form_id, $field_id );
		$path    = $request->get_file_params()['file']['tmp_name'];

		file_put_contents( $path, str_repeat( 'a', Weforms_Upload_Controller::DEFAULT_MAX_SIZE_KB * 1024 + 1 ) );
		clearstatcache( true, $path );

		$controller = new Weforms_Upload_Controller();
		$result     = $controller->is_file_validate( $request );
		$response   = rest_do_request( $request );

		remove_filter( 'upload_size_limit', $no_cap );

		$this->assertWPError( $result );
		$this->assertSame( 'rest_weforms_upload', $result->get_error_code() );
		$this->assertTrue( $response->is_error() );
		$this->assertSame( $count, $this->attachment_count() );
	}

	/**
	 * A real file field creates a new attachment.
	 *
	 * @return void
	 */
	public function test_file_upload_creates_new_attachment() {
		list( $form_id, $field_id ) = $this->create_upload_field( 'file_upload' );

		$this->assert_new_upload( $form_id, $field_id );
	}

	/**
	 * A file field may store its extension category as a string.
	 *
	 * @return void
	 */
	public function test_file_upload_string_extension_creates_new_attachment() {
		$form_id  = $this->create_form();
		$field_id = self::factory()->post->create(
			array(
				'post_type'    => 'wpuf_input',
				'post_status'  => 'publish',
				'post_parent'  => $form_id,
				'post_content' => maybe_serialize(
					array(
						'template'  => 'file_upload',
						'name'      => 'photo',
						'label'     => 'Photo',
						'max_size'  => '1024',
						'extension' => 'images',
					)
				),
			)
		);

		$this->assert_new_upload( $form_id, $field_id );
	}

	/**
	 * An image field nested in a column still creates a new attachment.
	 *
	 * @return void
	 */
	public function test_nested_image_upload_creates_new_attachment() {
		$form_id  = $this->create_form();
		$field_id = 900000001;

		self::factory()->post->create(
			array(
				'post_type'    => 'wpuf_input',
				'post_status'  => 'publish',
				'post_parent'  => $form_id,
				'post_content' => maybe_serialize(
					array(
						'template'     => 'column_field',
						'name'         => 'cols',
						'inner_fields' => array(
							array(
								array(
									'template' => 'image_upload',
									'id'       => $field_id,
									'name'     => 'photo',
									'label'    => 'Photo',
									'max_size' => '1024',
								),
							),
						),
					)
				),
			)
		);

		$this->assert_new_upload( $form_id, $field_id );
	}

	/**
	 * A subscriber who cannot delete_post is rejected by the permission callback.
	 *
	 * @return void
	 */
	public function test_delete_without_delete_post_is_rejected() {
		$form_id       = $this->create_form();
		$attachment_id = $this->create_media( $this->admin_id );

		update_post_meta( $attachment_id, 'attachment_form_id', $form_id );

		wp_set_current_user( $this->subscriber_id );

		$this->assertFalse( current_user_can( 'delete_post', $attachment_id ) );

		$request    = new WP_REST_Request( 'DELETE', '/weforms/v1/uploads/' . $form_id . '/files/' . $attachment_id );
		$request->set_param( 'form_id', $form_id );
		$request->set_param( 'id', $attachment_id );
		$request->set_param( 'force', true );

		$controller = new Weforms_Upload_Controller();
		$permission = $controller->delete_permissions_check( $request );

		$this->assertWPError( $permission );
		$this->assertSame( 403, $permission->get_error_data()['status'] );

		$response = rest_do_request( $request );

		$this->assertTrue( $response->is_error() );
		$this->assertSame( 403, $response->get_status() );
		$this->assertInstanceOf( 'WP_Post', get_post( $attachment_id ) );
		$this->assertSame( 'Original title', get_post( $attachment_id )->post_title );
	}

	/**
	 * A user who can delete_post is allowed by the same callback.
	 *
	 * @return void
	 */
	public function test_delete_with_delete_post_is_allowed() {
		$form_id       = $this->create_form();
		$attachment_id = $this->create_media( $this->admin_id );

		update_post_meta( $attachment_id, 'attachment_form_id', $form_id );

		wp_set_current_user( $this->admin_id );

		$this->assertTrue( current_user_can( 'delete_post', $attachment_id ) );

		$request    = new WP_REST_Request( 'DELETE', '/weforms/v1/uploads/' . $form_id . '/files/' . $attachment_id );
		$request->set_param( 'form_id', $form_id );
		$request->set_param( 'id', $attachment_id );
		$request->set_param( 'force', true );

		$controller = new Weforms_Upload_Controller();

		$this->assertTrue( $controller->delete_permissions_check( $request ) );

		$response = rest_do_request( $request );

		$this->assertFalse( $response->is_error() );
		$this->assertNull( get_post( $attachment_id ) );
	}

	/**
	 * The attachment image URL is escaped at the HTML sink.
	 *
	 * @return void
	 */
	public function test_attach_html_escapes_image_url() {
		$attachment_id = $this->create_media( $this->admin_id );

		add_filter(
			'wp_get_attachment_image_src',
			static function () {
				return array( 'javascript:alert(1)', 1, 1, false );
			}
		);

		$html = Weforms_Upload_Controller::attach_html( $attachment_id );

		$this->assertIsString( $html );
		$this->assertStringNotContainsString( 'javascript:', $html );
	}

	/**
	 * Shared success assertion for an open form upload.
	 *
	 * @param int $form_id  Form ID.
	 * @param int $field_id Field ID.
	 *
	 * @return void
	 */
	private function assert_new_upload( $form_id, $field_id ) {
		$attachment_id = $this->create_media( $this->admin_id );
		$before        = $this->attachment_snapshot( $attachment_id );
		$count         = $this->attachment_count();

		wp_set_current_user( $this->subscriber_id );

		$this->assertTrue( current_user_can( 'read' ) );
		$this->assertFalse( current_user_can( 'upload_files' ) );
		$this->assertFalse( current_user_can( 'edit_post', $attachment_id ) );

		$request  = $this->upload_request( $form_id, $field_id );
		$response = rest_do_request( $request );

		$this->assertFalse( $response->is_error() );

		$data = $response->get_data();

		$this->assertArrayHasKey( 'attach_id', $data );

		$new_id = (int) $data['attach_id'];

		$this->assertGreaterThan( 0, $new_id );
		$this->assertNotSame( $attachment_id, $new_id );
		$this->assertSame( $count + 1, $this->attachment_count() );
		$this->assertSame( $before, $this->attachment_snapshot( $attachment_id ) );

		$created = get_post( $new_id );

		$this->assertInstanceOf( 'WP_Post', $created );
		$this->assertSame( 'attachment', $created->post_type );
		$this->assertSame( 'private', $created->post_status );
		$this->assertTrue( WeForms_Upload_Protection::is_protected( $new_id ) );
		$this->assertStringStartsWith( WeForms_Upload_Protection::get_base_dir() . '/', get_attached_file( $new_id ) );
		$this->assertSame( 0, (int) $created->post_parent );
		$this->assertSame( $this->subscriber_id, (int) $created->post_author );
		$this->assertSame( (string) $form_id, (string) get_post_meta( $new_id, 'attachment_form_id', true ) );
		$this->assertNotSame( '', (string) get_post_meta( $new_id, '_weforms_upload_key', true ) );
		$this->assertNotSame( '', (string) get_post_meta( $new_id, '_wpuf_delete_token', true ) );
	}

	/**
	 * Create a published contact form.
	 *
	 * @return int
	 */
	private function create_form() {
		$form_id = self::factory()->post->create(
			array(
				'post_type'   => 'wpuf_contact_form',
				'post_status' => 'publish',
				'post_title'  => 'Upload form',
			)
		);

		update_post_meta( $form_id, 'wpuf_form_settings', array() );

		return $form_id;
	}

	/**
	 * Create an open form and one published upload field.
	 *
	 * @param string $template image_upload or file_upload.
	 *
	 * @return int[] Form ID and field ID.
	 */
	private function create_upload_field( $template ) {
		$form_id = $this->create_form();
		$content = array(
			'template' => $template,
			'name'     => 'photo',
			'label'    => 'Photo',
			'max_size' => '1024',
		);

		if ( 'file_upload' === $template ) {
			$content['extension'] = array( 'images' );
		}

		$field_id = self::factory()->post->create(
			array(
				'post_type'    => 'wpuf_input',
				'post_status'  => 'publish',
				'post_parent'  => $form_id,
				'post_content' => maybe_serialize( $content ),
			)
		);

		return array( $form_id, $field_id );
	}

	/**
	 * Create an administrator-owned attachment.
	 *
	 * @param int $author_id Author.
	 *
	 * @return int
	 */
	private function create_media( $author_id ) {
		$upload = wp_upload_bits( 'original.jpg', null, 'weforms-original' );

		$this->assertFalse( $upload['error'] );

		$attachment_id = self::factory()->attachment->create_object(
			$upload['file'],
			0,
			array(
				'post_mime_type' => 'image/jpeg',
				'post_title'     => 'Original title',
				'post_content'   => 'Original description',
				'post_excerpt'   => 'Original caption',
				'post_author'    => $author_id,
			)
		);

		wp_update_post(
			array(
				'ID'           => $attachment_id,
				'post_author'  => $author_id,
				'post_title'   => 'Original title',
				'post_content' => 'Original description',
				'post_excerpt' => 'Original caption',
			)
		);
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', 'Original alt' );

		return $attachment_id;
	}

	/**
	 * Values an overwrite would change.
	 *
	 * @param int $attachment_id Attachment ID.
	 *
	 * @return array
	 */
	private function attachment_snapshot( $attachment_id ) {
		$post = get_post( $attachment_id );

		return array(
			'title'   => $post->post_title,
			'content' => $post->post_content,
			'excerpt' => $post->post_excerpt,
			'author'  => (int) $post->post_author,
			'status'  => $post->post_status,
			'parent'  => (int) $post->post_parent,
			'file'    => get_attached_file( $attachment_id ),
			'alt'     => (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
		);
	}

	/**
	 * Count attachments, including inherited ones.
	 *
	 * @return int
	 */
	private function attachment_count() {
		$ids = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		return count( $ids );
	}

	/**
	 * Build a create-upload request.
	 *
	 * @param int   $form_id  Form ID.
	 * @param int   $field_id Field ID.
	 * @param array $extra    Extra body parameters.
	 *
	 * @return WP_REST_Request
	 */
	private function upload_request( $form_id, $field_id, $extra = array() ) {
		$request = new WP_REST_Request( 'POST', '/weforms/v1/uploads/' . $form_id . '/files' );

		$request->set_query_params(
			array(
				'form_id'  => $form_id,
				'field_id' => $field_id,
			)
		);
		$request->set_file_params(
			array(
				'file' => $this->png_upload( 'photo.png' ),
			)
		);

		if ( $extra ) {
			$request->set_body_params( $extra );
		}

		return $request;
	}

	/**
	 * A readable PNG the REST uploader can store during tests.
	 *
	 * @param string $name Client filename.
	 *
	 * @return array
	 */
	private function png_upload( $name ) {
		$path = wp_tempnam( $name );
		$png  = base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==' );

		file_put_contents( $path, $png );
		$this->temp_files[] = $path;

		return array(
			'name'     => $name,
			'type'     => 'image/png',
			'tmp_name' => $path,
			'error'    => 0,
			'size'     => filesize( $path ),
		);
	}
}
