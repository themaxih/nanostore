function onlyLetters(string) {
    return string.replace(/[^a-zA-Z\u00C0-\u00FF^-]/g, '');
}

const ids = [
    "registration_form_firstName",
    "registration_form_lastName"
];

for (let id of ids) {
    const element = document.getElementById(id);
    element.addEventListener('input', function (e) {
        e.target.value = onlyLetters(e.target.value);
    });
}