export function handleProgrammeCollection() {
    const addButton = document.querySelector('.add-programme');
    const collection = document.querySelector('.programmes');
    const form = collection?.closest('form');
    let selectedCourses = new Set();
    

    // Initialize existing selections
    if (collection) {
        collection.querySelectorAll('select').forEach(select => {
            if (select.value) {
                selectedCourses.add(select.value);
            }
        });
    }

    // Add form submit validation
    form?.addEventListener('submit', (event) => {
        const selects = collection.querySelectorAll('select');
        const values = new Set();
        
        // Check for duplicates
        let hasDuplicate = false;
        selects.forEach(select => {
            if (select.value) {
                if (values.has(select.value)) {
                    hasDuplicate = true;
                }
                values.add(select.value);
            }
        });

        if (hasDuplicate) {
            event.preventDefault();
            alert('Des cours sont en double. Veuillez vérifier votre sélection.');
            return false;
        }
    });

    if (collection && addButton) {
        let index = collection.children.length;
        
        addButton.addEventListener('click', () => {
            const prototype = collection.dataset.prototype;
            const newForm = prototype.replace(/__name__/g, index);
            const item = document.createElement('div');
            item.classList.add('programme-item');
            item.innerHTML = newForm;
            
            const innerElements = item.querySelectorAll('*');
            innerElements.forEach(element => {
                element.classList.add('programme-flex');
            });

            const select = item.querySelector('select');
            
            // Initial disable of already selected options
            updateSelectElement(select);
            
            // Handle selection changes
            select.addEventListener('change', (event) => {
                const selectedValue = event.target.value;
                if (selectedCourses.has(selectedValue)) {
                    event.preventDefault();
                    alert('Ce cours est déjà sélectionné');
                    select.value = '';
                    return;
                }
                if (select.value) {
                    selectedCourses.add(selectedValue);
                    updateAllSelects();
                }
            });
            
            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.classList.add('btn-delete');
            deleteButton.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            deleteButton.onclick = () => {
                if (select.value) {
                    selectedCourses.delete(select.value);
                }
                item.remove();
                updateAllSelects();
            };
            
            item.appendChild(deleteButton);
            collection.appendChild(item);
            index++;
        });
    }

    function updateSelectElement(select) {
        select.querySelectorAll('option').forEach(option => {
            if (option.value) {
                option.disabled = selectedCourses.has(option.value) && option.value !== select.value;
            }
        });
    }

    function updateAllSelects() {
        collection.querySelectorAll('select').forEach(select => {
            updateSelectElement(select);
        });
    }
}

document.addEventListener('DOMContentLoaded', handleProgrammeCollection);