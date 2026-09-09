const DEBOUNCE_MS = 350;
const VOICE_SEARCH_RESULTS_HINT_KEY = 'repomart_voice_search_results_hint';

let fetchProductsResults = null;

export function initProductsSearch() {
    const form = document.querySelector('[data-products-search]');
    const results = document.querySelector('[data-products-results]');
    const loading = document.querySelector('[data-products-loading]');
    const inlineLoading = document.querySelector('[data-products-search-loading]');
    const badge = document.querySelector('[data-products-total-badge]');

    if (! form || ! results) {
        return;
    }

    const input = form.querySelector('[name="q"]');
    let abortController = null;
    let debounceTimer = null;

    const buildUrl = (pageUrl = null) => {
        if (pageUrl) {
            return pageUrl;
        }

        const params = new URLSearchParams(new FormData(form));

        if (! params.get('q')) {
            params.delete('q');
        }

        if (! params.get('category')) {
            params.delete('category');
        }

        if (! params.get('store')) {
            params.delete('store');
        }

        if (! params.get('nearby')) {
            params.delete('nearby');
        }

        const query = params.toString();

        return query ? `${form.action}?${query}` : form.action;
    };

    const syncFormFromUrl = (url = window.location.href) => {
        const params = new URL(url, window.location.origin).searchParams;

        if (input) {
            input.value = params.get('q') ?? '';
        }

        const category = form.querySelector('[name="category"]');
        const store = form.querySelector('[name="store"]');
        const nearby = form.querySelector('[name="nearby"]');

        if (category instanceof HTMLSelectElement) {
            category.value = params.get('category') ?? '';
        }

        if (store instanceof HTMLSelectElement) {
            store.value = params.get('store') ?? '';
        }

        if (nearby instanceof HTMLInputElement) {
            nearby.checked = params.get('nearby') === '1';
        }
    };

    const updateBadge = (container) => {
        if (! badge) {
            return;
        }

        const meta = container.querySelector('[data-products-results-meta]');
        if (! meta) {
            return;
        }

        const total = Number(meta.dataset.total ?? 0);

        badge.classList.toggle('hidden', total === 0);
        badge.querySelector('[data-products-total-count]')?.replaceChildren(
            document.createTextNode(String(total))
        );

        const label = total === 1 ? 'product' : 'products';
        badge.querySelector('[data-products-total-label]')?.replaceChildren(
            document.createTextNode(label)
        );
    };

    const updateUrl = (url) => {
        const nextUrl = new URL(url, window.location.origin);
        window.history.replaceState(null, '', `${nextUrl.pathname}${nextUrl.search}`);
    };

    const setLoading = (isLoading) => {
        loading?.classList.toggle('hidden', ! isLoading);
        loading?.classList.toggle('flex', isLoading);
        inlineLoading?.classList.toggle('hidden', ! isLoading);
        results.classList.toggle('opacity-50', isLoading);
        results.classList.toggle('pointer-events-none', isLoading);
    };

    const fetchResults = async (pageUrl = null) => {
        clearTimeout(debounceTimer);
        abortController?.abort();
        abortController = new AbortController();

        const url = buildUrl(pageUrl);
        setLoading(true);

        try {
            const { data } = await window.axios.get(url, {
                signal: abortController.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            results.innerHTML = data;
            updateBadge(results);
            updateUrl(url);
            syncFormFromUrl(url);

            return getProductsSearchTotal();
        } catch (error) {
            if (error.code === 'ERR_CANCELED') {
                return null;
            }

            window.showRepomartToast?.('Unable to update products. Please try again.', 'error');

            return null;
        } finally {
            setLoading(false);
        }
    };

    fetchProductsResults = fetchResults;

    input?.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(() => fetchResults(), DEBOUNCE_MS);
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        fetchResults();
    });

    results.addEventListener('click', (event) => {
        const link = event.target.closest('a');

        if (! link || ! results.contains(link)) {
            return;
        }

        const isPagination = link.closest('[data-products-pagination]');

        if (! isPagination) {
            return;
        }

        event.preventDefault();
        fetchResults(link.href);
    });

    window.addEventListener('popstate', () => {
        syncFormFromUrl();
        fetchResults(window.location.href);
    });
}

export function triggerProductsSearch(form) {
    form?.querySelector('[name="q"]')?.dispatchEvent(new Event('input', { bubbles: true }));
}

export function getProductsSearchTotal() {
    const meta = document.querySelector('[data-products-results-meta]');

    return Number(meta?.dataset.total ?? 0);
}

export { buildProductsSearchSpeech, buildProductsCheckoutHintSpeech } from './voice-search-messages';

export function markVoiceSearchResultsLanding() {
    sessionStorage.setItem(VOICE_SEARCH_RESULTS_HINT_KEY, '1');
}

export function consumeVoiceSearchResultsLanding() {
    const shouldAnnounce = sessionStorage.getItem(VOICE_SEARCH_RESULTS_HINT_KEY) === '1';

    sessionStorage.removeItem(VOICE_SEARCH_RESULTS_HINT_KEY);

    return shouldAnnounce;
}

export async function searchProductsNow(query) {
    const form = document.querySelector('[data-products-search]');
    const input = form?.querySelector('[name="q"]');

    if (! form || ! input || typeof fetchProductsResults !== 'function') {
        return null;
    }

    input.value = query;

    document.querySelectorAll('.header-search-input').forEach((field) => {
        field.value = query;
    });

    return fetchProductsResults();
}
