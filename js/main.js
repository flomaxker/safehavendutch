import { loadHeader } from './header.js';
import { loadFooter } from './footer.js';
import { initializeMobileNavigation } from './nav.js';
import { initializeSmoothScrolling, initializeScrollAnimations, initializeActiveNavLinkHighlighting } from './scroll.js';
import { initializeTestimonialCarousel } from './carousel.js';
import { initializeContactForm } from './contact.js';
import { initializeFeelingsFlags } from './feelings-flags.js';
import { initializeGroningenChecklist } from './checklist.js';

document.addEventListener('DOMContentLoaded', () => {
    loadHeader();
    loadFooter();
    initializeMobileNavigation();
    initializeSmoothScrolling();
    initializeScrollAnimations();
    initializeActiveNavLinkHighlighting();
    initializeTestimonialCarousel();
    initializeContactForm();
    initializeFeelingsFlags();
    initializeGroningenChecklist();
});
