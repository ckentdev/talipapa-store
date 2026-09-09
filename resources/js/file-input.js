function updateFileInput(wrapper, file) {
    const input = wrapper.querySelector('input[type="file"]');
    const nameEl = wrapper.querySelector('[data-file-name]');
    const previewEl = wrapper.querySelector('[data-file-preview]');
    const defaultText = nameEl?.dataset.default ?? 'No file chosen';

    if (!file) {
        if (nameEl) {
            nameEl.textContent = defaultText;
        }
        if (previewEl) {
            previewEl.src = '';
            previewEl.classList.add('hidden');
        }
        return;
    }

    if (nameEl) {
        nameEl.textContent = file.name;
    }

    if (previewEl && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (event) => {
            previewEl.src = event.target.result;
            previewEl.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function setDropzoneActive(dropzone, active) {
    dropzone.classList.toggle('border-forest-400', active);
    dropzone.classList.toggle('bg-avocado-50', active);
}

export function initFileInputs() {
    document.querySelectorAll('[data-file-input]').forEach((wrapper) => {
        const input = wrapper.querySelector('input[type="file"]');
        const dropzone = wrapper.querySelector('[data-file-dropzone]');

        if (!input || !dropzone) {
            return;
        }

        input.addEventListener('change', () => {
            updateFileInput(wrapper, input.files?.[0] ?? null);
        });

        ['dragenter', 'dragover'].forEach((eventName) => {
            dropzone.addEventListener(eventName, (event) => {
                event.preventDefault();
                setDropzoneActive(dropzone, true);
            });
        });

        ['dragleave', 'drop'].forEach((eventName) => {
            dropzone.addEventListener(eventName, (event) => {
                event.preventDefault();
                setDropzoneActive(dropzone, false);
            });
        });

        dropzone.addEventListener('drop', (event) => {
            const file = event.dataTransfer?.files?.[0];

            if (!file) {
                return;
            }

            const transfer = new DataTransfer();
            transfer.items.add(file);
            input.files = transfer.files;
            updateFileInput(wrapper, file);
        });
    });
}
