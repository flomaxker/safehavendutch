export async function loadHeader() {
    const headerElement = document.querySelector('header.header');
    if (!headerElement) return;
    try {
        const response = await fetch('header.html');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const headerHtml = await response.text();
        headerElement.innerHTML = headerHtml;

        setActiveNavLink(headerElement);
    } catch (error) {
        console.error('Could not load header: ', error);
        headerElement.innerHTML = "<div class='container'><p style='text-align:center; padding: 20px; color: #9ca3af;'>Header could not be loaded.</p></div>";
    }
}

function setActiveNavLink(headerElement) {
    const bodyId = document.body.id;
    let selector = '';
    switch (bodyId) {
        case 'page-blog':
            selector = 'a[href="/blog/"]';
            break;
        case 'page-feelings-flags':
            selector = 'a[href="feelings-flags.html"]';
            break;
        case 'page-checklist':
            selector = 'a[href="integration-checklist.html"]';
            break;
        default:
            return;
    }
    const link = headerElement.querySelector(selector);
    if (link) {
        link.classList.add('active');
    }
}
