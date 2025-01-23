export function openModal(modal) {
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

export function closeModal(modal) {
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

export function initModalHandlers() {
    const modalButtons = document.querySelectorAll('[data-modal-target]');
    const closeButtons = document.querySelectorAll('.btn-annuler');

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

    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal-container')) {
            closeModal(event.target);
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const openModal = document.querySelector('.modal-container[style="display: block;"]');
            if (openModal) {
                closeModal(openModal);
            }
        }
    });
}

export const initCollectionForm = () => {
    const addFormToCollection = (e) => {
        const collectionHolder = document.querySelector('.programmes');
        const item = document.createElement('div');
        item.classList.add('programme-item');
        
        const prototype = collectionHolder.dataset.prototype;
        const index = collectionHolder.dataset.index;
        item.innerHTML = prototype.replace(/__name__/g, index);
        
        // Add delete button
        const deleteButton = document.createElement('button');
        deleteButton.innerHTML = '❌';
        deleteButton.classList.add('btn-delete');
        deleteButton.addEventListener('click', (e) => {
            e.preventDefault();
            item.remove();
        });
        
        item.appendChild(deleteButton);
        collectionHolder.appendChild(item);
        collectionHolder.dataset.index = parseInt(index) + 1;
    };

    document.addEventListener('DOMContentLoaded', () => {
        const addButton = document.createElement('button');
        addButton.innerHTML = 'Ajouter un cours';
        addButton.classList.add('btn', 'btn-primary', 'add-cours');
        
        const collectionHolder = document.querySelector('.programmes');
        if (collectionHolder) {
            collectionHolder.dataset.index = collectionHolder.children.length;
            collectionHolder.after(addButton);
            addButton.addEventListener('click', addFormToCollection);
        }
    });
};