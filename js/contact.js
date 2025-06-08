export function initializeContactForm() {
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

        if (!name) errors.push('Name is required.');
        if (!email || !/^\S+@\S+\.\S+$/.test(email)) errors.push('Valid email is required.');
        if (!message) errors.push('Message is required.');

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
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.indexOf('application/json') !== -1) {
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
                feedback.textContent = "Thanks for your message! I'll be in touch soon.";
                feedback.style.color = 'var(--color-secondary)';
                form.reset();
                setTimeout(() => { feedback.textContent = ''; }, 5000);
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
            let errorMessage = 'Submission failed. Please try WhatsApp/Email.';
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
