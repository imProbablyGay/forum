function addNavbarMoving() {
    if (window.innerWidth <= 991) {
        let menu = document.querySelector('.navbar-toggler');
        let menuBox = document.querySelector('.navbar-collapse');
        let menuBoxItems = document.querySelectorAll('.nav-link');
        
        menuBoxItems.forEach(current => {
            current.onclick = () => {
                menuBox.classList.remove('show');
                document.body.classList.remove('overflow-hidden');
                menu.classList.toggle('_active');
            }
        });
        
        menu.onclick = menuOpen;
        
        function menuOpen(e) {
                menu.disabled = true;
                menuBox.ontransitionend = () => {
                    menu.disabled = false;
                }
            
                e.preventDefault();
                document.body.classList.toggle('overflow-hidden');
                menu.classList.toggle('_active');
            }
        
    }
}
addNavbarMoving();

// add resize event
window.addEventListener('resize' , addNavbarMoving);


// live search
let search = document.querySelector('.navbar__search > input');
let searchBlock = search.nextElementSibling;

document.addEventListener('click', handleSearch);

function handleSearch(e) {
    // hide searches
    if (!e.target.classList.contains('navbar__search-result') || !e.target == search) {
        searchBlock.innerHTML = '';
    }

    // set events
    search.onblur = () => search.value = '';
    search.onkeydown = () => {
        setTimeout(() => {
            searchBlock.innerHTML = '';
            let value = search.value.trim();

            // check length
            if (value.trim().length == 0) {
                searchBlock.innerHTML = '';
                return;
            }

            getMatches(value)
                .then(data => {
                    searchBlock.innerHTML = data
                });
        }, 0);
    }
}

function getMatches(inp) {
    // get matches from database
    return sendJSON('../ajax/get_search_match.php', {"value": inp})
        .then(data => data.json())
        .then(data => drawHint(data,inp))
}

function drawHint(data,inp) {
    if (data.length == 0) return '';
    let matches = '';

    for (let item of data) {
        // mark match and form elem
        let searchTitle = item['title'].replace(inp, '<mark>'+inp+'</mark>');
        let link = `/question/${item['id']}`;
        matches += `<div class="search__result">
                        <a href="${link}">${searchTitle}</a>
                    </div>`;
    }

    matches += `<div class='search__show-all'><a href='/search?q=${inp}'>Посмотреть все</a></div>`;

    return matches;
}