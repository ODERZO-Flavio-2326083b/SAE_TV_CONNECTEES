( function( $ ) {

    wp.customize( 'background_color_header', function( value ) {
        value.bind( function( newval ) {
            $( 'header' ).css( 'background-color', newval );
            $( 'nav.menu ul' ).css( 'background-color', newval );
        } );
    } );

    wp.customize( 'color_header', function( value ) {
        value.bind( function( newval ) {
            $( '.icon' ).css( 'color', newval );
        } );
    } );

    wp.customize('color_menu', function(value) {
        value.bind(function(newval) {
            $('.menu-item, .menu-item a, .dropbtn').css('color', newval);
        } );
    } );

    wp.customize('background_color_body', function(value) {
        value.bind(function(newval) {
            $('body').css('background-color', newval);
        } );
    } );

    wp.customize('background_color_main', function(value) {
        value.bind(function(newval) {
            $('section').css('background-color', newval);
        } );
    } );

    wp.customize('color_title', function(value) {
        value.bind(function(newval) {
            $('h1, h2, h3, h4, h5, h6').css('color', newval);
        } );
    } );

    wp.customize('color_text', function(value) {
        value.bind(function(newval) {
            $('body').css('color', newval);
        } );
    } );

    wp.customize('background_color_tab_header', function(value) {
        value.bind(function(newval) {
            $('thead tr th, .nav-link.active').css('background-color', newval);
        } );
    } );

    wp.customize('color_tab_header', function(value) {
        value.bind( function(newval) {
            $('thead tr th').css('color', newval);
        } );
    } );

    wp.customize('background_color_tab_content', function(value) {
        value.bind(function(newval) {
            $('td, tr th').css('background-color', newval);
        } );
    } );

    wp.customize('color_tab_content', function(value) {
        value.bind(function(newval) {
            $('td, tr th').css('color', newval);
        } );
    } );

    wp.customize('color_link', function(value) {
        value.bind(function(newval) {
            $('a, a:hover').css('color', newval);
        } );
    } );

    wp.customize( 'background_color_alert', function( value ) {
        value.bind( function( newval ) {
            $( '.alerts' ).css( 'background-color', newval );
        } );
    } );

    wp.customize( 'color_alert', function( value ) {
        value.bind( function( newval ) {
            $( '.alerts' ).css( 'color', newval );
        } );
    } );

    wp.customize( 'background_color_weather', function( value ) {
        value.bind( function( newval ) {
            $( '.Infos' ).css( 'background-color', newval );
        } );
    } );

    wp.customize( 'color_weather', function( value ) {
        value.bind( function( newval ) {
            $( '.Infos' ).css( 'color', newval );
        } );
    } );

    wp.customize( 'color_weather', function( value ) {
        value.bind( function( newval ) {
            $( '.Infos .Weather, .Infos .Weather #weather' ).css( 'border-color', newval );
        } );
    } );


} )( jQuery );