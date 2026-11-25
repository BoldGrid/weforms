<?php
/**
 * Entry attachment ownership.
 *
 * @package weForms
 */

/**
 * A guest-supplied media ID must survive permanent entry delete.
 */
class WeForms_Entry_Attachment_Ownership_Test extends WP_UnitTestCase {

	/**
	 * Form ID.
	 *
	 * @var int
	 */
	private $form_id;

	/**
	 * Install tables and a form with an image upload field.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		$_SERVER['HTTP_USER_AGENT'] = 'Firefox/120.0';

		weforms()->wpdb_table_shortcuts();
		weforms()->fields->get_fields();

		require_once WEFORMS_INCLUDES . '/class-installer.php';

		$installer = new WeForms_Installer();
		$installer->create_tables();

		$this->form_id = self::factory()->post->create(
			array(
				'post_type'   => 'wpuf_contact_form',
				'post_status' => 'publish',
				'post_title'  => 'Photo form',
			)
		);

		self::factory()->post->create(
			array(
				'post_type'    => 'wpuf_input',
				'post_status'  => 'publish',
				'post_parent'  => $this->form_id,
				'post_content' => maybe_serialize(
					array(
						'template' => 'image_upload',
						'name'     => 'photo',
						'label'    => 'Photo',
					)
				),
			)
		);
	}

	/**
	 * Create an attachment post and file.
	 *
	 * @param string $filename File name.
	 *
	 * @return int
	 */
	private function create_attachment( $filename ) {
		$upload = wp_upload_bits( $filename, null, 'weforms-test' );

		$this->assertFalse( $upload['error'] );

		return self::factory()->attachment->create_object(
			$upload['file'],
			0,
			array(
				'post_mime_type' => 'image/jpeg',
				'post_title'     => $filename,
			)
		);
	}

	/**
	 * Guest submission of another user's attachment ID does not delete that file.
	 *
	 * @return void
	 */
	public function test_guest_foreign_attachment_survives_permanent_delete() {
		wp_set_current_user( 0 );

		$foreign_id = $this->create_attachment( 'precious-logo.jpg' );
		$nonce      = wp_create_nonce( 'wpuf_form_add' );

		$_POST = array(
			'_wpnonce'   => $nonce,
			'wpuf_files' => array(
				'photo' => array( (string) $foreign_id ),
			),
		);

		$stored = ( new WeForms_Form_Field_Image() )->prepare_entry(
			array(
				'name' => 'photo',
			)
		);

		$this->assertSame( array(), $stored );

		$entry_id = weforms_insert_entry(
			array(
				'form_id' => $this->form_id,
			),
			array(
				'photo' => array( $foreign_id ),
			)
		);

		$this->assertIsInt( $entry_id );
		$this->assertNotContains( $foreign_id, array_map( 'absint', (array) weforms_get_entry_meta( $entry_id, 'photo', true ) ) );
		$this->assertNotEmpty( get_post( $foreign_id ) );

		weforms_delete_entry( $entry_id );

		$this->assertNotEmpty( get_post( $foreign_id ) );
		$this->assertEmpty( get_post_meta( $foreign_id, '_weforms_entry_id', true ) );
	}

	/**
	 * An upload from the current session is stored, claimed, and removed with the entry.
	 *
	 * @return void
	 */
	public function test_session_upload_is_deleted_with_its_entry() {
		wp_set_current_user( 0 );

		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );

		$own_id     = $this->create_attachment( 'guest-photo.jpg' );
		$foreign_id = $this->create_attachment( 'other-logo.jpg' );

		weforms_register_uploaded_attachment( $own_id );

		$_POST = array(
			'_wpnonce'   => wp_create_nonce( 'wpuf_form_add' ),
			'wpuf_files' => array(
				'photo' => array( (string) $own_id, (string) $foreign_id ),
			),
		);

		$stored = ( new WeForms_Form_Field_Image() )->prepare_entry(
			array(
				'name' => 'photo',
			)
		);

		$this->assertSame( array( $own_id ), $stored );

		$entry_id = weforms_insert_entry(
			array(
				'form_id' => $this->form_id,
			),
			array(
				'photo' => $stored,
			)
		);

		$this->assertSame( (string) $entry_id, (string) get_post_meta( $own_id, '_weforms_entry_id', true ) );

