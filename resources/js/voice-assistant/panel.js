function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;

    return div.innerHTML;
}

function formatPrice(price) {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(price);
}

export function createVoicePanel(root) {
    const messagesEl = root.querySelector('[data-voice-assistant-messages]');
    const statusEl = root.querySelector('[data-voice-assistant-status]');
    const productsEl = root.querySelector('[data-voice-assistant-products]');

    function setStatus(text) {
        if (statusEl) {
            statusEl.textContent = text;
        }
    }

    function setOpen(open) {
        root.classList.toggle('translate-y-full', ! open);
        root.classList.toggle('pointer-events-none', ! open);
        root.setAttribute('aria-hidden', open ? 'false' : 'true');
    }

    function appendMessage(role, text) {
        if (! messagesEl || ! text) {
            return;
        }

        const bubble = document.createElement('div');
        bubble.className = role === 'user'
            ? 'ml-auto max-w-[85%] rounded-2xl rounded-br-md bg-brand-600 px-4 py-2.5 text-base text-white'
            : 'mr-auto max-w-[85%] rounded-2xl rounded-bl-md bg-gray-100 px-4 py-2.5 text-base text-gray-800';

        bubble.textContent = text;
        messagesEl.appendChild(bubble);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function renderProducts(products, onAddToCart) {
        if (! productsEl) {
            return;
        }

        productsEl.innerHTML = '';

        if (! products?.length) {
            return;
        }

        products.forEach((product) => {
            const card = document.createElement('div');
            card.className = 'flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3';
            card.innerHTML = `
                <div class="min-w-0 flex-1">
                    <a href="${escapeHtml(product.url)}" class="font-semibold text-gray-900 hover:text-brand-600 line-clamp-2">${escapeHtml(product.name)}</a>
                    <p class="mt-0.5 text-sm text-gray-500">${escapeHtml(product.store_name ?? '')}</p>
                    <p class="mt-1 text-base font-bold text-brand-600">${formatPrice(product.price)}</p>
                </div>
                <button type="button" data-voice-add-product="${product.id}" class="shrink-0 rounded-lg bg-brand-600 px-3 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                    Add
                </button>
            `;

            card.querySelector('[data-voice-add-product]')?.addEventListener('click', () => {
                onAddToCart?.(product);
            });

            productsEl.appendChild(card);
        });
    }

    function clear() {
        if (messagesEl) {
            messagesEl.innerHTML = '';
        }

        if (productsEl) {
            productsEl.innerHTML = '';
        }
    }

    return {
        setOpen,
        setStatus,
        appendMessage,
        renderProducts,
        clear,
    };
}
