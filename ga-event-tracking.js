( function() {
	"use strict";

	// Get headline link URLs
	var wpdHeadlines = document.getElementsByClassName('headline-link');

	// Combine and win
	var wpdTrackThese = Array.prototype.slice.call(wpdHeadlines);

	// Get image links
	var wpdHeadlineImgWrap = document.getElementsByClassName('link-list-image');
	for (var i = 0; i < wpdHeadlineImgWrap.length; ++i) {
		wpdTrackThese
			.push(wpdHeadlineImgWrap[i]
			.getElementsByTagName('a')[0]);
	}

	// Get single page external links
	var wpdExternalLinkWrap = document.getElementsByClassName('external-link');
	for ( i = 0; i < wpdExternalLinkWrap.length; ++i) {
		wpdTrackThese
			.push(wpdExternalLinkWrap[i].getElementsByTagName('a')[0])
			.push(wpdExternalLinkWrap[i].getElementsByTagName('a')[1]);
	}

	// Attach click events
	for ( i = 0; i < wpdTrackThese.length; ++i) {

		var el = wpdTrackThese[i];

		// Make sure we only set this for external links
		if ( el.host === window.location.host ){
			// continue;
		}

		if (el.addEventListener) {
			el.addEventListener('click', wpdGAClickEvent, false);
		} else if (el.attachEvent) {
			el.attachEvent('onclick', wpdGAClickEvent);
		}
	}

	function wpdGAClickEvent( event ) {
		//if ( typeof _gaq !== 'undefined' && typeof _gaq.push === 'function' ) {
		//	console.log(event.srcElement.href);
		//	_gaq.push(['_trackEvent', 'WPD Outbound Link', 'click', 'URL', event.srcElement.href ]);
		//}
		//else if (typeof ga !== 'undefined') {
		//	console.log(event.srcElement.href);
		//	ga('send', 'event', 'WPD Outbound Link', 'click', 'URL', event.srcElement.href );
		//}
		//else {
		//	console.log( 'Google Analytics is not installed on this site.' );
		//}
		ga('send', 'event', 'WPD Outbound Link', 'click', 'URL', event.srcElement.href);
	}

}());