		weforms_delete_entry( $entry_id );

		$this->assertEmpty( get_post( $own_id ) );
		$this->assertNotEmpty( get_post( $foreign_id ) );
	}

	/**
	 * A different session cannot reuse an upload it did not create.
	 *
	 * @return void
	 */
	public function test_other_session_cannot_submit_an_existing_upload() {
		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );

		$own_id = $this->create_attachment( 'session-a.jpg' );
		weforms_register_uploaded_attachment( $own_id );

		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );

		$this->assertSame( array(), weforms_filter_submission_attachment_ids( array( $own_id ) ) );
	}

	/**
	 * A file-upload value is stored only when it belongs to this submission.
	 *
	 * @return void
	 */
	public function test_file_upload_field_drops_foreign_attachment_ids() {
		self::factory()->post->create(
			array(
				'post_type'    => 'wpuf_input',
				'post_status'  => 'publish',
				'post_parent'  => $this->form_id,
				'post_content' => maybe_serialize(
					array(
						'template' => 'file_upload',
						'name'     => 'resume',
						'label'    => 'Resume',
					)
				),
			)
		);

		$foreign_id = $this->create_attachment( 'foreign-resume.pdf' );

		$entry_id = weforms_insert_entry(
			array(
				'form_id' => $this->form_id,
			),
			array(
				'resume' => array( $foreign_id ),
			)
		);

		$this->assertIsInt( $entry_id );
		$this->assertNotContains( $foreign_id, array_map( 'absint', (array) weforms_get_entry_meta( $entry_id, 'resume', true ) ) );

		weforms_delete_entry( $entry_id );

		$this->assertNotEmpty( get_post( $foreign_id ) );
	}

	/**
	 * The shared upload request records the new attachment for this session.
	 *
	 * @return void
	 */
	public function test_upload_request_registers_attachment() {
		$_REQUEST['action'] = 'wpuf_upload_file';
		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );

		$attach_id = $this->create_attachment( 'from-upload-request.jpg' );
		$uploader  = new WeForms_Ajax_Upload();
		$uploader->register_attachment_from_upload_request( $attach_id );

		$this->assertSame( array( $attach_id ), weforms_filter_submission_attachment_ids( array( $attach_id ) ) );

		unset( $_REQUEST['action'] );

		$other_id = $this->create_attachment( 'not-from-upload-request.jpg' );
		$uploader->register_attachment_from_upload_request( $other_id );

		$this->assertSame( array(), weforms_filter_submission_attachment_ids( array( $other_id ) ) );
	}

	/**
	 * An upload nested in a column is claimed, and a foreign ID is not stored.
	 *
	 * @return void
	 */
	public function test_column_upload_is_claimed_and_foreign_id_is_dropped() {
		self::factory()->post->create(
			array(
				'post_type'    => 'wpuf_input',
				'post_status'  => 'publish',
				'post_parent'  => $this->form_id,
				'post_content' => maybe_serialize(
					array(
						'template'     => 'column_field',
						'name'         => 'cols',
						'label'        => 'Columns',
						'inner_fields' => array(
							'column-1' => array(
								array(
									'template' => 'image_upload',
									'name'     => 'column_photo',
									'label'    => 'Column photo',
								),
							),
						),
					)
				),
			)
		);

		$_COOKIE[ weforms_upload_session_cookie_name() ] = wp_generate_password( 32, false );

		$own_id     = $this->create_attachment( 'column-photo.jpg' );
		$foreign_id = $this->create_attachment( 'column-foreign.jpg' );

		weforms_register_uploaded_attachment( $own_id );

		$entry_id = weforms_insert_entry(
			array(
				'form_id' => $this->form_id,
			),
			array(
				'column_photo' => array( $own_id, $foreign_id ),
			)
		);

		$stored = array_map( 'absint', (array) weforms_get_entry_meta( $entry_id, 'column_photo', true ) );

		$this->assertContains( $own_id, $stored );
		$this->assertNotContains( $foreign_id, $stored );
		$this->assertSame( (string) $entry_id, (string) get_post_meta( $own_id, '_weforms_entry_id', true ) );

		weforms_delete_entry( $entry_id );

		$this->assertEmpty( get_post( $own_id ) );
		$this->assertNotEmpty( get_post( $foreign_id ) );
	}
}
