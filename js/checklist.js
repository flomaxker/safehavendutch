export function initializeGroningenChecklist() {
    const checklistPageContainer = document.getElementById('page-checklist');
    if (!checklistPageContainer) return;

    const checklistContainer = document.querySelector('.checklist-container');
    const progressBar = document.querySelector('.progress-bar-container .progress-bar');
    const progressText = document.querySelector('.progress-bar-container .progress-text');

    if (!checklistContainer || !progressBar || !progressText) return;

    const checklistItems = checklistContainer.querySelectorAll('.checklist-item');
    const totalItems = checklistItems.length;
    const storagePrefix = 'groningenChecklist_';
    if (totalItems === 0) return;

    const updateProgress = () => {
        const checkedInputs = checklistContainer.querySelectorAll('input[type="checkbox"]:checked');
        const checkedItemsCount = checkedInputs.length;
        const percentage = totalItems > 0 ? (checkedItemsCount / totalItems) * 100 : 0;
        progressBar.style.width = `${percentage}%`;
        progressText.textContent = `${checkedItemsCount} / ${totalItems} Completed (${Math.round(percentage)}%)`;
    };

    const handleCheckboxChange = (checkbox) => {
        const itemElement = checkbox.closest('.checklist-item');
        if (!itemElement) return;

        const itemId = itemElement.getAttribute('data-item-id');
        const detailsElement = itemElement.querySelector('.checklist-details');

        if (checkbox.checked) {
            itemElement.classList.add('checked');
            if (detailsElement) detailsElement.style.display = 'block';
            if (itemId) localStorage.setItem(storagePrefix + itemId, 'checked');
        } else {
            itemElement.classList.remove('checked');
            if (detailsElement) detailsElement.style.display = 'none';
            if (itemId) localStorage.removeItem(storagePrefix + itemId);
        }
        updateProgress();
    };

    checklistItems.forEach(item => {
        const checkbox = item.querySelector('input[type="checkbox"]');
        const detailsElement = item.querySelector('.checklist-details');
        const itemId = item.getAttribute('data-item-id');

        if (checkbox && itemId) {
            if (localStorage.getItem(storagePrefix + itemId) === 'checked') {
                checkbox.checked = true;
                item.classList.add('checked');
                if (detailsElement) detailsElement.style.display = 'block';
            } else {
                checkbox.checked = false;
                item.classList.remove('checked');
                if (detailsElement) detailsElement.style.display = 'none';
            }
            checkbox.addEventListener('change', (event) => {
                handleCheckboxChange(event.target);
            });
        }
    });
    updateProgress();
}
