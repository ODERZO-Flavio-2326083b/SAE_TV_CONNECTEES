let scrollSpeed = SCROLL_SETTINGS.scrollSpeed ?? 12;

/**
 * Scroll all schedule from the bottom to the top
 */
$('.ticker1').easyTicker({
    direction: 'up',
    easing: 'swing',
    speed: 'slow',
    interval: scrollSpeed,
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

console.log(scrollSpeed);
