let scrollSpeed = parseInt(SCROLL_SETTINGS.scrollSpeed)|| 12;
let adjustedSpeed = scrollSpeed * 1000;



/**
 * Scroll all schedule from the bottom to the top
 */
$('.ticker1').easyTicker({
    direction: 'up',
    easing: 'swing',
    speed: 'slow',
    interval: adjustedSpeed,
    height: 'auto',
    visible: 0,
    mousePause: 1,
    controls: {
        up: '',
        down: '',
        toggle: '',
        playText: 'Play',
        stopText: 'Stop'
    }
});

