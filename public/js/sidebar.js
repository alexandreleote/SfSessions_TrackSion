export function initSidebar() {
    // Gestion de l'item actif dans la sidebar
    const currentPath = window.location.pathname;
    const navItems = document.querySelectorAll('.navbar-item a');
    
    navItems.forEach(item => {
      // Vérifie si le chemin actuel correspond à l'href du lien
      if (item.getAttribute('href') === currentPath) {
        item.classList.add('active');
      }
      
      // Ajoute l'effet de hover
      item.addEventListener('mouseenter', () => {
        item.style.transform = 'translateX(4px)';
      });
      
      item.addEventListener('mouseleave', () => {
        item.style.transform = 'translateX(0)';
      });
    });
}