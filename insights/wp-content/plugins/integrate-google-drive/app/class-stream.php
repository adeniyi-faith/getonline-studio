<?php

namespace IGD;

defined( 'ABSPATH' ) || exit;

class Stream {

	protected static $instance = null;
	private $file;

	public function __construct( $file_id, $account_id, $ignore_limit = false ) {
		if ( igd_fs()->can_use_premium_code__premium_only() && ! $ignore_limit && igd_fs()->can_use_premium_code__premium_only() && $limit_message = Restrictions::instance()->has_reached_download_limit( $file_id, 'stream' ) ) {
			Restrictions::display_error( $limit_message );
		}

		$app  = App::instance( $account_id );
		$file = $app->get_file_by_id( $file_id );

		if ( igd_is_shortcut( $file['type'] ) ) {
			$file = $app->get_file_by_id( $file['shortcutDetails']['targetId'] );
		}

		$this->file = $file;
		wp_using_ext_object_cache( false );
	}

	public function stream_content() {
		$referrer     = wp_get_raw_referer();
		$is_tutor_lms = strpos( $referrer, '/courses/' ) !== false;

		if ( igd_get_settings( 'secureVideoPlayback' ) && empty( $referrer ) ) {
			wp_die( 'Unauthorized access' );
		}

		do_action( 'igd_insert_log', [
			'type'       => 'stream',
			'file_id'    => $this->file['id'],
			'file_name'  => $this->file['name'],
			'file_type'  => $this->file['type'],
			'account_id' => $this->file['accountId'],
		] );

		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv( 'no-gzip', 1 );
		}

		@ini_set( 'zlib.output_compression', 'Off' );
		@session_write_close();

		wp_ob_end_flush_all();

		$chunk_size = $this->get_chunk_size( $is_tutor_lms ? 'high' : '' );
		$size       = $this->file['size'] ?? 0;
		$length     = $size;
		$start      = 0;
		$end        = $size - 1;

		header( 'Accept-Ranges: bytes' );
		header( 'Content-Type: ' . $this->file['type'] );
		header( 'X-Accel-Buffering: no' );
		header( 'Content-Disposition: inline; filename="' . basename( $this->file['name'] ) . '"' );

		$seconds_to_cache = 60 * 60 * 24;
		header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $seconds_to_cache ) . ' GMT' );
		header( 'Pragma: cache' );
		header( "Cache-Control: max-age={$seconds_to_cache}" );

		if ( isset( $_SERVER['HTTP_RANGE'] ) ) {
			$c_end = $end;
			list( , $range ) = explode( '=', $_SERVER['HTTP_RANGE'], 2 );

			if ( false !== strpos( $range, ',' ) ) {
				header( 'HTTP/1.1 416 Requested Range Not Satisfiable' );
				header( "Content-Range: bytes {$start}-{$end}/{$size}" );
				exit;
			}

			if ( '-' == $range ) {
				$c_start = $size - substr( $range, 1 );
			} else {
				$range   = explode( '-', $range );
				$c_start = (int) $range[0];
				$c_end   = isset( $range[1] ) && is_numeric( $range[1] ) ? (int) $range[1] : $size;
				$c_end   = min( $c_start + $chunk_size, $c_end );
			}

			$c_end = min( $c_end, $end );

			if ( $c_start > $c_end || $c_start > $size - 1 || $c_end >= $size ) {
				header( 'HTTP/1.1 416 Requested Range Not Satisfiable' );
				header( "Content-Range: bytes {$start}-{$end}/{$size}" );
				exit;
			}

			$start  = $c_start;
			$end    = $c_end;
			$length = $end - $start + 1;
			header( 'HTTP/1.1 206 Partial Content' );
		}

		header( "Content-Range: bytes {$start}-{$end}/{$size}" );
		header( 'Content-Length: ' . $length );

		@ini_set( 'max_execution_time', 0 );

		$chunk_start = $start;

		while ( $chunk_start <= $end ) {
			if ( connection_aborted() ) {
				break;
			}

			$chunk_end = min( $chunk_start + $chunk_size, $end );
			$this->stream_get_chunk( $chunk_start, $chunk_end );
			$chunk_start = $chunk_end + 1;

			igd_server_throttle( $is_tutor_lms ? 'high' : '' );
		}

	}

	private function stream_get_chunk( $start, $end, $chunked = true ) {
		$headers = $chunked ? [ 'Range' => 'bytes=' . $start . '-' . $end ] : [];

		if ( ! empty( $this->file['resourceKey'] ) ) {
			$headers['X-Goog-Drive-Resource-Keys'] = $this->file['id'] . '/' . $this->file['resourceKey'];
		}

		$request = new \IGDGoogle_Http_Request( $this->get_api_url(), 'GET', $headers );
		$request->disableGzip();

		$client = App::instance()->client;
		$client->getIo()->setOptions( [
			CURLOPT_RETURNTRANSFER => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HEADER         => false,
			CURLOPT_WRITEFUNCTION  => [ $this, 'stream_chunk_to_output' ],
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT        => 30,
		] );

		try {
			$client->getAuth()->authenticatedRequest( $request );
		} catch ( \Exception $e ) {
			sleep( 1 );
			$client->getAuth()->authenticatedRequest( $request );
		}
	}

	public function stream_chunk_to_output( $ch, $str ) {
		echo $str;
		flush();

		return strlen( $str );
	}

	private function get_chunk_size( $value = '' ) {
		$value = $value ?: igd_get_settings( 'serverThrottle', 'off' );

		switch ( $value ) {
			case 'high':
				$chunk_size = 1024 * 1024 * 2;
				break;
			case 'medium':
				$chunk_size = 1024 * 1024 * 10;
				break;
			case 'low':
				$chunk_size = 1024 * 1024 * 20;
				break;
			case 'off':
			default:
				$chunk_size = 1024 * 1024 * 50;
				break;
		}

		$free_mem = igd_get_free_memory_available();

		return $free_mem > 0 ? min( $free_mem - ( 1024 * 1024 * 5 ), $chunk_size ) : $chunk_size;
	}

	public function get_api_url() {
		return 'https://www.googleapis.com/drive/v3/files/' . $this->file['id'] . '?alt=media';
	}

	public static function instance( $file_id, $account_id, $ignore_limit = false ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $file_id, $account_id, $ignore_limit );
		}

		return self::$instance;
	}
}