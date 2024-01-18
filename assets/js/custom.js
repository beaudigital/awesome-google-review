jQuery(document).ready(function ($) {
  toastr.options = {
    closeButton: false,
    debug: false,
    newestOnTop: false,
    progressBar: true,
    positionClass: "toast-bottom-right",
    preventDuplicates: true,
    onclick: null,
    showDuration: "1000",
    hideDuration: "1000",
    timeOut: "3500",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "slideDown",
    hideMethod: "slideUp",
  };


  var buttons = document.querySelectorAll( '.arlina-button' );

Array.prototype.slice.call( buttons ).forEach( function( button ) {

	var resetTimeout;

	button.addEventListener( 'click', function() {
		
		if( typeof button.getAttribute( 'data-loading' ) === 'string' ) {
			button.removeAttribute( 'data-loading' );
		}
		else {
			button.setAttribute( 'data-loading', '' );
		}

		clearTimeout( resetTimeout );
		resetTimeout = setTimeout( function() {
			button.removeAttribute( 'data-loading' );			
		}, 2000 );

	}, false );

} );
});
