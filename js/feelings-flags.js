export function initializeFeelingsFlags() {
    const feelingsContainer = document.querySelector('.flags-container');
    const feelingDisplayArea = document.getElementById('selected-feeling-display');
    if (!feelingsContainer || !feelingDisplayArea) return;

    const dutchWordEl = feelingDisplayArea.querySelector('#display-dutch-word');
    const dutchPhraseEl = feelingDisplayArea.querySelector('#display-dutch-phrase');
    const childMessageEl = feelingDisplayArea.querySelector('#display-child-message');
    const parentTipContainerEl = feelingDisplayArea.querySelector('#display-parent-tip');
    const parentTipH4El = parentTipContainerEl ? parentTipContainerEl.querySelector('h4') : null;
    const parentTipPEl = parentTipContainerEl ? parentTipContainerEl.querySelector('p') : null;

    const allFeelingFlags = feelingsContainer.querySelectorAll('.feeling-flag');

    feelingsContainer.addEventListener('click', (event) => {
        const clickedFlag = event.target.closest('.feeling-flag');
        if (!clickedFlag) return;

        allFeelingFlags.forEach(flag => flag.classList.remove('selected'));
        clickedFlag.classList.add('selected');

        const feelingName = clickedFlag.dataset.feeling || '';
        const dutchWord = clickedFlag.dataset.dutch || '';
        const dutchPhrase = clickedFlag.dataset.dutchPhrase || '';
        const childMessage = clickedFlag.dataset.childMessage || '';
        const parentTip = clickedFlag.dataset.parentTip || '';

        if (dutchWordEl) dutchWordEl.textContent = dutchWord;
        if (dutchPhraseEl) dutchPhraseEl.textContent = dutchPhrase;
        if (childMessageEl) childMessageEl.innerHTML = childMessage;

        if (parentTipH4El) parentTipH4El.textContent = feelingName ? `Tip for Parents when your child feels ${feelingName.toLowerCase()}:` : 'Tip for Parents:';
        if (parentTipPEl) parentTipPEl.textContent = parentTip;

        feelingDisplayArea.classList.remove('hidden-display');

        setTimeout(() => {
            const headerHeight = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--header-height')) || 80;
            const targetPosition = feelingDisplayArea.offsetTop - headerHeight - 20;
            window.scrollTo({
                top: targetPosition >= 0 ? targetPosition : 0,
                behavior: 'smooth'
            });
        }, 100);
    });
}
