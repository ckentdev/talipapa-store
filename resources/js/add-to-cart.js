import { updateCartBadges, replaceCartDropdownContent } from './cart-dropdown';

function showCartToast(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('repomart:toast', {
        detail: { message, type },
    }));
}

export function initAddToCart() {
    document.addEventListener('submit', async (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || form.method.toLowerCase() !== 'post') {
            return;
        }

        const actionUrl = new URL(form.action, window.location.origin);

        if (form.method.toLowerCase() !== 'post' || actionUrl.pathname.replace(/\/+$/, '') !== '/cart') {
            return;
        }

        event.preventDefault();

        const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');

        if (submitButton instanceof HTMLButtonElement || submitButton instanceof HTMLInputElement) {
            submitButton.disabled = true;
        }

        try {
            const response = await window.axios.post(form.action, new FormData(form), {
                headers: {
                    Accept: 'application/json',
                },
            });

            updateCartBadges(response.data?.itemCount ?? 0);

            if (response.data?.html) {
                replaceCartDropdownContent(response.data.html);
            }

            showCartToast(response.data?.message ?? 'Added to cart.');

            window.dispatchEvent(new CustomEvent('repomart:cart-updated', {
                detail: response.data,
            }));
        } catch (error) {
            const message = error.response?.data?.message
                ?? error.response?.data?.errors?.product?.[0]
                ?? error.response?.data?.errors?.quantity?.[0]
                ?? 'Unable to add item to cart.';

            showCartToast(message, 'error');
        } finally {
            if (submitButton instanceof HTMLButtonElement || submitButton instanceof HTMLInputElement) {
                submitButton.disabled = false;
            }
        }
    });
}
