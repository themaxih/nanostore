document.addEventListener('DOMContentLoaded', function() {
    const cardNumbersInput = document.querySelector('#card-numbers input');
    const expirationInput = document.querySelector('#expiration-date input');
    const cardNameInput = document.querySelector('#card-name input')
    const cscInput = document.querySelector('#csc input');

    // Fonction pour autoriser seulement les lettres, les espaces et les tirets
    function allowOnlyLettersAndSpaces(event) {
        if (!/^[a-zA-Z\u00C0-\u024F-]+$/.test(event.key) && event.key !== ' ') {
            event.preventDefault();
        }
    }


    // Fonction pour autoriser seulement les chiffres (pas d'espaces)
    function allowOnlyDigits(event) {
        if (!/\d/.test(event.key)) {
            event.preventDefault();
        }
    }

    // Fonction pour formater le numéro de la carte
    function formatCardNumber(event) {
        // Suppression des caractères non numériques
        const value = event.target.value.replace(/[^0-9]/g, '');

        // Formatage du numéro de carte
        let formattedValue = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formattedValue += ' ';
            }
            formattedValue += value[i];
        }

        event.target.value = formattedValue;
    }

    // Appliquer les fonctions aux champs appropriés
    cardNumbersInput.addEventListener('input', formatCardNumber);
    expirationInput.addEventListener('click', function () {expirationInput.showPicker()});
    cardNameInput.addEventListener('keypress', allowOnlyLettersAndSpaces);
    cscInput.addEventListener('keypress', allowOnlyDigits);
});


