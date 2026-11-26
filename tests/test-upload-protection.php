<?php
/**
 * Form upload storage and access.
 *
 * @package weForms
 */

/**
 * Files uploaded through a form stay private.
 */
class WeForms_Upload_Protection_Test extends WP_UnitTestCase {

	/**
	 * Form ID.
	 *
	 * @var int
	 */
	private $form_id;

	/**
	 * Attachments created by a test.
	 *
	 * @var int[]
	 */
	private $attachments = array();

	/**
	 * Image upload field ID.
	 *
	 * @var int
	 */
	private $field_id;

	/**
	 * Install tables and a form with a file upload field.
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

		/**
		 * Under PHPUnit the REST media controller uploads with the
		 * wp_handle_mock_upload action, so core skips wp_handle_upload_prefilter.
		 * Run that filter here so the hooks a real request runs still apply.
		 */
		add_filter( 'wp_handle_mock_upload_prefilter', array( $this, 'run_upload_prefilter' ) );

		weforms()->wpdb_table_shortcuts();
		weforms()->fields->get_fields();

		require_once WEFORMS_INCLUDES . '/class-installer.php';

		$installer = new WeForms_Installer();
		$installer->create_tables();

		$this->form_id = self::factory()->post->create(
			array(
				'post_type'   => 'wpuf_contact_form',
				'post_status' => 'publish',
				'post_title'  => 'Application form',
			)
		);

		update_post_meta( $this->form_id, 'wpuf_form_settings', array() );

		$this->field_id = self::factory()->post->create(
			array(
				'post_type'    => 'wpuf_input',
				'post_status'  => 'publish',
				'post_parent'  => $this->form_id,
				'post_content' => maybe_serialize(
					array(
						'template' => 'image_upload',
						'name'     => 'passport',
						'label'    => 'Passport',
						'max_size' => '1024',
					)
				),
			)
		);

