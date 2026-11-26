<?php
/**
 * Private storage for files uploaded through weForms forms.
 *
 * @package weForms
 */

/**
 * Keeps form uploads out of public media queries and public upload URLs.
 *
 * Uploads are written to a deny-all directory under a random file name and
 * saved as private attachments. Their URLs point to a download handler that
 * checks access before streaming the file.
 *
 * @since 1.6.30
 */
class WeForms_Upload_Protection {

	/**
	 * Directory under the uploads base directory.
	 *
	 * @since 1.6.30
	 *
	 * @var string
	 */
	const DIR = 'weforms-uploads';

	/**
	 * Attachment meta key marking a protected upload.
	 *
	 * @since 1.6.30
	 *
	 * @var string
	 */
	const META = '_weforms_protected_upload';

	/**
	 * AJAX action for the download handler.
	 *
	 * @since 1.6.30
	 *
	 * @var string
	 */
	const ACTION = 'weforms_view_upload';

	/**
	 * Option set once legacy uploads have been made private.
	 *
	 * @since 1.6.30
	 *
	 * @var string
	 */
	const MIGRATED_OPTION = 'weforms_private_uploads_migrated';

	/**
	 * Option set once legacy upload files have been moved.
	 *
	 * @since 1.6.30
	 *
	 * @var string
	 */
	const FILES_MIGRATED_OPTION = 'weforms_protected_upload_files_migrated';

	/**
	 * Attachment meta key for a legacy file that could not be moved.
	 *
	 * @since 1.6.30
	 *
	 * @var string
	 */
	const MOVE_FAILED_META = '_weforms_protected_move_failed';

	/**
	 * Seconds a signed no-login link stays valid (30 days).
	 *
	 * Users who can manage entries can still open the file after that.
	 *
	 * @since 1.6.30
	 *
	 * @var int
	 */
	const SIGNED_URL_TTL = 30 * DAY_IN_SECONDS;

	/**
	 * Nesting depth of begin_signed_urls() calls.
	 *
	 * @since 1.6.30
	 *
	 * @var int
	 */
	private static $signing = 0;

	/**
	 * Register hooks.
	 *
	 * @since 1.6.30
	 */
	public function __construct() {
		\add_filter( 'wp_get_attachment_url', array( __CLASS__, 'filter_attachment_url' ), 10, 2 );
		\add_filter( 'image_downsize', array( __CLASS__, 'filter_image_downsize' ), 10, 3 );
		\add_filter( 'wp_calculate_image_srcset', array( __CLASS__, 'filter_image_srcset' ), 10, 5 );
		\add_filter( 'wp_prepare_attachment_for_js', array( __CLASS__, 'filter_attachment_for_js' ), 10, 2 );
		\add_action( 'wp_ajax_' . self::ACTION, array( __CLASS__, 'serve' ) );
		\add_action( 'wp_ajax_nopriv_' . self::ACTION, array( __CLASS__, 'serve' ) );
		\add_action( 'admin_init', array( __CLASS__, 'maybe_run_legacy_migration' ), 10, 0 );
	}

	/**
	 * Run the legacy upload migration for an administrator in wp-admin.
	 *
	 * admin_init also fires on admin-ajax.php and admin-post.php, which
	 * visitors who are not logged in can reach. AJAX, cron, and users who
	 * cannot manage_options are skipped so only an administrator's screen
	 * load does the work.
	 *
	 * @since 1.6.30
	 *
	 * @return void
	 */
	public static function maybe_run_legacy_migration() {
		if ( \wp_doing_ajax() || \wp_doing_cron() || ! \current_user_can( 'manage_options' ) ) {
			return;
		}

		self::maybe_migrate_legacy_uploads();
		self::migrate_legacy_files();
	}

	/**
	 * Make attachment URLs built until end_signed_urls() signed links.
	 *
	 * Used while rendering notifications for recipients who are not logged in.
	 *
	 * @since 1.6.30
	 *
	 * @return void
	 */
	public static function begin_signed_urls() {
		++self::$signing;
	}

