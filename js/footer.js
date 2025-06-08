export async function loadFooter() {
    const footerElement = document.querySelector('footer.footer');
    if (footerElement) {
        try {
            const response = await fetch('footer.html');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const footerHtml = await response.text();
            footerElement.innerHTML = footerHtml;
            const yearSpan = footerElement.querySelector('#current-year');
            if (yearSpan) {
                yearSpan.textContent = new Date().getFullYear();
            }
        } catch (error) {
            console.error('Could not load footer: ', error);
            footerElement.innerHTML = "<div class='container'><p style='text-align:center; padding: 20px; color: #9ca3af;'>Footer could not be loaded.</p></div>";
        }
    }
}
