const guestSearch = document.querySelector('[data-guest-search]');
const searchResults = document.querySelector('[data-search-results]');
const autoUploadInput = document.querySelector('[data-auto-upload]');
const copyHashtagButton = document.querySelector('[data-copy-hashtag]');
const openingScreen = document.querySelector('[data-opening-screen]');
const openInvitationButton = document.querySelector('[data-open-invitation]');
const invitationOpenedKey = 'wedding-invitation-opened';
const pageTransitionLinks = document.querySelectorAll('[data-page-transition]');

document.body.classList.remove('page-exiting');

if (openingScreen && openInvitationButton) {
    if (sessionStorage.getItem(invitationOpenedKey) === 'true') {
        openingScreen.remove();
    } else {
        document.body.classList.add('invitation-locked');
        openInvitationButton.focus();
    }

    openInvitationButton.addEventListener('click', () => {
        sessionStorage.setItem(invitationOpenedKey, 'true');
        openingScreen.classList.add('is-opening');
        document.body.classList.add('invitation-opening');

        window.setTimeout(() => {
            openingScreen.remove();
            document.body.classList.remove('invitation-locked', 'invitation-opening');
        }, 700);
    });
}

pageTransitionLinks.forEach((link) => {
    link.addEventListener('click', (event) => {
        if (
            event.defaultPrevented ||
            event.button !== 0 ||
            event.metaKey ||
            event.ctrlKey ||
            event.shiftKey ||
            event.altKey ||
            link.target === '_blank' ||
            link.href === window.location.href
        ) {
            return;
        }

        event.preventDefault();
        document.body.classList.add('page-exiting');

        window.setTimeout(() => {
            window.location.assign(link.href);
        }, 260);
    });
});

if (copyHashtagButton) {
    copyHashtagButton.addEventListener('click', async (event) => {
        event.preventDefault();

        const hashtag = copyHashtagButton.dataset.copyHashtag;

        if (!hashtag) {
            return;
        }

        try {
            await navigator.clipboard.writeText(hashtag);
        } catch {
            const fallbackInput = document.createElement('textarea');
            fallbackInput.value = hashtag;
            fallbackInput.setAttribute('readonly', '');
            fallbackInput.style.position = 'fixed';
            fallbackInput.style.opacity = '0';
            document.body.append(fallbackInput);
            fallbackInput.select();
            document.execCommand('copy');
            fallbackInput.remove();
        }

        copyHashtagButton.classList.add('copied');
        window.setTimeout(() => copyHashtagButton.classList.remove('copied'), 1800);
    });
}

if (autoUploadInput) {
    autoUploadInput.addEventListener('change', () => {
        if (autoUploadInput.files?.length) {
            autoUploadInput.form?.submit();
        }
    });
}

if (guestSearch && searchResults) {
    const guests = JSON.parse(guestSearch.dataset.guests || '[]');

    guestSearch.addEventListener('input', () => {
        const query = guestSearch.value.trim().toLocaleLowerCase();
        searchResults.replaceChildren();

        if (!query) {
            return;
        }

        const matches = guests.filter(({ name }) =>
            name.toLocaleLowerCase().includes(query),
        );

        if (matches.length === 0) {
            const emptyState = document.createElement('p');
            emptyState.className = 'search-empty';
            emptyState.textContent = 'No matching guest found.';
            searchResults.append(emptyState);
            return;
        }

        matches.forEach(({ name, table, seat }) => {
            const result = document.createElement('div');
            result.className = 'seat-item';

            const guestName = document.createElement('span');
            guestName.className = 'guest-name';
            guestName.textContent = name;

            const guestTable = document.createElement('span');
            guestTable.className = 'guest-meta';
            guestTable.textContent = seat ? `${table} · Seat ${seat}` : table;

            result.append(guestName, guestTable);
            searchResults.append(result);
        });
    });
}