	/**
	 * Stop signing attachment URLs.
	 *
	 * @since 1.6.30
	 *
	 * @return void
	 */
	public static function end_signed_urls() {
		self::$signing = \max( 0, self::$signing - 1 );
	}

	/**
	 * Route uploads made until end() into the protected directory.
	 *
	 * @since 1.6.30
	 *
	 * @return void
	 */
	public static function begin() {
		\add_filter( 'upload_dir', array( __CLASS__, 'filter_upload_dir' ) );
		\add_filter( 'wp_handle_upload_prefilter', array( __CLASS__, 'randomize_filename' ) );
		\add_filter( 'wp_handle_sideload_prefilter', array( __CLASS__, 'randomize_filename' ) );

		self::ensure_directory();
	}

	/**
	 * Stop routing uploads into the protected directory.
	 *
	 * @since 1.6.30
	 *
	 * @return void
	 */
	public static function end() {
		\remove_filter( 'upload_dir', array( __CLASS__, 'filter_upload_dir' ) );
		\remove_filter( 'wp_handle_upload_prefilter', array( __CLASS__, 'randomize_filename' ) );
		\remove_filter( 'wp_handle_sideload_prefilter', array( __CLASS__, 'randomize_filename' ) );
	}

	/**
	 * Point the upload path and URL at the protected directory.
	 *
	 * The base directory is left alone so the stored relative path still
	 * resolves through get_attached_file() after the filter is removed.
	 *
	 * @since 1.6.30
	 *
	 * @param array $uploads Upload directory data.
	 *
	 * @return array
	 */
	public static function filter_upload_dir( $uploads ) {
		$subdir = '/' . self::DIR . $uploads['subdir'];

		$uploads['path']   = $uploads['basedir'] . $subdir;
		$uploads['url']    = $uploads['baseurl'] . $subdir;
		$uploads['subdir'] = $subdir;

		return $uploads;
	}

	/**
	 * Replace the client file name with a random one, keeping the extension.
	 *
	 * @since 1.6.30
	 *
	 * @param array $file Upload data from $_FILES or a sideload.
	 *
	 * @return array
	 */
	public static function randomize_filename( $file ) {
		if ( ! is_array( $file ) || empty( $file['name'] ) || ! is_string( $file['name'] ) ) {
			return $file;
		}

		$file['name'] = \wp_generate_password( 32, false, false ) . self::extension( $file['name'] );

		return $file;
	}

	/**
	 * Absolute path of the protected directory.
	 *
	 * @since 1.6.30
	 *
	 * @return string Empty string when uploads are unavailable.
	 */
	public static function get_base_dir() {
		$uploads = \wp_upload_dir( null, false );

		if ( ! empty( $uploads['error'] ) || empty( $uploads['basedir'] ) ) {
			return '';
		}

		return \trailingslashit( $uploads['basedir'] ) . self::DIR;
	}

	/**
	 * Create the protected directory and its deny rules.
	 *
	 * @since 1.6.30
	 *
	 * @return bool Whether the directory and deny rules exist.
	 */
	public static function ensure_directory() {
		$dir = self::get_base_dir();

		if ( '' === $dir || ! \wp_mkdir_p( $dir ) ) {
			return false;
		}

		$files = array(
			'.htaccess'  => "# Files here are served by weForms after an access check.\n<IfModule mod_authz_core.c>\n\tRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n\tOrder deny,allow\n\tDeny from all\n</IfModule>\n",
			'web.config' => "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<configuration>\n\t<system.webServer>\n\t\t<authorization>\n\t\t\t<deny users=\"*\" />\n\t\t</authorization>\n\t</system.webServer>\n</configuration>\n",
			'index.php'  => "<?php\n// Silence is golden.\n",
		);

		$ok = true;

		foreach ( $files as $name => $contents ) {
			$path = $dir . '/' . $name;

			if ( \file_exists( $path ) ) {
				continue;
			}

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			if ( false === \file_put_contents( $path, $contents ) ) {
				$ok = false;
			}
		}

		return $ok;
	}

