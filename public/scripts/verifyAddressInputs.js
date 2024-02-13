document.addEventListener('DOMContentLoaded', function() {
    const firstNameInput = document.querySelector('#firstName input');
    const lastNameInput = document.querySelector('#lastName input');
    const phoneNumberInput = document.querySelector('#phoneNumber input');
    const addressInput = document.querySelector('#address input')
    const zipCodeInput = document.querySelector('#postalCode input');
    const cityInput = document.querySelector('#city input');

    // Fonction pour autoriser seulement les lettres (pas d'espaces, mais incluant les tirets)
    function allowOnlyLetters(event) {
        if (!/^[a-zA-Z\u00C0-\u024F-]+$/.test(event.key)) {
            event.preventDefault();
        }
    }

    function allowOnlyLettersAndSpaces(event) {
        if (!/^[a-zA-Z\u00C0-\u024F-]+$/.test(event.key) && event.key !== ' ') {
            event.preventDefault();
        }
    }

    // Fonction pour bloquer les caractères spéciaux
    function blockSpecialCharacters(event) {
        // Autorise uniquement les lettres, les chiffres et les espaces
        if (!/^[a-zA-Z0-9\u00C0-\u024F ]+$/.test(event.key)) {
            event.preventDefault();
        }
    }

    // Fonction pour autoriser seulement les chiffres (pas d'espaces)
    function allowOnlyDigits(event) {
        if (!/\d/.test(event.key)) {
            event.preventDefault();
        }
    }

    // Fonction pour autoriser les chiffres et les espaces
    function allowOnlyDigitsAndSpaces(event) {
        if (!/\d/.test(event.key) && event.key !== ' ') {
            event.preventDefault();
        }
    }

    // Appliquer les fonctions aux champs appropriés
    firstNameInput.addEventListener('keypress', allowOnlyLetters);
    lastNameInput.addEventListener('keypress', allowOnlyLetters);
    phoneNumberInput.addEventListener('keypress', allowOnlyDigitsAndSpaces);
    addressInput.addEventListener('keydown', blockSpecialCharacters);
    zipCodeInput.addEventListener('keypress', allowOnlyDigits);
    cityInput.addEventListener('keypress', allowOnlyLettersAndSpaces);
});


