export function smoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {

        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            if (this.getAttribute('href') && this.getAttribute('href').length >= 2) {
                const target = document.querySelector(this.getAttribute('href'));
                const yOffset = -110;
                const y = target.getBoundingClientRect().top + window.pageYOffset + yOffset;
                window.scrollTo({top: y, behavior: 'smooth'});
            }
        });
    });
}
