function updateCartBadges(itemCount) {
    document.querySelectorAll('[data-cart-badge]').forEach((badge) => {
        if (itemCount <= 0) {
            badge.textContent = '';
            badge.classList.add('hidden');
            return;
        }

        badge.textContent = itemCount > 9 ? '9+' : String(itemCount);
        badge.classList.remove('hidden');
    });
}

function replaceCartDropdownContent(html) {
    const container = document.getElementById('header-cart-dropdown-content');
    if (! container || ! html) {
        return;
    }

    container.innerHTML = html;
}

async function refreshCartDropdown() {
    try {
        const response = await window.axios.get('/cart/preview', {
            headers: { Accept: 'application/json' },
        });

        updateCartBadges(response.data?.itemCount ?? 0);
        replaceCartDropdownContent(response.data?.html ?? '');
    } catch {
        // Keep existing dropdown content if refresh fails.
    }
}

export function initCartDropdown() {
    window.addEventListener('repomart:cart-updated', (event) => {
        const itemCount = event.detail?.itemCount;

        if (typeof itemCount === 'number') {
            updateCartBadges(itemCount);
        }

        if (event.detail?.html) {
            replaceCartDropdownContent(event.detail.html);

            return;
        }

        refreshCartDropdown();
    });
}

export { updateCartBadges, replaceCartDropdownContent, refreshCartDropdown };
