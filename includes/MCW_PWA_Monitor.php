<?php
/*
 * Monitoring performance and report First Paint, First Contentful Paint, and Time To Interactive if Google Analytics is available.
 * @since 0.1.7
 */ 

require_once( MCW_PWA_DIR . 'includes/MCW_PWA_Module.php' );

class MCW_PWA_Monitor extends MCW_PWA_Module {
	private static $__instance = null;
	/**
	 * Singleton implementation
	 *
	 * @return MCW_PWA_LazyLoad instance
	 */
	public static function instance() {
		if ( ! is_a( self::$__instance, 'MCW_PWA_Monitor' ) ) {
			self::$__instance = new MCW_PWA_Monitor();
		}

		return self::$__instance;
	}

	public function getKey() {
		return 'mcw_enable_monitor';
	}

	public function initScript() {
        if(!is_admin()){
            add_action( 'wp_head', array( $this, 'addObserver' ), 0 );
            add_action( 'wp_print_footer_scripts', array( $this, 'addFooterScript' ), 99 );
            wp_enqueue_script( 'tti_polyfill', MCW_PWA_URL . 'scripts/node_modules/tti-polyfill/tti-polyfill.js',array(),MCW_PWA_VER);
            wp_enqueue_script('tti_performance',MCW_PWA_URL . 'scripts/performance.js',array('tti_polyfill'),MCW_PWA_VER,true);
        }
	}


    

	public function settingsApiInit() {
		register_setting(
			MCW_PWA_OPTION, $this->getKey(),
			array(
				'type'              => 'boolean',
				'description'       => 'Enable speed monitoring only if you use Google Analytics. Disable it if you\'re not use it.',
				'default'           => 0,
				'sanitize_callback' => array( $this, 'settingSanitize' ),
			)
		);

				// Add the field with the names and function to use for our new
		// settings, put it in our new section
		add_settings_field(
			$this->getKey(),
			'Enable Speed Monitoring To Google Analytics',
			array( $this, 'settingCallback' ),
			MCW_PWA_SETTING_PAGE,
			MCW_SECTION_PERFORMANCE
		);
	}


	public function addObserver() {
		echo '
          <script>
          
          !function(){if("PerformanceLongTaskTiming" in window){var g=window.__tti={e:[]};
            g.o=new PerformanceObserver(function(l){g.e=g.e.concat(l.getEntries())});
            g.o.observe({entryTypes:["longtask"]})} 
            
            window._performanceMetrics=[];

            const observer = new PerformanceObserver((list) => {
                const currentPage=document.querySelector("title").text;
                for (const entry of list.getEntries()) {
                  const metricName = entry.name;
                  const time = Math.round(entry.startTime + entry.duration);
                  
                  _performanceMetrics.push({
                        hitType: "timing",
                        timingCategory: "Load Performance",
                        timingVar: metricName,
                        timingValue: time,
                        timingLabel:currentPage
                    });
                }
              });
              
              // Start observing paint entries.
              observer.observe({entryTypes: ["paint"]});
            }(); 
          </script>
          ';
    }
}
