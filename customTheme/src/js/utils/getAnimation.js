import lottie from 'lottie-web';

export function initLottie(options) {
    return lottie.loadAnimation({
        container: options.element,
        path: options.path,
        renderer: 'svg',
        loop: true,
        autoplay: true,
    });
}
