const toggleButton = document.querySelectorAll('.toggle-button');
document.addEventListener('DOMContentLoaded', function() {
    toggleButton.forEach(button => {
        button.addEventListener('click', function() {
            const arrowImage = button.querySelector('img');
            const orderTable = button.closest('.order').querySelector('.order-content');

            if (orderTable.style.height) {
                orderTable.style = '';
                arrowImage.classList.remove('rotated');
            }
            else {
                orderTable.style.padding = '24px';
                orderTable.style.height = orderTable.scrollHeight + 24 + 'px';
                arrowImage.classList.add('rotated');
            }
        })
    })
});