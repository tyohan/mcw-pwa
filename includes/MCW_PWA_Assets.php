<?php
require_once( MCW_PWA_DIR . 'includes/MCW_PWA_Module.php' );
class MCW_PWA_Assets extends MCW_PWA_Module {
	private static $__instance   = null;
	private $_scripts            = [];
	private $_styles             = [];
	protected $__enableByDefault = false;

	/**
	 * Singleton implementation
	 *
	 * @return MCW_PWA_Assets instance
	 */
	public static function instance() {
		if ( ! is_a( self::$__instance, 'MCW_PWA_Assets' ) ) {
			self::$__instance = new MCW_PWA_Assets();
		}

		return self::$__instance;
	}

	public function getKey() {
		return 'mcw_enable_assets';
	}

	public function initScript() {
		if ( ! is_admin() ) {
			add_action('wp_head',array($this,'addPreload'), 1);
			add_filter( 'script_loader_tag', array( $this, 'addDeferAsyncAttribute' ), 10, 2 );
			// Remove WP Version From Styles
			//add_filter( 'style_loader_src', array( $this, 'removeVersion' ), 9999, 1 );
		
			// // Remove WP Version From Scripts
			// add_filter( 'script_loader_src', array( $this, 'removeVersion' ), 9999, 1 );

						$this->disableEmojis();
		}
	}

	public function settingsApiInit() {
		register_setting(
			MCW_PWA_OPTION, $this->getKey(),
			array(
				'type'        => 'boolean',
				'description' => 'Enable Async Defer Assets Loading',
				'default'     => 1,
				//'sanitize_callback'=>array($this,'settingSanitize')
			)
		);

				// Add the field with the names and function to use for our new
		// settings, put it in our new section
		add_settings_field(
			$this->getKey(),
			'Enable Async Defer On Scripts',
			array( $this, 'settingCallback' ),
			MCW_PWA_SETTING_PAGE,
			MCW_SECTION_PERFORMANCE
		);
	}

	public function addPreload() {

		global $wp_scripts,$wp_styles;
		
	
		foreach($wp_scripts->queue as $handle) {
			$script = $wp_scripts->registered[$handle];
	
			//-- Weird way to check if script is being enqueued in the footer.
			if($script->extra['group'] === 1) {
	
				//-- If version is set, append to end of source.
				$source = $script->src .'?ver='. ($script->ver ? "{$script->ver}" : get_bloginfo( 'version' ));
	
				//-- Spit out the tag.
				echo "<link rel='preload' href='{$source}' as='script'/>\n";
			}
		}

		
		foreach($wp_styles->queue as $handle){
			$style = $wp_styles->registered[$handle];
			//-- If version is set, append to end of source.
			$source = $style->src .'?ver='. ($style->ver ? "{$style->ver}" : get_bloginfo( 'version' ));
			echo "<link rel='preload' href='{$source}' as='style'/>\n";
		}

	}

	public function removeVersion( $src ) {
		// Function to remove version numbers
		if ( strpos( $src, 'ver=' ) ) {
			$src = remove_query_arg( 'ver', $src );
		}
		return $src;
	}

	private function scriptHasDepts( $handle ) {
		global $wp_scripts;

		foreach ( $wp_scripts->to_do as $other_script_handle ) {
				$other_script = $wp_scripts->registered[ $other_script_handle ];

			if ( ( $other_script->deps !== null ) && ( in_array( $handle, $other_script->deps ) || ( $handle === $other_script_handle && count( $other_script->deps ) !== 0 ) ) ) {
				return true;
			}
		}

		return false;
	}

	protected function shouldDefer( $handle ) {
		if ( $this->scriptHasDepts( $handle ) ) {
			return true;
		}
		return false;
	}

	public function addDeferAsyncAttribute( $tag, $handle ) {
		if ( $this->shouldDefer( $handle ) ) {
			if ( strpos( $tag, 'defer' ) === false ) {
				return str_replace( ' src', ' defer="defer" src', $tag );
			}
		}
		if ( strpos( $tag, 'async' ) === false ) {
			return str_replace( ' src', ' async="async" src', $tag );
		}

				return $tag;
	}

	public function disableEmojis() {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'embed_head', 'print_emoji_detection_script', 7 );

		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );

		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

		add_filter( 'tiny_mce_plugins', array( $this, 'disableEmojiTinymce' ) );
		add_filter( 'wp_resource_hints', array( $this, 'disableEmojisRemoveDNSPrefetch' ), 10, 2 );
	}

		/**
	 * Filter function used to remove the tinymce emoji plugin.
	 *
	 * @param array $plugins
	 * @return array Difference betwen the two arrays
	 */
	public function disableEmojiTinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}

	/**
	 * Remove emoji CDN hostname from DNS prefetching hints.
	 *
	 * @param array $urls URLs to print for resource hints.
	 * @param string $relation_type The relation type the URLs are printed for.
	 * @return array Difference betwen the two arrays.
	 */
	public function disableEmojisRemoveDNSPrefetch( $urls, $relation_type ) {
		if ( 'dns-prefetch' == $relation_type ) {
			/** This filter is documented in wp-includes/formatting.php */
			$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

			$urls = array_diff( $urls, array( $emoji_svg_url ) );
		}

		return $urls;
	}
}
