// Navigation interactions

document.addEventListener('DOMContentLoaded', function () {
    const navContainer = document.querySelector('.nav-container');
    const searchToggle = document.querySelector('.nav-link-search');
    const navSearch = document.querySelector('.nav-search');
    const navSearchWrap = document.querySelector('.nav-search-wrap');
    const navSearchInput = document.querySelector('#nav-search-input');

    if (!searchToggle || !navContainer || !navSearch || !navSearchInput) {
        return; // nothing to do
    }

    let isOpen = false;

    const mq = window.matchMedia('(max-width: 768px)');

    function openSearch() {
        if (isOpen) return;
        navContainer.classList.add('search-open');
        // small delay to ensure CSS changes applied before focusing
        setTimeout(() => {
            navSearchInput.focus();
            // move cursor to end
            const val = navSearchInput.value;
            try { navSearchInput.setSelectionRange(val.length, val.length); } catch (e) {}
        }, 50);
        document.addEventListener('click', onDocumentClick);
        document.addEventListener('keydown', onKeyDown);
        isOpen = true;
    }

    function closeSearch() {
        if (!isOpen) return;
        navContainer.classList.remove('search-open');
        document.removeEventListener('click', onDocumentClick);
        document.removeEventListener('keydown', onKeyDown);
        isOpen = false;
    }

    function onDocumentClick(e) {
        // if click is inside the search wrap or on the toggle, ignore
        if (navSearchWrap && navSearchWrap.contains(e.target)) return;
        if (searchToggle && searchToggle.contains(e.target)) return;
        // otherwise close
        closeSearch();
    }

    function onKeyDown(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            closeSearch();
        }
    }

    // Toggle behavior: on mobile intercept clicks and open the inline search instead of navigating
    searchToggle.addEventListener('click', function (ev) {
        // only intercept on mobile sizes; otherwise allow default link behavior
        if (!mq.matches) return; // desktop — let the link navigate
        ev.preventDefault();
        if (isOpen) {
            closeSearch();
        } else {
            openSearch();
        }
    });
});
