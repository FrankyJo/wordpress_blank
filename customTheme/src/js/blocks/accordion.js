window.addEventListener('DOMContentLoaded', () => {
    const accordion = document.querySelector('.box-accordion');
    const elemTitle = accordion.querySelectorAll('.accordion-title');
    const elemContent = [...accordion.querySelectorAll('.accordion-content')];
    accordion.addEventListener('click', change);

    function change(event) {
        const targ = event.target;
        if (targ.classList.contains('active')) return;
        if (targ.classList.contains('close')) {
            targ.classList.remove('close');
            showText(targ.nextElementSibling);
        } else {
            targ.classList.add('close');
            closeText(targ.nextElementSibling);
        }
    }

    function closeText(textEl) {
        textEl.style.height = '0px';
    }
    function showText(textEl) {
        textEl.style.height = textEl.scrollHeight + 'px';
    }

    // resize
    let timeout = false;
    const delay = 200;
    let calls = 0;

    function getDimensions() {
        calls += 1;
        elemContent.forEach((item) => {
            if (item.clientHeight > 0) {
                item.style.height = `${item.firstElementChild.clientHeight}px`;
            }
        });
    }
    window.addEventListener('resize', () => {
        clearTimeout(timeout);
        timeout = setTimeout(getDimensions, delay);
    });

    getDimensions();
});
