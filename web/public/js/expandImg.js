(function () {
    let nav = document.querySelector('.navbar');

    document.addEventListener('click', expandImage);

    function expandImage(e) {
        if (e.target.classList.contains('expandable') && e.target.tagName == 'IMG') {
            document.body.classList.toggle('overflow-hidden');
            e.target.parentNode.classList.toggle('expanded');
            nav.classList.toggle('fixed-top');
        }
    }
}())