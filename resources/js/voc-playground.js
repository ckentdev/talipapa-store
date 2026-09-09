export function initVocPlayground() {
    const root = document.querySelector('[data-voc-playground]');

    if (! root) {
        return;
    }

    const form = root.querySelector('[data-voc-form]');
    const input = root.querySelector('[data-voc-input]');
    const status = root.querySelector('[data-voc-status]');
    const results = root.querySelector('[data-voc-results]');
    const chips = root.querySelector('[data-voc-chips]');
    const products = root.querySelector('[data-voc-products]');
    const extractUrl = root.getAttribute('data-extract-url');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (! form || ! input || ! extractUrl) {
        return;
    }

    const chipClass = 'rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-700';

    const addChip = (label, value, extraClass = '') => {
        if (value === null || value === undefined || value === '' || (Array.isArray(value) && value.length === 0)) {
            return;
        }

        const chip = document.createElement('span');
        chip.className = `${chipClass} ${extraClass}`.trim();
        const display = Array.isArray(value) ? value.join(', ') : String(value);
        chip.textContent = `${label}: ${display}`;
        chips.appendChild(chip);
    };

    const render = (payload) => {
        chips.innerHTML = '';
        products.innerHTML = '';
        results.classList.remove('hidden');

        addChip('language', payload.language);
        addChip('product', payload.product);
        addChip('brand', payload.brand);
        addChip('size', payload.units);
        addChip('diet', payload.dietary);
        addChip('price', payload.price_intent);
        addChip('exclude', payload.exclusions, 'bg-red-50 text-red-700');

        (payload.attributes || []).forEach((attribute) => {
            if (['product', 'brand', 'unit', 'dietary', 'price_intent', 'exclusion'].includes(attribute.type)) {
                return;
            }

            addChip(attribute.type, attribute.value);
        });

        if (! payload.products || payload.products.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'px-3 py-3 text-sm text-gray-500';
            empty.textContent = 'No matching products in the catalog.';
            products.appendChild(empty);
            return;
        }

        payload.products.forEach((product) => {
            const name = document.createElement('span');
            name.className = 'font-medium text-gray-900';
            name.textContent = product.name;

            const meta = document.createElement('span');
            meta.className = 'text-gray-500';
            meta.textContent = `₱${Number(product.price).toFixed(2)} · ${product.store_name ?? ''}`;

            const row = document.createElement('a');
            row.href = product.url;
            row.className = 'flex items-center justify-between px-3 py-2 text-sm hover:bg-gray-50';
            row.append(name, meta);
            products.appendChild(row);
        });
    };

    const extract = async (query) => {
        const text = (query ?? input.value).trim();

        if (text === '') {
            return;
        }

        input.value = text;
        status.classList.remove('hidden');
        status.textContent = 'Extracting…';
        results.classList.add('hidden');

        try {
            const response = await fetch(extractUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrf ?? '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ q: text }),
            });

            if (! response.ok) {
                throw new Error('Extract failed');
            }

            const payload = await response.json();
            status.textContent = `${payload.result_count} matching product(s)`;
            render(payload);
        } catch (error) {
            status.textContent = 'Could not extract that phrase. Try again.';
        }
    };

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        extract();
    });

    root.querySelectorAll('[data-voc-example]').forEach((button) => {
        button.addEventListener('click', () => {
            extract(button.getAttribute('data-voc-example') ?? '');
        });
    });
}
