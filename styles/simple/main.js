(function($){

// Keep track of the keyCodes we'll use.
var keyCode = {
		DOWN: 40,
		LEFT: 37,
		RIGHT: 39,
		UP: 38,
	};

/**
 * PRESENTATION CONSTRUCTOR
 */
function Presentation( elem ) {
	var hash;
	
	this.root = elem;
	this.slides = elem.find('.slide');
	this.scroller = $( elem._scrollable() );
	this.id = elem.attr('id');
	
	if ( ! this.id ) {
		this.id = Presentation.generateUuid();
		elem.attr('id', this.id );
	}
	
	
	// Select the current slide from either
	// a hash in the URL, or the first slide in the stack.
	hash = this.parseLocationHash();
	this.select( hash === false ? this.slides.first() : hash );
}

/**
 * PRESENTATION API
 */
$.extend( Presentation.prototype, {
	/**
	 * Select a slide.
	 * 
	 * @param { jQuery | DOMElement | String | Number } slide The slide to select.
	 */
	select: function( slide ) {
		// Search for a slide based on the input.
		slide = this.findSlide( slide );

		// If we don't find a slide, return.
		if ( ! slide )
			return;
		
		// We found a slide, update the current slide.
		this.current = slide;
		
		// Do the animation
		this.scroller.stop( true, true )	// Complete any existing animation
			.scrollTo( this.current, {		// Scroll to the selected slide.
				duration: 500,
				easing: 'swing',
				onAfter: function() {
					// Once the slide is in place,
					// we update the browser location
					window.location.hash = slide.data('locationHash');
				}
			});
	},
	/**
	 * Selects the next slide.
	 */
	next: function() {
		if(this.current) {
			var next = this.current.next();
			
			while ( next.length && ! next.hasClass('slide') )
				next = next.next();
			
			if ( next.length )
				this.select( next );
		}
	},
	/**
	 * Selects the previous slide.
	 */
	prev: function() {
		if(this.current) {
			var prev = this.current.prev();
			
			while ( prev.length && ! prev.hasClass('slide') )
				prev = prev.prev();
			
			if ( prev.length )
				this.select( prev );
		}
	},
	/**
	 * Quickly refreshes the position of the current slide.
	 */
	refresh: function() {
		if(this.current) {
			this.scroller.stop().scrollTo( this.current );
		}
	},
	/**
	 * Makes the presentation full-screen.
	 * 
	 * @param { boolean } on Whether to force the fullscreen on or off.
	 * 						 Optional. Toggles status by default.
	 */
	fullscreen: function( on ) {
		// Toggle or set fullscreen.
		this.root.toggleClass('presentation-fullscreen', on );
		// Update whether the fullscreen is on.
		on = this.root.hasClass('presentation-fullscreen');
		
		$('html, body').toggleClass('presentation-fullscreen-root', on );
		// If fullscreen is active, refresh the slide position
		// when the window is resized.
		this.refreshOnResize( on );
		// Refresh the slide position to fit the new window size.
		this.refresh();
		
		if(on) {
			$('#wpadminbar').hide();
		} else {
			$('#wpadminbar').show();
		}
	}
});


/**
 * LOW-LEVEL PRESENTATION API
 */
$.extend( Presentation.prototype, {
	refreshOnResize: function( bind ) {
		var self = this;
		// Remove the resize function from only this
		// namespace (if it previously was bound).
		$(window).unbind( 'resize.' + this.id );
		
		// Namespace the resize function to this
		// specific presentation (using the id).
		if ( bind ) {
			$(window).bind( 'resize.presentation.' + this.id, function() {
				self.refresh();
			});
		}
	},
	/**
	 * Fetches a slide and updates its location hash.
	 * 
	 * @param { jQuery | DOMElement | String | Number } slide The slide to select.
	 * @return jQuery The slide.
	 */
	findSlide: function( slide ) {
		var hash;
		
		// String input: the user provided an ID
		if ( typeof slide == "string" ) {
			hash = slide;
			slide = $('#' + hash);
			
		// Numeric input: the user provided an index
		} else if ( typeof slide == "number" ) {
			hash = slide;
			slide = this.slides.eq( hash );
		// jQuery/DOM input: the user provided a slide
		} else {
			// Convert a DOM element into a jQuery object.
			if ( slide.nodeType )
				slide = $(slide);
			
			// Check for an id. If not found, use the index.
			hash = slide.attr('id') || this.slides.index( slide );
		}
		
		// Check to see if we found an element.
		// If we didn't, return false.
		if ( ! slide.length || slide[0].parentNode.parentNode !== this.root[0] )
			return false;
		
		// Update the location hash
		return slide.data('locationHash', this.buildLocationHash( hash ) );
	},
	/**
	 * Parses a location hash value.
	 * 
	 * Accepts hashes of the form:
	 * 		#? presentationId - [ slideId | slideIndex ]
	 * 
	 * @param { String } Optional. The hash value to parse.
	 * 					 Defaults to window.location.hash.
	 * 
	 * @return The parsed value (the slideId or the slideIndex), or false.
	 */
	parseLocationHash: function( hash ) {
		var index;
		
		hash = hash || window.location.hash;
		
		// Strip the hash symbol
		if ( hash.indexOf('#') == 0 )
			hash = hash.substr( 1 );
		
		// Check for the presentationId
		if ( hash.indexOf( this.id ) != 0 )
			return false;
			
		hash = hash.substr( this.id.length + 1 ); // Remove the '-' as well.

		// Check for an index.
		index = parseInt( hash, 10 );
		// If it returned a number, it's an index. If it didn't, it's an id.
		return isNaN( index ) ? hash : index;
	},
	/**
	 * Builds a location hash value.
	 */
	buildLocationHash: function( hash ) {
		return '#' + this.id + '-' + hash;
	}
});

$.extend( Presentation, {
	_uuid: 0,
	generateUuid: function() {
		return '_presentation' + Presentation._uuid++;
	}
});


/**
 * BIND HANDLERS
 */
$(document).ready( function() {
	var doc = $(document),
		presentations = $('.presentation'),
		active,
		// Build our key-based navigation.
		pressKey = function( event ) {
			if ( ! active )
				return;
			switch ( event.keyCode ) {
				case keyCode.UP:
				case keyCode.LEFT:
					active.prev();
					break;
				case keyCode.DOWN:
				case keyCode.RIGHT:
					active.next();
					break;
				// If we're not looking for the pressed key,
				// let the browser handle it by returning.
				default:
					return;
			}

			// If we did use the key, we want to prevent default browser behavior
			// and stop it from bubbling up the DOM. Returning false accomplishes both.
			//
			// Otherwise, in this case, the browser would scroll up/down slightly.
			return false;
		};
	
	// Build the presentations.
	presentations.each( function() {
		var t = $(this);
		t.data('presentation', new Presentation( t ) );
	});
	
	// Set the presentation to active when it is clicked,
	// and clear the active presentation when another object is clicked.
	doc.bind( 'click.presentation', function( event ) {
		var pres = $(event.target).parents('.presentation').first();
		active = pres.length ? pres.data('presentation') : null;
		test = pres.length ? pres.data('presentation') : null;
	});
	
	// Bind our key-based navigation.
	doc.bind( 'keydown.presentation', pressKey );
	
	// Add full-screen triggers
	presentations.live( 'dblclick.presentation', function ( event ) {
		$(this).data('presentation').fullscreen();
	});
	
	// Maintain key-navigation in a slide's child iframes.
	// It would be nice to delegate this,
	// but you need to grab the iframe's document.
	$('iframe').contents().bind( 'keydown.presentation', function( event ) {
		return pressKey(event);
	});
});

})(jQuery);