	/**
	 * Make a new form upload private and mark it for the download handler.
	 *
	 * @since 1.6.30
	 *
	 * @param int    $attach_id     Attachment ID.
	 * @param string $original_name Optional client file name to use as the title.
	 *
	 * @return void
	 */
	public static function protect( $attach_id, $original_name = '' ) {
		$attach_id  = \absint( $attach_id );
		$attachment = $attach_id ? \get_post( $attach_id ) : null;

		if ( ! $attachment || 'attachment' !== $attachment->post_type ) {
			return;
		}

		$update = array();

		if ( 'private' !== $attachment->post_status || 0 !== (int) $attachment->post_parent ) {
			$update['post_status'] = 'private';
			$update['post_parent'] = 0;
		}

		$title = is_string( $original_name ) ? \sanitize_text_field( \preg_replace( '/\.[^.]+$/', '', \wp_basename( $original_name ) ) ) : '';

		if ( '' !== $title && $title !== $attachment->post_title ) {
			$update['post_title'] = $title;
		}

		if ( $update ) {
			$update['ID'] = $attach_id;
			\wp_update_post( \wp_slash( $update ) );
		}

		if ( '' !== self::resolve_path( $attach_id ) ) {
			\update_post_meta( $attach_id, self::META, 1 );
		}
	}

	/**
	 * Whether the attachment is stored in the protected directory.
	 *
	 * @since 1.6.30
	 *
	 * @param int $attach_id Attachment ID.
	 *
	 * @return bool
	 */
	public static function is_protected( $attach_id ) {
		$attach_id = \absint( $attach_id );

		return $attach_id && (bool) \get_post_meta( $attach_id, self::META, true );
	}

	/**
	 * Download handler URL for a protected upload.
	 *
	 * @since 1.6.30
	 *
	 * @param int    $attach_id Attachment ID.
	 * @param string $file      Optional image size file name.
	 * @param bool   $signed    Whether to add a key that works without login
	 *                          until SIGNED_URL_TTL passes.
	 *
	 * @return string
	 */
	public static function get_url( $attach_id, $file = '', $signed = false ) {
		$args = array(
			'action' => self::ACTION,
			'id'     => \absint( $attach_id ),
		);

		if ( '' !== $file ) {
			$args['file'] = \wp_basename( $file );
		}

		if ( $signed ) {
			$args['expires'] = \time() + self::SIGNED_URL_TTL;
			$args['key']     = self::sign( $attach_id, $args['expires'] );
		}

		return \add_query_arg( \rawurlencode_deep( $args ), \admin_url( 'admin-ajax.php' ) );
	}

	/**
	 * URL to put in a notification email.
	 *
	 * Protected uploads get a signed link so recipients who are not logged in
	 * can still open the file.
	 *
	 * @since 1.6.30
	 *
	 * @param int $attach_id Attachment ID.
	 *
	 * @return string|false
	 */
	public static function get_notification_url( $attach_id ) {
		if ( self::is_protected( $attach_id ) ) {
			return self::get_url( $attach_id, '', true );
		}

		return \wp_get_attachment_url( $attach_id );
	}

	/**
	 * Signature for a no-login link to one attachment.
	 *
	 * @since 1.6.30
	 *
	 * @param int $attach_id Attachment ID.
	 * @param int $expires   Unix time the link stops working.
	 *
	 * @return string
	 */
	public static function sign( $attach_id, $expires ) {
		return \wp_hash( self::ACTION . '|' . \absint( $attach_id ) . '|' . \absint( $expires ) );
	}

