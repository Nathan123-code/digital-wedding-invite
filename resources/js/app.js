const guestSearch = document.querySelector('[data-guest-search]');
const searchResults = document.querySelector('[data-search-results]');
const autoUploadInput = document.querySelector('[data-auto-upload]');

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
