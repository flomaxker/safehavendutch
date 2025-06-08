export function initializeTestimonialCarousel() {
    const track = document.querySelector('.testimonial-track');
    const nextButton = document.querySelector('.carousel-btn.next');
    const prevButton = document.querySelector('.carousel-btn.prev');

    if (!track || !nextButton || !prevButton) return;

    const cards = Array.from(track.children);
    if (cards.length === 0) {
        prevButton.style.display = 'none';
        nextButton.style.display = 'none';
        return;
    }

    let currentIndex = 0;
    const cardCount = cards.length;

    const updateCarousel = () => {
        track.style.transform = `translateX(-${currentIndex * 100}%)`;
        updateButtons();
    };

    const updateButtons = () => {
        prevButton.disabled = currentIndex === 0;
        nextButton.disabled = currentIndex === cardCount - 1;
    };

    const moveToCard = (index) => {
        currentIndex = Math.max(0, Math.min(index, cardCount - 1));
        updateCarousel();
    };

    nextButton.addEventListener('click', () => moveToCard(currentIndex + 1));
    prevButton.addEventListener('click', () => moveToCard(currentIndex - 1));

    updateCarousel();
}
