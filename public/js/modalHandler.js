export function openModal(modal) {
    // Afficher la modale avec display flex
    modal.style.display = 'flex';
    modal.style.justifyContent = 'center';
    modal.style.alignItems = 'center';
    
    // Empêcher le scroll du body
    document.body.style.overflow = 'hidden';
}

export function closeModal(modal) {
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

export function initModalHandlers() {
    const modalButtons = document.querySelectorAll('[data-modal-target]');
    const closeButtons = document.querySelectorAll('.btn-annuler, [data-close-button]');

    modalButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-target');
            const modal = document.getElementById(modalId);
            if (modal) {
                openModal(modal);
            }
        });
    });

    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal-container');
            if (modal) {
                closeModal(modal);
            }
        });
    });

    // Fermeture en cliquant en dehors de la modale
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal-container')) {
            closeModal(event.target);
        }
    });

    // Fermeture avec la touche Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const openModal = document.querySelector('.modal-container.active');
            if (openModal) {
                closeModal(openModal);
            }
        }
    });
}