		rest_get_server();
	}

	/**
	 * Remove attachments and their files.
	 *
	 * @return void
	 */
	public function tear_down() {
		foreach ( $this->attachments as $id ) {
			wp_delete_attachment( $id, true );
		}

		remove_filter( 'weforms_upload_accept_local_file', '__return_true' );
		remove_filter( 'wp_handle_mock_upload_prefilter', array( $this, 'run_upload_prefilter' ) );

		$_COOKIE = array();
		wp_set_current_user( 0 );

		parent::tear_down();
	}

	/**
	 * Apply wp_handle_upload_prefilter to a mock upload.
	 *
	 * @param array $file Upload data.
	 *
	 * @return array
	 */
	public function run_upload_prefilter( $file ) {
		return apply_filters( 'wp_handle_upload_prefilter', $file );
	}

	/**
	 * Upload a JPEG as a guest through the weForms uploads REST controller.
	 *
	 * @param string $filename Client file name.
	 *
	 * @return int Attachment ID.
	 */
	private function rest_upload( $filename ) {
		$tmp = wp_tempnam( 'weforms-test' );
		copy( DIR_TESTDATA . '/images/canola.jpg', $tmp );

		$request = new WP_REST_Request( 'POST', '/weforms/v1/uploads/' . $this->form_id . '/files' );
		$request->set_param( 'form_id', $this->form_id );
		$request->set_param( 'field_id', $this->field_id );
		$request->set_file_params(
			array(
				'file' => array(
					'name'     => $filename,
					'type'     => 'image/jpeg',
					'tmp_name' => $tmp,
					'error'    => 0,
					'size'     => filesize( $tmp ),
				),
			)
		);

		$response = ( new Weforms_Upload_Controller() )->upload_file( $request );

		$this->assertNotWPError( $response );

		$data = $response->get_data();
		$id   = (int) $data['attach_id'];

		$this->assertGreaterThan( 0, $id );
		$this->attachments[] = $id;

		return $id;
	}

	/**
	 * Create a plain Media Library attachment that weForms did not upload.
	 *
	 * @return int
	 */
	private function create_media_library_attachment() {
		$upload = wp_upload_bits( 'site-logo.jpg', null, file_get_contents( DIR_TESTDATA . '/images/canola.jpg' ) );

		$this->assertFalse( $upload['error'] );

		$id = self::factory()->attachment->create_object(
			$upload['file'],
			0,
			array(
				'post_mime_type' => 'image/jpeg',
				'post_title'     => 'site-logo',
			)
		);

		$this->attachments[] = $id;

		return $id;
	}

	/**
	 * IDs listed by GET /wp/v2/media for the current user.
	 *
	 * @return int[]
	 */
	private function listed_media_ids() {
		$response = rest_do_request( new WP_REST_Request( 'GET', '/wp/v2/media' ) );

		return array_map( 'intval', wp_list_pluck( $response->get_data(), 'id' ) );
	}

	/**
	 * An administrator.
	 *
	 * @return int
	 */
	private function admin_id() {
		return self::factory()->user->create( array( 'role' => 'administrator' ) );
	}

	/**
	 * The REST path stores the file privately, under a random name, outside the dated public folder.
	 *
	 * @return void
	 */
	public function test_rest_upload_is_private_and_not_at_public_path() {
		wp_set_current_user( 0 );
		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );

		$id   = $this->rest_upload( 'passport-scan.jpg' );
		$post = get_post( $id );
		$file = get_attached_file( $id );
		$base = WeForms_Upload_Protection::get_base_dir();
		$dirs = wp_upload_dir();

		$this->assertSame( 'private', $post->post_status );
		$this->assertSame( 0, (int) $post->post_parent );
		$this->assertSame( 'passport-scan', $post->post_title );
		$this->assertTrue( WeForms_Upload_Protection::is_protected( $id ) );

		$this->assertStringStartsWith( $base . '/', $file );
		$this->assertFileExists( $file );
		$this->assertMatchesRegularExpression( '/^[A-Za-z0-9]{32}\.jpg$/', wp_basename( $file ) );
		$this->assertFileDoesNotExist( $dirs['path'] . '/passport-scan.jpg' );
		$this->assertStringNotContainsString( '/weforms-uploads', $dirs['path'] );

		$this->assertFileExists( $base . '/.htaccess' );
		$this->assertStringContainsString( 'Require all denied', file_get_contents( $base . '/.htaccess' ) );
		$this->assertFileExists( $base . '/index.php' );

		$url = wp_get_attachment_url( $id );

		$this->assertStringContainsString( 'admin-ajax.php', $url );
		$this->assertStringContainsString( 'action=' . WeForms_Upload_Protection::ACTION, $url );
		$this->assertStringNotContainsString( wp_basename( $file ), $url );
	}

	/**
	 * Anonymous media REST requests cannot list or read a form upload.
	 *
	 * @return void
	 */
	public function test_anonymous_media_rest_cannot_list_or_read_form_upload() {
		wp_set_current_user( 0 );
		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );

		$upload_id = $this->rest_upload( 'cv.jpg' );
		$public_id = $this->create_media_library_attachment();

		$_COOKIE = array();

		$listed = $this->listed_media_ids();

		$this->assertNotContains( $upload_id, $listed );
		$this->assertContains( $public_id, $listed );

		$single = rest_do_request( new WP_REST_Request( 'GET', '/wp/v2/media/' . $upload_id ) );

		$this->assertContains( $single->get_status(), array( 401, 403, 404 ) );

		$private = new WP_REST_Request( 'GET', '/wp/v2/media' );
		$private->set_param( 'status', 'private' );

		$this->assertContains( rest_do_request( $private )->get_status(), array( 400, 401, 403 ) );
	}

	/**
	 * The download handler denies anonymous visitors and other sessions.
	 *
	 * @return void
	 */
	public function test_anonymous_visitor_cannot_view_form_upload() {
		wp_set_current_user( 0 );
		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );

		$id = $this->rest_upload( 'id-card.jpg' );

		$this->assertTrue( WeForms_Upload_Protection::can_view( $id ), 'Uploader can preview before submit.' );

		$_COOKIE = array();
		$this->assertFalse( WeForms_Upload_Protection::can_view( $id ) );

		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );
		$this->assertFalse( WeForms_Upload_Protection::can_view( $id ) );
		$this->assertFalse( WeForms_Upload_Protection::can_view( $id, 'not-the-key' ) );

		$subscriber = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $subscriber );
		$this->assertFalse( WeForms_Upload_Protection::can_view( $id ) );
	}

	/**
	 * The download handler answers 404 to an anonymous request, even for a real file name.
	 *
	 * @return void
	 */
	public function test_download_handler_refuses_anonymous_request() {
		wp_set_current_user( 0 );
		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );

		$id = $this->rest_upload( 'tax-return.jpg' );

		$_COOKIE = array();
		$_GET    = array(
			'id'   => (string) $id,
			'file' => wp_basename( get_attached_file( $id ) ),
		);

		$die_handler = function () {
			return function ( $message, $title, $args ) {
				throw new Exception( 'wp_die:' . $args['response'] );
			};
		};

		add_filter( 'wp_die_handler', $die_handler );

		try {
			WeForms_Upload_Protection::serve();
			$this->fail( 'serve() should stop with wp_die().' );
		} catch ( Exception $e ) {
			$this->assertSame( 'wp_die:404', $e->getMessage() );
		} finally {
			remove_filter( 'wp_die_handler', $die_handler );
			$_GET = array();
		}
	}

	/**
	 * The uploader loses preview access once an entry claims the file.
	 *
	 * @return void
	 */
	public function test_uploader_preview_ends_when_entry_claims_file() {
		wp_set_current_user( 0 );
		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );

		$id = $this->rest_upload( 'photo.jpg' );

		update_post_meta( $id, '_weforms_entry_id', 99 );

		$this->assertFalse( WeForms_Upload_Protection::can_view( $id ) );
	}

	/**
	 * A user who can manage weForms entries can view and resolve the file and its sizes.
	 *
	 * @return void
	 */
	public function test_entry_manager_can_view_form_upload() {
		wp_set_current_user( 0 );
		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );

		$id = $this->rest_upload( 'resume.jpg' );

		$_COOKIE = array();
		wp_set_current_user( $this->admin_id() );

		$this->assertTrue( WeForms_Upload_Protection::can_view( $id ) );
		$this->assertSame( realpath( get_attached_file( $id ) ), WeForms_Upload_Protection::resolve_path( $id ) );
		$this->assertContains( $id, $this->listed_media_ids_with_private() );

		$meta = wp_get_attachment_metadata( $id );

		$this->assertNotEmpty( $meta['sizes']['thumbnail']['file'] );

		$thumb_file = $meta['sizes']['thumbnail']['file'];
		$src        = wp_get_attachment_image_src( $id, 'thumbnail' );

		$this->assertStringContainsString( 'file=' . rawurlencode( $thumb_file ), $src[0] );
		$this->assertStringStartsWith( WeForms_Upload_Protection::get_base_dir() . '/', WeForms_Upload_Protection::resolve_path( $id, $thumb_file ) );

		$this->assertSame( '', WeForms_Upload_Protection::resolve_path( $id, '../../../wp-config.php' ) );
		$this->assertSame( '', WeForms_Upload_Protection::resolve_path( $id, 'other.jpg' ) );
		$this->assertStringNotContainsString( 'srcset', wp_get_attachment_image( $id, 'thumbnail' ) );
	}

	/**
	 * IDs listed by GET /wp/v2/media?status=private for the current user.
	 *
	 * @return int[]
	 */
	private function listed_media_ids_with_private() {
		$request = new WP_REST_Request( 'GET', '/wp/v2/media' );
		$request->set_param( 'status', array( 'inherit', 'private' ) );

		return array_map( 'intval', wp_list_pluck( rest_do_request( $request )->get_data(), 'id' ) );
	}

	/**
	 * Notification links carry a key that opens only that file.
	 *
	 * @return void
	 */
	public function test_notification_link_is_signed_for_that_file() {
		wp_set_current_user( 0 );
		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );

		$id    = $this->rest_upload( 'scan.jpg' );
		$other = $this->rest_upload( 'other-scan.jpg' );

		$_COOKIE = array();

		$url = WeForms_Upload_Protection::get_notification_url( $id );
		parse_str( (string) wp_parse_url( $url, PHP_URL_QUERY ), $query );

		$this->assertTrue( WeForms_Upload_Protection::can_view( $id, $query['key'], $query['expires'] ) );
		$this->assertFalse( WeForms_Upload_Protection::can_view( $other, $query['key'], $query['expires'] ) );
		$this->assertFalse( WeForms_Upload_Protection::can_view( $id, $query['key'] ), 'A key without its expiry does not open the file.' );
		$this->assertFalse( WeForms_Upload_Protection::can_view( $id, $query['key'], $query['expires'] + 1 ), 'A changed expiry breaks the key.' );

		$public_id = $this->create_media_library_attachment();
		$this->assertSame( wp_get_attachment_url( $public_id ), WeForms_Upload_Protection::get_notification_url( $public_id ) );
	}

	/**
	 * A signed link stops working once it expires.
	 *
	 * @return void
	 */
	public function test_notification_link_expires() {
		wp_set_current_user( 0 );
		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );

		$id = $this->rest_upload( 'expired.jpg' );

		$_COOKIE = array();
		$expired = time() - 1;
		$key     = WeForms_Upload_Protection::sign( $id, $expired );

		$this->assertFalse( WeForms_Upload_Protection::can_view( $id, $key, $expired ) );

		$url = WeForms_Upload_Protection::get_notification_url( $id );
		parse_str( (string) wp_parse_url( $url, PHP_URL_QUERY ), $query );

		$this->assertGreaterThan( time(), (int) $query['expires'] );
		$this->assertLessThanOrEqual( time() + WeForms_Upload_Protection::SIGNED_URL_TTL, (int) $query['expires'] );
	}

	/**
	 * The {all_fields} table in a notification uses signed file and thumbnail links.
	 *
	 * @return void
	 */
	public function test_all_fields_notification_links_are_signed() {
		wp_set_current_user( 0 );
		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );

		$id = $this->rest_upload( 'all-fields.jpg' );

		$entry_id = weforms_insert_entry(
			array(
				'form_id' => $this->form_id,
			),
			array(
				'passport' => array( $id ),
			)
		);

		$_COOKIE = array();

		$notification = new WeForms_Notification(
			array(
				'form_id'  => $this->form_id,
				'entry_id' => $entry_id,
			)
		);

		$html = $notification->replace_all_fields( '{all_fields}' );

		$this->assertMatchesRegularExpression( '/<a href="[^"]*action=' . WeForms_Upload_Protection::ACTION . '[^"]*key=/', $html );
		$this->assertMatchesRegularExpression( '/<img [^>]*src="[^"]*action=' . WeForms_Upload_Protection::ACTION . '[^"]*key=/', $html );

		preg_match_all( '/(?:href|src)="([^"]+)"/', $html, $links );

		$this->assertNotEmpty( $links[1] );

		foreach ( $links[1] as $link ) {
			parse_str( (string) wp_parse_url( html_entity_decode( $link ), PHP_URL_QUERY ), $query );

			$this->assertTrue( WeForms_Upload_Protection::can_view( $id, $query['key'], $query['expires'] ) );
		}

		$this->assertStringNotContainsString( 'key=', wp_get_attachment_url( $id ), 'Signing ends after the table is built.' );
	}

	/**
	 * The Slack message, read outside WordPress, carries signed file links.
	 *
	 * @return void
	 */
	public function test_slack_notification_links_are_signed() {
		wp_set_current_user( 0 );
		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );

		$id = $this->rest_upload( 'slack.jpg' );

		$entry_id = weforms_insert_entry(
			array(
				'form_id' => $this->form_id,
			),
			array(
				'passport' => array( $id ),
			)
		);

		$_COOKIE = array();

		update_post_meta(
			$this->form_id,
			'integrations',
			array(
				'slack' => (object) array(
					'enabled' => true,
					'url'     => 'https://hooks.slack.test/services/weforms',
				),
			)
		);

		$sent    = array();
		$capture = static function ( $preempt, $args, $url ) use ( &$sent ) {
			$sent[] = $args['body']['payload'];

			return array(
				'headers'  => array(),
				'body'     => '',
				'response' => array(
					'code'    => 200,
					'message' => 'OK',
				),
				'cookies'  => array(),
			);
		};

		add_filter( 'pre_http_request', $capture, 10, 3 );
		( new WeForms_Integration_Slack() )->send_notification( $entry_id, $this->form_id, 0, array() );
		remove_filter( 'pre_http_request', $capture, 10 );

		$this->assertCount( 1, $sent );
		$this->assertStringContainsString( 'action=' . WeForms_Upload_Protection::ACTION, $sent[0] );
		$this->assertStringContainsString( 'key=', $sent[0] );
		$this->assertStringNotContainsString( 'key=', wp_get_attachment_url( $id ), 'Signing ends after the message is built.' );
	}

	/**
	 * The AJAX upload path writes into the protected directory under a random name.
	 *
	 * @return void
	 */
	public function test_ajax_upload_uses_protected_directory_and_random_name() {
		$seen = array();
		$spy  = function ( $file ) use ( &$seen ) {
			$dirs   = wp_upload_dir();
			$seen[] = array(
				'name' => $file['name'],
				'path' => $dirs['path'],
			);

			return $file;
		};

		add_filter( 'wp_handle_upload_prefilter', $spy, 99 );

		$tmp = wp_tempnam( 'weforms-test' );
		copy( DIR_TESTDATA . '/images/canola.jpg', $tmp );

		( new WeForms_Ajax_Upload() )->handle_upload(
			array(
				'name'     => 'driver-licence.jpg',
				'type'     => 'image/jpeg',
				'tmp_name' => $tmp,
				'error'    => 0,
				'size'     => filesize( $tmp ),
			)
		);

		remove_filter( 'wp_handle_upload_prefilter', $spy, 99 );
		@unlink( $tmp );

		$this->assertCount( 1, $seen );
		$this->assertMatchesRegularExpression( '/^[A-Za-z0-9]{32}\.jpg$/', $seen[0]['name'] );
		$this->assertStringStartsWith( WeForms_Upload_Protection::get_base_dir() . '/', $seen[0]['path'] );

		$dirs = wp_upload_dir();
		$this->assertStringNotContainsString( '/weforms-uploads', $dirs['path'], 'Upload dir filter is removed after the upload.' );
		$this->assertFalse( has_filter( 'wp_handle_upload_prefilter', array( 'WeForms_Upload_Protection', 'randomize_filename' ) ) );
	}

	/**
	 * Simulate the WPUF upload handler: sideload a file and insert the attachment.
	 *
	 * @param WeForms_Ajax_Upload $uploader Uploader listening for add_attachment.
	 * @param int                 $form_id  Form ID in the request.
	 *
	 * @return int Attachment ID.
	 */
	private function shared_handler_upload( $uploader, $form_id ) {
		$_REQUEST['action'] = 'wpuf_upload_file';
		$_POST['form_id']   = (string) $form_id;

		$uploader->protect_shared_upload_request();

		$tmp = wp_tempnam( 'weforms-test' );
		copy( DIR_TESTDATA . '/images/canola.jpg', $tmp );

		$upload = array(
			'name'     => 'shared-handler.jpg',
			'type'     => 'image/jpeg',
			'tmp_name' => $tmp,
			'error'    => 0,
			'size'     => filesize( $tmp ),
		);

		$file = wp_handle_sideload( $upload, array( 'test_form' => false ) );

		$this->assertArrayHasKey( 'file', $file );

		$id = wp_insert_attachment(
			array(
				'post_mime_type' => 'image/jpeg',
				'post_title'     => 'shared-handler',
				'post_status'    => 'inherit',
			),
			$file['file']
		);

		unset( $_REQUEST['action'], $_POST['form_id'] );

		$this->assertFalse( has_filter( 'upload_dir', array( 'WeForms_Upload_Protection', 'filter_upload_dir' ) ), 'Storage filters end once the attachment exists.' );
		WeForms_Upload_Protection::end();

		$this->attachments[] = $id;

		return $id;
	}

	/**
	 * When WPUF handles the shared upload action, weForms form uploads are still protected.
	 *
	 * @return void
	 */
	public function test_shared_upload_handler_is_protected_for_weforms_forms() {
		wp_set_current_user( 0 );
		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );

		$id = $this->shared_handler_upload( new WeForms_Ajax_Upload(), $this->form_id );

		$this->assertSame( 'private', get_post( $id )->post_status );
		$this->assertTrue( WeForms_Upload_Protection::is_protected( $id ) );
		$this->assertStringStartsWith( WeForms_Upload_Protection::get_base_dir() . '/', get_attached_file( $id ) );
		$this->assertNotContains( $id, $this->listed_media_ids() );
	}

	/**
	 * Storage filters end with the request action even when the shared handler creates nothing.
	 *
	 * @return void
	 */
	public function test_shared_upload_handler_ends_filters_without_attachment() {
		$uploader = new WeForms_Ajax_Upload();

		remove_all_actions( 'wp_ajax_nopriv_wpuf_upload_file' );

		$_POST['form_id'] = (string) $this->form_id;

		$uploader->protect_shared_upload_request();

		$this->assertNotFalse( has_filter( 'upload_dir', array( 'WeForms_Upload_Protection', 'filter_upload_dir' ) ) );

		do_action( 'wp_ajax_nopriv_wpuf_upload_file' );

		unset( $_POST['form_id'] );

		$this->assertFalse( has_filter( 'upload_dir', array( 'WeForms_Upload_Protection', 'filter_upload_dir' ) ) );
		$this->assertFalse( has_filter( 'wp_handle_upload_prefilter', array( 'WeForms_Upload_Protection', 'randomize_filename' ) ) );
	}

	/**
	 * Uploads for WPUF's own forms are left to WPUF.
	 *
	 * @return void
	 */
	public function test_shared_upload_handler_ignores_other_forms() {
		$wpuf_form = self::factory()->post->create(
			array(
				'post_type'   => 'wpuf_forms',
				'post_status' => 'publish',
			)
		);

		$id = $this->shared_handler_upload( new WeForms_Ajax_Upload(), $wpuf_form );

		$this->assertSame( 'inherit', get_post( $id )->post_status );
		$this->assertFalse( WeForms_Upload_Protection::is_protected( $id ) );
		$this->assertStringNotContainsString( '/weforms-uploads/', get_attached_file( $id ) );
	}

	/**
	 * Uploading through weForms does not change other uploads.
	 *
	 * @return void
	 */
	public function test_media_library_uploads_are_unchanged() {
		$id   = $this->create_media_library_attachment();
		$dirs = wp_upload_dir();

		$this->assertFalse( WeForms_Upload_Protection::is_protected( $id ) );
		$this->assertSame( 'inherit', get_post( $id )->post_status );
		$this->assertSame( $dirs['url'] . '/' . wp_basename( get_attached_file( $id ) ), wp_get_attachment_url( $id ) );
		$this->assertFalse( WeForms_Upload_Protection::can_view( $id ) );
	}

	/**
	 * Permanent entry delete still removes the protected file.
	 *
	 * @return void
	 */
	public function test_entry_delete_removes_protected_file() {
		wp_set_current_user( 0 );
		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );

		$id   = $this->rest_upload( 'delete-me.jpg' );
		$file = get_attached_file( $id );

		$entry_id = weforms_insert_entry(
			array(
				'form_id' => $this->form_id,
			),
			array(
				'passport' => array( $id ),
			)
		);

		$this->assertSame( (string) $entry_id, (string) get_post_meta( $id, '_weforms_entry_id', true ) );

		weforms_delete_entry( $entry_id );

		$this->assertEmpty( get_post( $id ) );
		$this->assertFileDoesNotExist( $file );
	}

	/**
	 * Earlier form uploads are made private once; other media is left alone.
	 *
	 * @return void
	 */
	public function test_legacy_form_uploads_are_made_private() {
		$legacy_id = $this->create_media_library_attachment();
		$public_id = $this->create_media_library_attachment();

		update_post_meta( $legacy_id, '_weforms_upload_key', wp_hash( 'old-session' ) );
		delete_option( WeForms_Upload_Protection::MIGRATED_OPTION );

		WeForms_Upload_Protection::maybe_migrate_legacy_uploads();

		$this->assertSame( 'private', get_post( $legacy_id )->post_status );
		$this->assertSame( 'inherit', get_post( $public_id )->post_status );
		$this->assertNotEmpty( get_option( WeForms_Upload_Protection::MIGRATED_OPTION ) );

		wp_set_current_user( 0 );
		$this->assertNotContains( $legacy_id, $this->listed_media_ids() );
	}

	/**
	 * The first migration pass handles a capped number of uploads per call.
	 *
	 * @return void
	 */
	public function test_legacy_first_pass_is_capped_per_call() {
		$first  = $this->create_media_library_attachment();
		$second = $this->create_media_library_attachment();

		update_post_meta( $first, 'attachment_form_id', $this->form_id );
		update_post_meta( $second, 'attachment_form_id', $this->form_id );
		delete_option( WeForms_Upload_Protection::MIGRATED_OPTION );

		WeForms_Upload_Protection::maybe_migrate_legacy_uploads( 1 );

		$this->assertSame( 'private', get_post( $first )->post_status );
		$this->assertSame( 'inherit', get_post( $second )->post_status );
		$this->assertEmpty( get_option( WeForms_Upload_Protection::MIGRATED_OPTION ) );

		WeForms_Upload_Protection::maybe_migrate_legacy_uploads( 1 );
		WeForms_Upload_Protection::maybe_migrate_legacy_uploads( 1 );

		$this->assertSame( 'private', get_post( $second )->post_status );
		$this->assertNotEmpty( get_option( WeForms_Upload_Protection::MIGRATED_OPTION ) );
	}

	/**
	 * Only an administrator's non-AJAX admin request runs the migration.
	 *
	 * @return void
	 */
	public function test_legacy_migration_needs_admin_screen_request() {
		$legacy_id = $this->create_media_library_attachment();
		$old_main  = get_attached_file( $legacy_id );

		update_post_meta( $legacy_id, '_weforms_entry_id', 12 );
		delete_option( WeForms_Upload_Protection::MIGRATED_OPTION );
		delete_option( WeForms_Upload_Protection::FILES_MIGRATED_OPTION );

		$this->assertNotFalse( has_action( 'admin_init', array( 'WeForms_Upload_Protection', 'maybe_run_legacy_migration' ) ) );
		$this->assertFalse( has_action( 'init', array( 'WeForms_Upload_Protection', 'maybe_migrate_legacy_uploads' ) ) );
		$this->assertFalse( has_action( 'admin_init', array( 'WeForms_Upload_Protection', 'migrate_legacy_files' ) ) );

		$subscriber = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		$admin      = $this->admin_id();

		foreach ( array( 0, $subscriber ) as $user_id ) {
			wp_set_current_user( $user_id );
			WeForms_Upload_Protection::maybe_run_legacy_migration();
		}

		wp_set_current_user( $admin );
		add_filter( 'wp_doing_ajax', '__return_true' );
		WeForms_Upload_Protection::maybe_run_legacy_migration();
		remove_filter( 'wp_doing_ajax', '__return_true' );

		$this->assertSame( 'inherit', get_post( $legacy_id )->post_status );
		$this->assertFileExists( $old_main );
		$this->assertFalse( WeForms_Upload_Protection::is_protected( $legacy_id ) );

		WeForms_Upload_Protection::maybe_run_legacy_migration();

		$this->assertSame( 'private', get_post( $legacy_id )->post_status );
		$this->assertFileDoesNotExist( $old_main );
		$this->assertTrue( WeForms_Upload_Protection::is_protected( $legacy_id ) );
	}

	/**
	 * Earlier files tied to an entry leave the public folder; abandoned or unrelated ones stay.
	 *
	 * @return void
	 */
	public function test_legacy_entry_files_move_into_protected_storage() {
		$legacy_id = $this->create_media_library_attachment();
		$other_id  = $this->create_media_library_attachment();
		$old_main  = get_attached_file( $legacy_id );

		wp_update_attachment_metadata( $legacy_id, wp_generate_attachment_metadata( $legacy_id, $old_main ) );

		$old_meta  = wp_get_attachment_metadata( $legacy_id );
		$old_thumb = dirname( $old_main ) . '/' . $old_meta['sizes']['thumbnail']['file'];

		$this->assertFileExists( $old_thumb );

		update_post_meta( $legacy_id, '_weforms_entry_id', 12 );
		update_post_meta( $other_id, '_weforms_upload_key', wp_hash( 'abandoned' ) );
		delete_option( WeForms_Upload_Protection::FILES_MIGRATED_OPTION );

		WeForms_Upload_Protection::migrate_legacy_files();

		$new_main = get_attached_file( $legacy_id );
		$new_meta = wp_get_attachment_metadata( $legacy_id );

		$this->assertFileDoesNotExist( $old_main );
		$this->assertFileDoesNotExist( $old_thumb );
		$this->assertStringStartsWith( WeForms_Upload_Protection::get_base_dir() . '/', $new_main );
		$this->assertFileExists( $new_main );
		$this->assertMatchesRegularExpression( '/^[A-Za-z0-9]{32}\.jpg$/', wp_basename( $new_main ) );
		$this->assertFileExists( dirname( $new_main ) . '/' . $new_meta['sizes']['thumbnail']['file'] );
		$this->assertTrue( WeForms_Upload_Protection::is_protected( $legacy_id ) );
		$this->assertSame( 'private', get_post( $legacy_id )->post_status );
		$this->assertStringContainsString( 'action=' . WeForms_Upload_Protection::ACTION, wp_get_attachment_url( $legacy_id ) );
		$this->assertNotEmpty( get_option( WeForms_Upload_Protection::FILES_MIGRATED_OPTION ) );

		$this->assertFalse( WeForms_Upload_Protection::is_protected( $other_id ) );
		$this->assertFileExists( get_attached_file( $other_id ) );
		$this->assertStringNotContainsString( '/weforms-uploads/', get_attached_file( $other_id ) );

		wp_set_current_user( $this->admin_id() );
		$this->assertNotSame( '', WeForms_Upload_Protection::resolve_path( $legacy_id, $new_meta['sizes']['thumbnail']['file'] ) );
	}

	/**
	 * One admin request keeps moving batches until none remain.
	 *
	 * @return void
	 */
	public function test_legacy_move_runs_several_batches() {
		$first  = $this->create_media_library_attachment();
		$second = $this->create_media_library_attachment();

		update_post_meta( $first, 'attachment_form_id', $this->form_id );
		update_post_meta( $second, 'attachment_form_id', $this->form_id );
		delete_option( WeForms_Upload_Protection::FILES_MIGRATED_OPTION );

		WeForms_Upload_Protection::migrate_legacy_files( 1 );

		$this->assertTrue( WeForms_Upload_Protection::is_protected( $first ) );
		$this->assertTrue( WeForms_Upload_Protection::is_protected( $second ) );
		$this->assertNotEmpty( get_option( WeForms_Upload_Protection::FILES_MIGRATED_OPTION ) );
		$this->assertFalse( get_transient( WeForms_Upload_Protection::FILES_MIGRATED_OPTION . '_lock' ) );
	}

	/**
	 * A size whose file is gone is dropped from metadata instead of pointing at a file that was never moved.
	 *
	 * @return void
	 */
	public function test_legacy_move_drops_sizes_missing_on_disk() {
		$legacy_id = $this->create_media_library_attachment();
		$old_main  = get_attached_file( $legacy_id );

		wp_update_attachment_metadata( $legacy_id, wp_generate_attachment_metadata( $legacy_id, $old_main ) );

		$old_meta = wp_get_attachment_metadata( $legacy_id );
		unlink( dirname( $old_main ) . '/' . $old_meta['sizes']['thumbnail']['file'] );

		update_post_meta( $legacy_id, 'attachment_form_id', $this->form_id );

		$this->assertTrue( WeForms_Upload_Protection::move_into_protected_storage( $legacy_id ) );

		$new_meta = wp_get_attachment_metadata( $legacy_id );
		$new_dir  = dirname( get_attached_file( $legacy_id ) );

		$this->assertArrayNotHasKey( 'thumbnail', $new_meta['sizes'] );

		foreach ( $new_meta['sizes'] as $size ) {
			$this->assertFileExists( $new_dir . '/' . $size['file'] );
		}
	}
}
