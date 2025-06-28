'use strict';

// Wait for the DOM to be fully loaded before running scripts
document.addEventListener('DOMContentLoaded', async () => {
    // --- Call initialization functions ---
    initializeMobileNavigation();
    initializeSmoothScrolling();
    initializeContactForm();
});

// --- Mobile Navigation Toggle ---
function initializeMobileNavigation() {
    const navToggle = document.getElementById('nav-toggle');
    const navMenu = document.getElementById('nav-menu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            const icon = navToggle.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            }
        });

        document.addEventListener('click', e => {
            const isClickInsideNav = navMenu.contains(e.target);
            const isClickOnToggle = navToggle.contains(e.target);

            if (!isClickInsideNav && !isClickOnToggle && navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
                const icon = navToggle.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });
    }
}

// --- Smooth Scrolling & Mobile Menu Close for Nav Links & Hero Button ---
function initializeSmoothScrolling() {
    const navLinks = document.querySelectorAll('.nav-link');
    const heroButton = document.querySelector('.hero .btn-primary[href="#contact"]');
    const navMenu = document.getElementById('nav-menu'); // For closing mobile menu
    const navToggle = document.getElementById('nav-toggle'); // For resetting icon
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
        });
    }

    navLinks.forEach(link => {
        link.addEventListener('click', (e) => handleLinkClick(e, link));
    });

    if (heroButton) {
        heroButton.addEventListener('click', (e) => handleLinkClick(e, heroButton));
    }
}

// --- Contact Form AJAX Submission ---
function initializeContactForm() {
    const form = document.getElementById('contact-form');
    const feedback = document.getElementById('form-feedback');

    if (!form || !feedback) return;

    form.addEventListener('submit', e => {
        e.preventDefault();
        feedback.textContent = 'Sending...';
        feedback.style.color = 'var(--color-white)';

        const nameInput = form.querySelector('[name="name"]');
        const emailInput = form.querySelector('[name="email"]');
        const messageInput = form.querySelector('[name="message"]');

        const name = nameInput ? nameInput.value.trim() : '';
        const email = emailInput ? emailInput.value.trim() : '';
        const message = messageInput ? messageInput.value.trim() : '';
        let errors = [];

        if (!name) errors.push("Name is required.");
        if (!email || !/^\S+@\S+\.\S+$/.test(email)) errors.push("Valid email is required.");
        if (!message) errors.push("Message is required.");

        if (errors.length > 0) {
            feedback.textContent = errors.join(' ');
            feedback.style.color = '#f87171';
            return;
        }

        fetch('contact-handler.php', {
            method: 'POST',
            body: new FormData(form)
        })
        .then(response => {
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                return response.json().then(data => {
                    if (!response.ok) {
                        throw data;
                    }
                    return data;
                });
            } else {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(text || `Server error: ${response.status}`);
                    });
                }
                return response.text();
            }
        })
        .then(data => {
            if (data && data.success) {
                feedback.textContent = 'Thanks for your message! I\'ll be in touch soon.';
                feedback.style.color = 'var(--color-secondary)';
                form.classList.add('sent');
                feedback.classList.add('sent');
                setTimeout(() => {
                    form.classList.remove('sent');
                    feedback.classList.remove('sent');
                    feedback.textContent = '';
                    form.reset();
                }, 1500);
            } else if (data && data.errors) {
                feedback.textContent = data.errors.join(' ');
                feedback.style.color = '#f87171';
            } else {
                feedback.textContent = 'An error occurred. Please try again.';
                feedback.style.color = '#f87171';
            }
        })
        .catch((error) => {
            console.error('Form Submission Error:', error);
            let errorMessage = "Submission failed. Please try WhatsApp/Email.";
            if (error.message) {
                 errorMessage = `Submission failed: ${error.message}. Please try WhatsApp/Email.`;
            } else if (error.errors && Array.isArray(error.errors)) {
                 errorMessage = `Submission failed: ${error.errors.join(' ')}. Please try WhatsApp/Email.`;
            }
            feedback.textContent = errorMessage;
            feedback.style.color = '#f87171';
        });
    });
}
