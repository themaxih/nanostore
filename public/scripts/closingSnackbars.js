const snackbars = document.querySelectorAll(".snackbar");

snackbars.forEach(snackbar => {
    const closeButton = snackbar.querySelector(".close-button");
    if (closeButton) {
        closeButton.addEventListener("click", function () {
            snackbar.classList.add("fadeout");
        });
    }

    snackbar.addEventListener("animationend", (event) => {
        if (event.animationName === 'fadeout') {
            snackbar.classList.remove("show", "fadeout");
        }
    });
})