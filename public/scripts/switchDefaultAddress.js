document.addEventListener('DOMContentLoaded', function () {
    const radioButtons = document.querySelectorAll('.default-address input');
    let lastCheckedRadio = document.querySelector('.default-address input:checked');

    radioButtons.forEach(function (radioButton) {
        radioButton.addEventListener('click', function (event) {
            const parentAddress = event.target.closest('.address');
            const byDefaultText = parentAddress.querySelector('#default-address');

            if (event.target === lastCheckedRadio) {
                event.target.checked = false;
                lastCheckedRadio = null;
                parentAddress.classList.remove('default');
                byDefaultText.style.display = 'none';

            } else {
                if (lastCheckedRadio) {
                    const address = lastCheckedRadio.closest('.address');
                    address.classList.remove('default');
                    address.querySelector('#default-address').style.display = 'none';
                }
                lastCheckedRadio = event.target;
                parentAddress.classList.add('default');
                byDefaultText.style.display = 'block';
            }

            // Désactiver tous les boutons radio
            radioButtons.forEach(btn => btn.disabled = true);

            // Réactiver les boutons radio après une demi-seconde
            setTimeout(() => {
                radioButtons.forEach(btn => btn.disabled = false);
            }, 500);
            const formData = new FormData();
            formData.append('selectedAddress', event.target.value);

            fetch('/profile/addresses/changeDefaultAddress', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => console.log(data))
                .catch(error => console.error('Error:', error));
        });
    });
});
