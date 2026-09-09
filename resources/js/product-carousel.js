export function initProductCarousels() {
    document.querySelectorAll('[data-product-carousel]').forEach((carousel) => {
        const track = carousel.querySelector('[data-product-carousel-track]');
        const viewport = carousel.querySelector('[data-carousel-viewport]');
        const prevBtn = carousel.querySelector('[data-carousel-prev]');
        const nextBtn = carousel.querySelector('[data-carousel-next]');

        if (!track || !viewport) {
            return;
        }

        const originals = [...track.querySelectorAll('[data-carousel-item]')];
        const total = originals.length;

        if (total === 0) {
            prevBtn?.classList.add('hidden');
            nextBtn?.classList.add('hidden');
            return;
        }

        originals.forEach((item) => {
            track.appendChild(item.cloneNode(true));
        });

        let index = 0;
        let isAnimating = false;
        let touchStartX = 0;
        let touchStartY = 0;

        const visibleCount = () => {
            if (window.innerWidth >= 1024) {
                return 5;
            }

            if (window.innerWidth >= 640) {
                return 3;
            }

            return 2;
        };

        const stepWidth = () => track.children[0]?.getBoundingClientRect().width ?? 0;

        const toggleControls = () => {
            const hide = total <= visibleCount() || window.innerWidth < 640;

            [prevBtn, nextBtn].forEach((button) => {
                button?.classList.toggle('hidden', hide);
            });
        };

        const setTransform = (animate = true) => {
            track.style.transition = animate ? 'transform 300ms ease-out' : 'none';
            track.style.transform = `translateX(-${index * stepWidth()}px)`;
        };

        const goNext = () => {
            if (isAnimating || total <= visibleCount()) {
                return;
            }

            isAnimating = true;
            index += 1;
            setTransform(true);

            window.setTimeout(() => {
                if (index >= total) {
                    index = 0;
                    setTransform(false);
                }

                isAnimating = false;
            }, 300);
        };

        const goPrev = () => {
            if (isAnimating || total <= visibleCount()) {
                return;
            }

            isAnimating = true;

            if (index === 0) {
                index = total;
                setTransform(false);
                requestAnimationFrame(() => {
                    index = total - 1;
                    setTransform(true);

                    window.setTimeout(() => {
                        isAnimating = false;
                    }, 300);
                });

                return;
            }

            index -= 1;
            setTransform(true);

            window.setTimeout(() => {
                isAnimating = false;
            }, 300);
        };

        prevBtn?.addEventListener('click', goPrev);
        nextBtn?.addEventListener('click', goNext);

        viewport.addEventListener('touchstart', (event) => {
            if (window.innerWidth >= 640) {
                return;
            }

            const touch = event.changedTouches[0];
            touchStartX = touch.screenX;
            touchStartY = touch.screenY;
        }, { passive: true });

        viewport.addEventListener('touchend', (event) => {
            if (window.innerWidth >= 640) {
                return;
            }

            const touch = event.changedTouches[0];
            const deltaX = touch.screenX - touchStartX;
            const deltaY = touch.screenY - touchStartY;

            if (Math.abs(deltaX) < 40 || Math.abs(deltaX) <= Math.abs(deltaY)) {
                return;
            }

            if (deltaX < 0) {
                goNext();
            } else {
                goPrev();
            }
        }, { passive: true });

        toggleControls();
        setTransform(false);

        window.addEventListener('resize', () => {
            toggleControls();
            setTransform(false);
        });
    });
}
