(function () {
    const toggle = document.querySelector('[data-nav-toggle]');
    const nav = document.getElementById('primary-nav');

    if (!toggle || !nav) {
        return;
    }

    const setState = (expanded) => {
        toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        nav.classList.toggle('app-nav--open', expanded);
    };

    const handleResize = () => {
        setState(window.innerWidth >= 768);
    };

    toggle.addEventListener('click', () => {
        const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
        setState(!isExpanded);
    });

    document.addEventListener('keyup', (event) => {
        if (event.key === 'Escape') {
            setState(false);
            toggle.focus();
        }
    });

    window.addEventListener('resize', handleResize);
    handleResize();
})();
