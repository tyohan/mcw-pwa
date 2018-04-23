## Code Structures
The plugin has 5 main directories which are
* assets, static assets for images, fonts, and CSS files.
* docs, documentation directory
* includes, internal modules directory
* scripts, public directory for all JavaScript files.
* vendor, third party modules directory. All PHP modules manage by composer goes here 

The main script is in plugin root directory called `MCW_PWA.php` which manage how the modules from includes directory load and run in plugin thread. 

## Modules
Currently there are 4 main modules inside `includes` directory which are

### 1. Service Worker
This module responsible to generate, register service worker, and detect if AMP plugin loaded so it will add amp-install-serviceworker component to AMP page. It's inside `include/service_workers` directory with 3 files 
 * `MCW_PWA_Offline_Setting.php` - to show offline section in plugin setting page
 * `MCW_PWA_Precaches_Setting.php` - to show precache management section in plugin setting page
 * `MCW_PWA_Service_Worker.php` - main file that manage all service worker stuff in frontend side. 


The service worker module has 5 main functions
* register service worker at WordPress site by hook to `wp_print_footer_scripts`
* generate service worker dynamicly in template redirect hook by detecting if `MCW_SW_QUERY_VAR` exist in URL query
* detecting if AMP plugin serve AMP page and will hook a function to generate `amp-install-serviceworker` component in the page using `amp_post_template_head` and `amp_post_template_footer` hooks.
* Precache management to manage which URLs to precache when service worker installed.
* Provide offline page when user navigate to a page but the connection is not available.

#### Precache Management
Precache setting pages provide a form to manually add URL to precache in service worker. When the plugin activated, the plugin will scan the current site and detect for any static assets like CSS and JavaScript files and add it to precache recommendation list. Later the admin can manually add it all to precache list.

#### Offline Page
Admin need to create manually a page to serve as offline page. 
After offline page exist, then the admin can select it from offline page setting to enable it as offline page in service worker.

## 2. Assets Management - `MCW_PWA_Assets.php`
handle how the assets like scripts and styles in WordPress loaded. It will add async and defer if needed to tag.

## 3. Lazy Load Assets - `MCW_PWA_LazyLoad.php`
it will detect browser if support Intersection Observer API, and load a polyfil if not. Then make the offscreen images not loaded during the page load.

## 4. Setting Page - `MCW_PWA_Settings.php`
a module to manage setting page. It will add setting section in plugin setting page from all other modules if the module has it.