	/**
	 * Whether the current request may read a protected upload.
	 *
	 * @since 1.6.30
	 *
	 * @param int    $attach_id Attachment ID.
	 * @param string $key       Signed key from a notification link.
	 * @param int    $expires   Expiry time from the same link.
	 *
	 * @return bool
	 */
	public static function can_view( $attach_id, $key = '', $expires = 0 ) {
		$attach_id = \absint( $attach_id );

		if ( ! self::is_protected( $attach_id ) ) {
			return false;
		}

		if ( \current_user_can( weforms_form_access_capability() ) || \current_user_can( 'read_post', $attach_id ) ) {
			return true;
		}

		$expires = \absint( $expires );

		if ( is_string( $key ) && '' !== $key && $expires >= \time() && \hash_equals( self::sign( $attach_id, $expires ), $key ) ) {
			return true;
		}

		/**
		 * The visitor who uploaded the file may preview it until the entry
		 * that claims it is saved.
		 */
		if ( \absint( \get_post_meta( $attach_id, '_weforms_entry_id', true ) ) ) {
			return false;
		}

		$session = weforms_get_upload_session_key( false );
		$stored  = (string) \get_post_meta( $attach_id, '_weforms_upload_key', true );

		return $session && '' !== $stored && \hash_equals( $stored, \wp_hash( $session ) );
	}

	/**
	 * Absolute path of the original file or one of its image sizes.
	 *
	 * @since 1.6.30
	 *
	 * @param int    $attach_id Attachment ID.
	 * @param string $file      Optional image size file name.
	 *
	 * @return string Empty string when the file is missing or outside the protected directory.
	 */
	public static function resolve_path( $attach_id, $file = '' ) {
		$main = \get_attached_file( $attach_id );
		$base = self::get_base_dir();

		if ( ! $main || '' === $base ) {
			return '';
		}

		$allowed = array( \wp_basename( $main ) );
		$meta    = \wp_get_attachment_metadata( $attach_id );

		if ( is_array( $meta ) ) {
			if ( ! empty( $meta['original_image'] ) ) {
				$allowed[] = \wp_basename( $meta['original_image'] );
			}

			if ( ! empty( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
				foreach ( $meta['sizes'] as $size ) {
					if ( ! empty( $size['file'] ) ) {
						$allowed[] = \wp_basename( $size['file'] );
					}
				}
			}
		}

		$name = '' === $file ? \wp_basename( $main ) : \wp_basename( $file );

		if ( ! in_array( $name, $allowed, true ) ) {
			return '';
		}

		$path      = \realpath( \dirname( $main ) . '/' . $name );
		$base_real = \realpath( $base );

		if ( ! $path || ! $base_real || 0 !== strpos( $path, $base_real . DIRECTORY_SEPARATOR ) || ! \is_file( $path ) ) {
			return '';
		}

		return $path;
	}

	/**
	 * Use the download handler for protected upload URLs.
	 *
	 * @since 1.6.30
	 *
	 * @param string $url       Attachment URL.
	 * @param int    $attach_id Attachment ID.
	 *
	 * @return string
	 */
	public static function filter_attachment_url( $url, $attach_id ) {
		return self::is_protected( $attach_id ) ? self::get_url( $attach_id, '', self::$signing > 0 ) : $url;
	}

	/**
	 * Resolve image sizes of protected uploads to download handler URLs.
	 *
	 * Core builds size URLs by swapping the file name at the end of the
	 * attachment URL, which does not work for a query-string URL.
	 *
	 * @since 1.6.30
	 *
	 * @param array|false  $out       Short-circuit value.
	 * @param int          $attach_id Attachment ID.
	 * @param string|int[] $size      Requested size.
	 *
	 * @return array|false
	 */
	public static function filter_image_downsize( $out, $attach_id, $size ) {
		if ( false !== $out || ! self::is_protected( $attach_id ) || ! \wp_attachment_is_image( $attach_id ) ) {
			return $out;
		}

		$meta = \wp_get_attachment_metadata( $attach_id );

		if ( ! is_array( $meta ) ) {
			return $out;
		}

		$intermediate = 'full' === $size ? false : \image_get_intermediate_size( $attach_id, $size );

		if ( $intermediate && ! empty( $intermediate['file'] ) ) {
			return array(
				self::get_url( $attach_id, $intermediate['file'], self::$signing > 0 ),
				(int) $intermediate['width'],
				(int) $intermediate['height'],
				true,
			);
		}

		$width  = isset( $meta['width'] ) ? (int) $meta['width'] : 0;
		$height = isset( $meta['height'] ) ? (int) $meta['height'] : 0;

		if ( $width && $height ) {
			list( $width, $height ) = \image_constrain_size_for_editor( $width, $height, $size );
		}

		return array( self::get_url( $attach_id, '', self::$signing > 0 ), $width, $height, false );
	}

	/**
	 * Drop srcset for protected uploads; core would build public URLs.
	 *
	 * @since 1.6.30
	 *
	 * @param array|false $sources       Srcset sources.
	 * @param array       $size_array    Width and height.
	 * @param string      $image_src     Image src.
	 * @param array       $image_meta    Image meta.
	 * @param int         $attachment_id Attachment ID.
	 *
	 * @return array|false
	 */
	public static function filter_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
		return self::is_protected( $attachment_id ) ? false : $sources;
	}

