export function initializeSmoothScrolling() {
    const navLinks = document.querySelectorAll('.nav-link');
    const heroButton = document.querySelector('.hero .btn-primary[href="#contact"]');
    const navMenu = document.getElementById('nav-menu');
    const navToggle = document.getElementById('nav-toggle');
    const headerHeight = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--header-height')) || 80;

    const handleLinkClick = (e, linkElement) => {
        const targetHref = linkElement.getAttribute('href');
        const isIndexPage = window.location.pathname === '/' || window.location.pathname.endsWith('index.html') || window.location.pathname === '';

        if (targetHref && targetHref.includes('#') && (targetHref.startsWith('#') || (isIndexPage && targetHref.startsWith('index.html#')))) {
            const targetId = targetHref.substring(targetHref.indexOf('#'));
            if (isIndexPage && targetId.length > 1) {
                e.preventDefault();
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    const targetPosition = targetElement.offsetTop - headerHeight - 10;
                    window.scrollTo({ top: targetPosition, behavior: 'smooth' });
                }
            }
        }
        if (navMenu && navMenu.classList.contains('active')) {
            navMenu.classList.remove('active');
            if (navToggle) {
                const icon = navToggle.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        }
    };

    navLinks.forEach(link => {
        link.addEventListener('click', (e) => handleLinkClick(e, link));
    });
    if (heroButton) {
        heroButton.addEventListener('click', (e) => handleLinkClick(e, heroButton));
    }
}

export function initializeScrollAnimations() {
    const animatedElements = document.querySelectorAll('.fade-in, .slide-up');
    if (animatedElements.length === 0) return;

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        animatedElements.forEach(el => { observer.observe(el); });
    } else {
        animatedElements.forEach(el => { el.classList.add('visible'); });
    }
}

export function initializeActiveNavLinkHighlighting() {
    const isIndexPageForNav = window.location.pathname === '/' || window.location.pathname.endsWith('index.html') || window.location.pathname === '';
    if (!isIndexPageForNav) return;

    const sections = document.querySelectorAll('main section[id]');
    const navLinksForHighlight = document.querySelectorAll('#nav-menu a.nav-link[href^="#"], #nav-menu a.nav-link[href^="index.html#"]');
    const headerHeight = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--header-height')) || 80;

    if (sections.length === 0 || navLinksForHighlight.length === 0) return;

    const setActiveLink = () => {
        let currentSectionId = '';
        const scrollY = window.pageYOffset;
        const buffer = headerHeight + 50;
        const homeSection = document.getElementById('home');
        if (homeSection && scrollY < (homeSection.offsetTop + homeSection.offsetHeight - buffer)) {
            currentSectionId = 'home';
        } else {
            sections.forEach(section => {
                const sectionTop = section.offsetTop - buffer;
                const sectionHeight = section.offsetHeight;
                const sectionId = section.getAttribute('id');
                if (sectionId !== 'home' && scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                    currentSectionId = sectionId;
                }
            });
        }
        if (!currentSectionId && (window.innerHeight + scrollY) >= document.body.offsetHeight - 150) {
            currentSectionId = 'contact';
        }
        if (!currentSectionId && scrollY < buffer) {
            currentSectionId = 'home';
        }
        navLinksForHighlight.forEach(link => {
            link.classList.remove('active');
            const linkHref = link.getAttribute('href');
            const linkHrefId = linkHref ? linkHref.substring(linkHref.indexOf('#') + 1) : null;
            if (linkHrefId && linkHrefId === currentSectionId) {
                link.classList.add('active');
            }
        });
    };

    let scrollTimeout;
    window.addEventListener('scroll', () => {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(setActiveLink, 150);
    });
    setActiveLink();
}
