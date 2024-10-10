/**
 * Scroll all schedule from the bottom to the top
 */
$('.ticker1').easyTicker({
    direction: 'up',
    easing: 'swing',
    speed: 'slow',
    interval: 12000,
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
