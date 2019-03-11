workbox.routing.registerRoute( /wp-admin(.*)|(.*)preview=true(.*)/,
	workbox.strategies.networkOnly()
);

// Stale while revalidate for JS and CSS that are not precache
workbox.routing.registerRoute(
	/\.(?:js|css)$/,
	workbox.strategies.staleWhileRevalidate()
);

// We want no more than 60 images in the cache. We check using a cache first strategy
workbox.routing.registerRoute(
	/\.(?:png|gif|jpg|jpeg|svg)$/,
	new workbox.strategies.CacheFirst({
	  cacheName: 'images',
	  plugins: [
		new workbox.expiration.Plugin({
		  maxEntries: 60,
		  maxAgeSeconds: 30 * 24 * 60 * 60, // 30 Days
		}),
	  ],
	})
  );


// We need cache fonts if any
workbox.routing.registerRoute( /(.*)\.(?:woff|eot|woff2|ttf|svg)$/,
	workbox.strategies.cacheFirst( {
		cacheExpiration: {
			maxEntries: 20,
		},
		cacheableResponse: {
			statuses: [ 0, 200 ],
		},
	} )
);

// Cache the Google Fonts stylesheets with a stale-while-revalidate strategy.
workbox.routing.registerRoute(
	/^https:\/\/fonts\.googleapis\.com/,
	new workbox.strategies.StaleWhileRevalidate({
	  cacheName: 'google-fonts-stylesheets',
	})
  );
  
  // Cache the underlying font files with a cache-first strategy for 1 year.
  workbox.routing.registerRoute(
	/^https:\/\/fonts\.gstatic\.com/,
	new workbox.strategies.CacheFirst({
	  cacheName: 'google-fonts-webfonts',
	  plugins: [
		new workbox.cacheableResponse.Plugin({
		  statuses: [0, 200],
		}),
		new workbox.expiration.Plugin({
		  maxAgeSeconds: 60 * 60 * 24 * 365,
		  maxEntries: 30,
		}),
	  ],
	})
  );

  workbox.routing.registerRoute(
	/.*(?:googleapis|gstatic)\.com/,
	new workbox.strategies.StaleWhileRevalidate()
  );