	/**
	 * Point Media Library size URLs of protected uploads at the handler.
	 *
	 * @since 1.6.30
	 *
	 * @param array   $response   Attachment data for JS.
	 * @param WP_Post $attachment Attachment post.
	 *
	 * @return array
	 */
	public static function filter_attachment_for_js( $response, $attachment ) {
		if ( empty( $response['sizes'] ) || ! is_array( $response['sizes'] ) || ! self::is_protected( $attachment->ID ) ) {
			return $response;
		}

		$meta = \wp_get_attachment_metadata( $attachment->ID );

		foreach ( $response['sizes'] as $name => $data ) {
			$file = '';

			if ( 'full' !== $name && isset( $meta['sizes'][ $name ]['file'] ) ) {
				$file = $meta['sizes'][ $name ]['file'];
			}

			$response['sizes'][ $name ]['url'] = self::get_url( $attachment->ID, $file );
		}

		return $response;
	}

	/**
	 * Stream a protected upload after the access check.
	 *
	 * @since 1.6.30
	 *
	 * @return void
	 */
	public static function serve() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only download; access is checked below.
		$attach_id = isset( $_GET['id'] ) && is_scalar( $_GET['id'] ) ? \absint( $_GET['id'] ) : 0;
		$file      = isset( $_GET['file'] ) && is_scalar( $_GET['file'] ) ? \sanitize_file_name( \wp_unslash( $_GET['file'] ) ) : '';
		$key       = isset( $_GET['key'] ) && is_scalar( $_GET['key'] ) ? \sanitize_text_field( \wp_unslash( $_GET['key'] ) ) : '';
		$expires   = isset( $_GET['expires'] ) && \is_scalar( $_GET['expires'] ) ? \absint( $_GET['expires'] ) : 0;
		// phpcs:enable

		$path = self::can_view( $attach_id, $key, $expires ) ? self::resolve_path( $attach_id, $file ) : '';

		if ( '' === $path ) {
			\wp_die( \esc_html__( 'File not found.', 'weforms' ), '', array( 'response' => 404 ) );
		}

		$type = \wp_check_filetype( $path );
		$mime = ! empty( $type['type'] ) ? $type['type'] : 'application/octet-stream';
		$ext  = ! empty( $type['ext'] ) ? '.' . $type['ext'] : '';

		$inline = in_array( $mime, array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp' ), true );
		$title  = \sanitize_file_name( (string) \get_post_field( 'post_title', $attach_id ) );
		$name   = ( '' !== $title ? $title : 'file' ) . $ext;

		while ( \ob_get_level() ) {
			\ob_end_clean();
		}

		\nocache_headers();
		header( 'Content-Type: ' . $mime );
		header( 'Content-Disposition: ' . ( $inline ? 'inline' : 'attachment' ) . '; filename="' . $name . '"' );
		header( 'Content-Length: ' . \filesize( $path ) );
		header( 'X-Content-Type-Options: nosniff' );
		header( "Content-Security-Policy: default-src 'none'; sandbox" );

		\readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
		exit;
	}

