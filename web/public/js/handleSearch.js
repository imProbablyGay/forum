let moreSearch = document.querySelectorAll('.search-block > .row');
let defaultLimit = 4;
let limit = defaultLimit;
let type;

moreSearch.forEach(el => {
    el.addEventListener('click', showMoreSearches);
})

function showMoreSearches(e) {
    if (e.target.classList.contains('search-span')) {
        // check type of search
        let classType = getDataset(this, 'data-search-type');

        e.target.parentElement.remove();
        let getArg = window.location.search.substring(1).split('=')[1];
        let userID = window.location.href.split('/profile/')[1];

        sendJSON('../ajax/get_more_searches.php', {"query": getArg, "limit": limit, "type": classType, "user_id": userID})
        .then(data => data.text())
        .then(data => {
            limit += defaultLimit;
            this.innerHTML += data;
        })
    }
}

// delete answer
document.addEventListener('click', handleDelete);