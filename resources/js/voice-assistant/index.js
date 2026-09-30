import {
    hasVoiceSearchPermissions,
    initVoicePermissionModal,
    openVoicePermissionModal,
    requestMicrophonePermission,
} from '../media-permissions';
import { openListeningModal, closeListeningModal, updateListeningModalStatus } from './listening';
import { createVoiceRecorder } from './recorder';
import { createVoicePanel } from './panel';
import { voiceFilename } from '../voice-audio';

let isProcessing = false;

function canUseAssistant() {
    if (! window.isSecureContext) {
        window.showRepomartToast?.('Voice assistant requires HTTPS.', 'error');

        return false;
    }

    if (! navigator.onLine) {
        window.showRepomartToast?.('Voice assistant requires an internet connection.', 'error');

        return false;
    }

    if (! window.voiceAssistantEnabled) {
        window.showRepomartToast?.('Voice assistant is not available.', 'warning');

        return false;
    }

    return true;
}

function getContext() {
    return document.body.dataset.voiceContext || 'default';
}

function getCheckoutStep() {
    const step = document.body.dataset.checkoutStep;

    return step ? parseInt(step, 10) : null;
}

async function processVoiceInput(blob, panel) {
    if (! blob?.size) {
        throw new Error('No speech detected. Please try again.');
    }

    const formData = new FormData();
    formData.append('audio', blob, voiceFilename(blob));
    formData.append('context', getContext());

    const step = getCheckoutStep();

    if (step) {
        formData.append('checkout_step', String(step));
    }

    panel.setStatus('Understanding your request…');

    const { data } = await window.axios.post('/voice-assistant/process', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
    });

    if (data.transcript) {
        panel.appendMessage('user', data.transcript);
    }

    if (data.reply) {
        panel.appendMessage('assistant', data.reply);
    }

    panel.renderProducts(data.products ?? [], async (product) => {
        await addProductToCart(product, panel);
    });

    if (data.requires_login) {
        window.showRepomartToast?.('Sign in to use cart and checkout voice commands.', 'warning');
    }

    if (data.redirect) {
        window.setTimeout(() => {
            window.location.href = data.redirect;
        }, 1500);
    }

    panel.setStatus('Tap the microphone to speak again.');
}

async function addProductToCart(product, panel) {
    try {
        const response = await window.axios.post('/cart', {
            product_id: product.id,
            quantity: 1,
        }, {
            headers: {
                Accept: 'application/json',
            },
        });

        window.dispatchEvent(new CustomEvent('repomart:cart-updated', {
            detail: response.data,
        }));

        window.showRepomartToast?.(response.data?.message ?? `Added ${product.name} to cart.`, 'success');
        panel.appendMessage('assistant', `Added ${product.name} to your cart.`);
    } catch {
        window.showRepomartToast?.('Could not add to cart. Please sign in or try again.', 'error');
    }
}

async function runRecording(panel) {
    if (isProcessing) {
        return;
    }

    const recorder = createVoiceRecorder();

    try {
        isProcessing = true;
        panel.setStatus('Listening… speak now.');
        openListeningModal();
        updateListeningModalStatus('Listening… tap stop when done.');

        const recordPromise = recorder.start();

        const stopButton = document.querySelector('[data-voice-assistant-stop]');
        const stopHandler = () => recorder.stop();
        stopButton?.addEventListener('click', stopHandler, { once: true });

        document.querySelector('[data-voice-listening-cancel]')?.addEventListener('click', stopHandler, { once: true });

        const blob = await recordPromise;
        closeListeningModal();

        if (! blob?.size) {
            throw new Error('No speech detected. Please try again.');
        }

        panel.setStatus('Processing…');

        await processVoiceInput(blob, panel);
    } catch (error) {
        closeListeningModal();
        const message = error?.response?.data?.message ?? error?.message ?? 'Voice assistant failed.';
        window.showRepomartToast?.(message, 'error');
        panel.setStatus('Tap the microphone to try again.');
    } finally {
        isProcessing = false;
    }
}

function beginAssistant(panel) {
    if (! canUseAssistant()) {
        return;
    }

    panel.setOpen(true);

    if (! hasVoiceSearchPermissions()) {
        const modal = document.getElementById('voice-search-permission-modal');

        openVoicePermissionModal(modal, () => runRecording(panel));

        return;
    }

    runRecording(panel);
}

export function initVoiceAssistant() {
    const root = document.querySelector('[data-voice-assistant]');

    if (! root || ! window.voiceAssistantEnabled) {
        return;
    }

    initVoicePermissionModal();

    const panel = createVoicePanel(root);

    document.querySelector('[data-voice-assistant-toggle]')?.addEventListener('click', () => {
        const isOpen = root.getAttribute('aria-hidden') === 'false';

        if (! isOpen) {
            panel.setOpen(true);
            beginAssistant(panel);

            return;
        }

        if (! isProcessing) {
            beginAssistant(panel);
        }
    });

    root.querySelector('[data-voice-assistant-close]')?.addEventListener('click', () => {
        panel.setOpen(false);
    });

    root.querySelector('[data-voice-assistant-mic]')?.addEventListener('click', () => {
        beginAssistant(panel);
    });
}