	/**
	 * Make existing form uploads private, up to $limit per call.
	 *
	 * This hides them from public media queries. Files that belong to a
	 * weForms form are then moved by migrate_legacy_files(). Updated rows
	 * leave the query, so the next call picks up where this one stopped.
	 *
	 * @since 1.6.30
	 *
	 * @param int $limit Attachments per call.
	 *
	 * @return void
	 */
	public static function maybe_migrate_legacy_uploads( $limit = 200 ) {
		if ( \get_option( self::MIGRATED_OPTION ) ) {
			return;
		}

		global $wpdb;

		$limit = \max( 1, \absint( $limit ) );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT p.ID FROM {$wpdb->posts} p
				INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID
				WHERE p.post_type = 'attachment'
				AND p.post_status = 'inherit'
				AND p.post_parent = 0
				AND m.meta_key IN ( '_weforms_upload_key', '_weforms_entry_id', 'attachment_form_id' )
				ORDER BY p.ID
				LIMIT %d",
				$limit
			)
		);

		foreach ( $ids as $id ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->update( $wpdb->posts, array( 'post_status' => 'private' ), array( 'ID' => (int) $id ) );
			\clean_post_cache( (int) $id );
		}

		if ( \count( $ids ) < $limit ) {
			\update_option( self::MIGRATED_OPTION, 1 );
		}
	}

	/**
	 * Move earlier form uploads into protected storage, in batches, for up to
	 * five seconds per admin request.
	 *
	 * Only attachments tied to a weForms entry or form are moved. Links to the
	 * old public path stop working; entry screens use the handler URL.
	 *
	 * @since 1.6.30
	 *
	 * @param int $limit Attachments per batch.
	 *
	 * @return void
	 */
	public static function migrate_legacy_files( $limit = 20 ) {
		if ( \get_option( self::FILES_MIGRATED_OPTION ) || \get_transient( self::FILES_MIGRATED_OPTION . '_lock' ) ) {
			return;
		}

		\set_transient( self::FILES_MIGRATED_OPTION . '_lock', 1, MINUTE_IN_SECONDS );

		$started = microtime( true );

		do {
			$done = self::migrate_legacy_files_batch( $limit );
		} while ( ! $done && microtime( true ) - $started < 5 );

		if ( $done ) {
			\update_option( self::FILES_MIGRATED_OPTION, 1 );
		}

		\delete_transient( self::FILES_MIGRATED_OPTION . '_lock' );
	}

	/**
	 * Move one batch of earlier form uploads.
	 *
	 * @since 1.6.30
	 *
	 * @param int $limit Attachments per batch.
	 *
	 * @return bool Whether no eligible attachments remain.
	 */
	private static function migrate_legacy_files_batch( $limit ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT p.ID FROM {$wpdb->posts} p
				INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID
				WHERE p.post_type = 'attachment'
				AND m.meta_key IN ( '_weforms_entry_id', 'attachment_form_id' )
				AND NOT EXISTS (
					SELECT 1 FROM {$wpdb->postmeta} x
					WHERE x.post_id = p.ID AND x.meta_key IN ( %s, %s )
				)
				ORDER BY p.ID
				LIMIT %d",
				self::META,
				self::MOVE_FAILED_META,
				\absint( $limit )
			)
		);

		foreach ( $ids as $id ) {
			if ( ! self::move_into_protected_storage( (int) $id ) ) {
				\update_post_meta( (int) $id, self::MOVE_FAILED_META, 1 );
			}
		}

		return count( $ids ) < \absint( $limit );
	}

	/**
	 * Move one attachment and its image sizes into protected storage.
	 *
	 * @since 1.6.30
	 *
	 * @param int $attach_id Attachment ID.
	 *
	 * @return bool Whether the attachment is now protected.
	 */
	public static function move_into_protected_storage( $attach_id ) {
		$main    = \get_attached_file( $attach_id );
		$uploads = \wp_upload_dir( null, false );
		$base    = self::get_base_dir();

		if ( ! $main || ! \is_file( $main ) || '' === $base || ! self::ensure_directory() ) {
			return false;
		}

		$main_real = \realpath( $main );
		$base_dir  = \realpath( $uploads['basedir'] );

		if ( ! $main_real || ! $base_dir || 0 !== strpos( $main_real, $base_dir . DIRECTORY_SEPARATOR ) ) {
			return false;
		}

		if ( 0 === strpos( $main_real, \realpath( $base ) . DIRECTORY_SEPARATOR ) ) {
			self::protect( $attach_id );

			return self::is_protected( $attach_id );
		}

		$relative   = \_wp_relative_upload_path( $main );
		$subdir     = \dirname( $relative );
		$target_dir = $base . ( '.' === $subdir ? '' : '/' . $subdir );

		if ( ! \wp_mkdir_p( $target_dir ) ) {
			return false;
		}

		$source_dir = \dirname( $main );
		$stem       = \wp_generate_password( 32, false, false );
		$meta       = \wp_get_attachment_metadata( $attach_id );
		$meta       = is_array( $meta ) ? $meta : array();
		$names      = array( \wp_basename( $main ) => $stem . self::extension( $main ) );

		if ( ! empty( $meta['original_image'] ) ) {
			$names[ \wp_basename( $meta['original_image'] ) ] = $stem . '-original' . self::extension( $meta['original_image'] );
		}

		if ( ! empty( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
			foreach ( $meta['sizes'] as $size ) {
				if ( ! empty( $size['file'] ) && ! isset( $names[ \wp_basename( $size['file'] ) ] ) ) {
					$names[ \wp_basename( $size['file'] ) ] = $stem . '-' . substr( md5( $size['file'] ), 0, 8 ) . self::extension( $size['file'] );
				}
			}
		}

		$moved = array();

		foreach ( $names as $old => $new ) {
			$from = $source_dir . '/' . $old;

			if ( ! \is_file( $from ) ) {
				continue;
			}

			if ( ! @\rename( $from, $target_dir . '/' . $new ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions.rename_rename
				foreach ( $moved as $done_old => $done_new ) {
					@\rename( $target_dir . '/' . $done_new, $source_dir . '/' . $done_old ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions.rename_rename
				}

				return false;
			}

			$moved[ $old ] = $new;
		}

		\update_attached_file( $attach_id, $target_dir . '/' . $names[ \wp_basename( $main ) ] );

		if ( $meta ) {
			$meta['file'] = \_wp_relative_upload_path( $target_dir . '/' . $names[ \wp_basename( $main ) ] );

			if ( ! empty( $meta['original_image'] ) ) {
				$old = \wp_basename( $meta['original_image'] );

				if ( isset( $moved[ $old ] ) ) {
					$meta['original_image'] = $moved[ $old ];
				} else {
					unset( $meta['original_image'] );
				}
			}

			if ( ! empty( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
				foreach ( $meta['sizes'] as $key => $size ) {
					$old = ! empty( $size['file'] ) ? \wp_basename( $size['file'] ) : '';

					if ( '' !== $old && isset( $moved[ $old ] ) ) {
						$meta['sizes'][ $key ]['file'] = $moved[ $old ];
					} else {
						unset( $meta['sizes'][ $key ] );
					}
				}
			}

			\wp_update_attachment_metadata( $attach_id, $meta );
		}

		self::protect( $attach_id );

		return self::is_protected( $attach_id );
	}

	/**
	 * Lower-case extension of a file name, with the leading dot.
	 *
	 * @since 1.6.30
	 *
	 * @param string $file File name or path.
	 *
	 * @return string Empty string when there is no extension.
	 */
	private static function extension( $file ) {
		$ext = preg_replace( '/[^a-z0-9]/', '', strtolower( (string) \pathinfo( $file, PATHINFO_EXTENSION ) ) );

		return '' !== $ext ? '.' . $ext : '';
	}
}
