document.addEventListener('DOMContentLoaded', function() {
    const searchSort = document.querySelector('#search-sort');
    searchSort.addEventListener('change', function () {
        const selectedIndex = searchSort.options.selectedIndex;
        const selectedOption = searchSort[selectedIndex];
        window.location.href = selectedOption.dataset.url;
    });
});