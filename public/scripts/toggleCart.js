const toggleButton = document.getElementById('toggle-button');
const tableContainer = document.getElementById('table-container')
const table = document.getElementById('table');
const arrowImage = toggleButton.querySelector('img');
document.addEventListener('DOMContentLoaded', function() {

    // Décommenter si par défaut ouvert
    //openTable()

    toggleButton.addEventListener('click', function() {
        if (tableContainer.style.height) {
            collapseTable()
        }
        else {
            openTable()
        }
    });
});

function collapseTable() {
    tableContainer.style.height = '';
    arrowImage.classList.remove('rotated');
}

function openTable() {
    tableContainer.style.height = table.scrollHeight + 'px';
    arrowImage.classList.add('rotated');
}