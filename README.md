### Minimum Configuration WordPress PWA Plugin

## Overview
This plugin is developed to focus on enhancing the loading experiences with minimum configuration.

The plugin's current features:
* use service worker to cache all static assets like images, JavaScripts, CSS styles, and fonts. It use [Workbox](https://developers.google.com/web/tools/workbox/) as a library.
* use Intersection Observer API to lazy load images on page.
* make scripts loaded with async if no dependency found and defer if it has dependency like jQuery library. **This is a bit risky if themes or plugins not include the scripts with wp_enqueue_script function or not include dependency when they enqueue the script. Make sure you check there is no error on JavaScript console when you activate the plugin.**
* install service worker on AMP page if the website has AMP plugin running. AMP is a custom elements library that boost the website loading performance.
* add offline page to show when user offline. 
* scan and set static URLs to precache with service worker.
* preload your CSS and JavaScripts to boost performance.
* Integrated loading performance monitoring with Google Analytics. If you have Google Analytics, it will track page load [user performance metric](https://developers.google.com/web/fundamentals/performance/user-centric-performance-metrics) to your Google Analytics using [user timings report](https://developers.google.com/analytics/devguides/collection/analyticsjs/user-timings).

## To Develop
* Select page to precache
* Notificaton on offline connection

## Setup For Development
To install the plugin follow the steps. Follow this [steps](https://ben.lobaugh.net/blog/147853/creating-a-two-way-sync-between-a-github-repository-and-subversion) to work on git svn syncronize repo.

1. Clone the repo to a directory inside your `wp-content/plugins` directory by running command in your terminal `git clone https://github.com/wp-id/mcw-pwa.git mcw-pwa`
2. Install NPM first because we need it to install Workbox library
3. Switch your terminal working directory to `mcw-pwa/scripts` and run command `npm install` to install the WorkBox library.
4. Activate the plugin inside your WordPress Admin Panel on plugin section.
5. Check with your Browser's DevTool to **make sure there is no JavaScript error in your JavaScript console.**

## Credits
I use some plugins as my references to develop this plugin. Please check their amazing works
* [BJ Lazy Load](https://wordpress.org/plugins/bj-lazy-load/)
* [Jetpack](https://wordpress.org/plugins/jetpack/) 
* [Accelerated Mobile Pages - AMP](https://wordpress.org/plugins/amp/)
* [Workbox](https://developers.google.com/web/tools/workbox/)