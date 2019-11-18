(function(){
	// @todo: let js takeover the countdown in real time to help defeat caching
  'use strict';

  window.addEventListener( 'load', init, false );

  function init() {

    var countdowns, i;

    countdowns = document.querySelectorAll( '.uri-countdown' );
    console.log( countdowns );

    for ( i = 0; i < countdowns.length; i++ ) {
      setupCountdown( countdowns[i] );
    }

  }

  function setupCountdown( countdown ) {

    var cvalue, hash;

    countdown.querySelector( '.dismiss' ).addEventListener( 'click', dismiss.bind( null, countdown ), false );

    hash = countdown.getAttribute( 'data-hash' );
    cvalue = getCookie( 'uri-countdown-' + hash );

    console.log( cvalue );

    if ( 'dismissed' == cvalue ) {
      dismiss( countdown );
    }

  }

  function dismiss( countdown ) {

    var hash;

    console.log( 'dismiss' );
    hash = countdown.getAttribute( 'data-hash' );

    countdown.classList.add( 'dismissed' );
    setCookie( 'uri-countdown-' + hash, 'dismissed', 30 );

  }

  function setCookie(cname, cvalue, exdays) {
      var d = new Date();
      d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
      var expires = 'expires='+d.toUTCString();
      document.cookie = cname + '=' + cvalue + ';' + expires + ';path=/';
  }

  function getCookie(cname) {
      var name = cname + '=';
      var ca = document.cookie.split(';');
      for(var i = 0; i < ca.length; i++) {
          var c = ca[i];
          while (c.charAt(0) == ' ') {
              c = c.substring(1);
          }
          if (c.indexOf(name) == 0) {
              return c.substring(name.length, c.length);
          }
      }
      return '';
  }

})();
