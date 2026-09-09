export function openListeningModal() {
    const modal = document.getElementById('voice-search-listening-modal');

    if (! modal) {
        return;
    }

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

export function closeListeningModal() {
    const modal = document.getElementById('voice-search-listening-modal');

    if (! modal) {
        return;
    }

    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

export function updateListeningModalStatus(text) {
    const status = document.querySelector('[data-voice-listening-status]');

    if (status) {
        status.textContent = text;
    }
}
