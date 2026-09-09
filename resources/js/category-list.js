export function initCategoryList() {
    document.querySelectorAll('[data-category-section]').forEach((section) => {
        const toggle = section.querySelector('[data-category-toggle]');
        const hiddenItems = section.querySelectorAll('[data-category-hidden]');

        if (!toggle || hiddenItems.length === 0) {
            return;
        }

        toggle.addEventListener('click', () => {
            const expanded = toggle.getAttribute('aria-expanded') === 'true';

            hiddenItems.forEach((item) => {
                item.classList.toggle('hidden', expanded);
            });

            toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            toggle.querySelector('[data-toggle-label]').textContent = expanded ? 'View all' : 'View less';

            const icon = toggle.querySelector('[data-toggle-icon]');
            if (icon) {
                icon.classList.toggle('ri-arrow-right-s-line', expanded);
                icon.classList.toggle('ri-arrow-up-s-line', !expanded);
            }
        });
    });
}
