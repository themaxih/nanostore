document.querySelectorAll('.quantity-select').forEach(select => {
    select.addEventListener('change', function() {
        const productId = this.dataset.productId;
        const quantity = this.value;

        fetch('/cart/update-cart-quantity', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest', // Important pour Symfony afin de détecter une requête AJAX
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                productId: productId,
                quantity: quantity
            })
        })
            .then(response => {
                if (response.statusText === "Unauthorized") {
                    window.location.href = "/login";
                }
                return response.json();
            })
            .then(data => {
                console.log(data);
                if (data.status === 'success') {
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
    });
});
