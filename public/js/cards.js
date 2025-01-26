export function initCards() {
    const cards = document.querySelectorAll('.home-section-content');
    
    cards.forEach(card => {
      // Animation au hover
      card.addEventListener('mouseenter', () => {
        card.style.transform = 'translateY(-2px)';
        card.style.borderColor = 'var(--accent-primary)';
      });
      
      card.addEventListener('mouseleave', () => {
        card.style.transform = 'translateY(0)';
        card.style.borderColor = 'rgba(139, 92, 246, 0.1)';
      });
    });
}