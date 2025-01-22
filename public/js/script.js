document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('.header-mobile');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    /* Modal */
    // Sélectionner tous les boutons qui ouvrent une modal
    const modalButtons = document.querySelectorAll('[data-modal-target]');
    
    modalButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Récupérer l'ID de la modal à partir du data-attribute
            const modalId = this.getAttribute('data-modal-target');
            const modal = document.getElementById(modalId);
            
            if (modal) {
                openModal(modal);
            }
        });
    });

    // Sélectionner tous les boutons de fermeture
    const closeButtons = document.querySelectorAll('.btn-annuler');
    
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal-container');
            if (modal) {
                closeModal(modal);
            }
        });
    });

    // Fermer la modal en cliquant en dehors
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal-container')) {
            closeModal(event.target);
        }
    });

    // Fermer la modal avec la touche Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const openModal = document.querySelector('.modal-container[style="display: block;"]');
            if (openModal) {
                closeModal(openModal);
            }
        }
    });
});

// Fonction pour ouvrir la modal
function openModal(modal) {
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden'; // Empêcher le défilement de la page
}

// Fonction pour fermer la modal
function closeModal(modal) {
    modal.style.display = 'none';
    document.body.style.overflow = ''; // Réactiver le défilement de la page
}