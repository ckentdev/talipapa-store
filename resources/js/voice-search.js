import {
    clearMicrophonePermission,
    initVoicePermissionModal,
    openVoicePermissionModal,
    playVoiceFeedback,
    releaseVoiceMicStream,
} from './media-permissions';
import { createVoiceRecorder } from './voice-assistant/recorder';
import {
    buildHeardTranscriptSpeech,
    buildOpeningProductsSpeech,
    buildProductsSearchSpeech,
    buildSearchingSpeech,
    friendlyVoiceSearchError,
    voiceSearchCopy,
} from './voice-search-messages';
import { voiceFilename } from './voice-audio';
import {
    consumeVoiceSearchResultsLanding,
    markVoiceSearchResultsLanding,
    searchProductsNow,
} from './products-search';

let activeRecognition = null;
let activeRecorder = null;
let voiceSearchCancelled = false;
let activeVoiceControl = null;
let lastModalDisplayMessage = '';

const SILENCE_STOP_MS = 1600;
const SPEECH_LEVEL_THRESHOLD = 0.07;
const MIN_RECORDING_MS = 1200;
const MIN_SPEECH_MS = 450;

export function initVoiceSearch() {
    initVoicePermissionModal();
    initVoiceListeningModal();

    document.addEventListener('click', (event) => {
        const fab = event.target.closest('[data-voice-search-fab]');
        if (fab) {
            event.preventDefault();
            handleVoiceSearchFabClick(fab);
            return;
        }

        const button = event.target.closest('[data-voice-search]');
        if (button) {
            event.preventDefault();
            handleVoiceSearchClick(button);
        }
    });

    document.querySelectorAll('[data-search-filter-toggle]').forEach((button) => {
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleFilterPanel(button);
        });
    });

    document.querySelectorAll('[data-search-filter-close]').forEach((button) => {
        button.addEventListener('click', () => closeFilterPanels());
    });

    document.addEventListener('click', (e) => {
        if (! e.target.closest('[data-search-filter-panel]') && ! e.target.closest('[data-search-filter-toggle]')) {
            closeFilterPanels();
        }
    });
}

function initVoiceListeningModal() {
    const modal = document.getElementById('voice-search-listening-modal');
    if (! modal) {
        return;
    }

    modal.querySelector('[data-voice-listening-cancel]')?.addEventListener('click', () => {
        setModalActionsVisible(false);
        finishRecording();
    });

    modal.querySelector('[data-voice-listening-close]')?.addEventListener('click', () => {
        handleModalClose();
    });

    modal.querySelector('[data-voice-listening-backdrop]')?.addEventListener('click', () => {
        handleModalClose();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && ! modal.classList.contains('hidden')) {
            handleModalClose();
        }
    });

    void announceVoiceSearchResultsLanding();
}

function resolveSpeechLanguage() {
    return 'fil-PH';
}

async function waitForAudioHandoff(ms = 150) {
    await new Promise((resolve) => window.setTimeout(resolve, ms));
}

function canUseServerVoiceSearch() {
    return window.voiceSearchEnabled === true;
}

function canUseVoiceSearch() {
    if (! window.isSecureContext) {
        notify('Voice search requires a secure connection (HTTPS).', 'error');
        return false;
    }

    if (! navigator.onLine) {
        notify('Voice search requires an internet connection.', 'error');
        return false;
    }

    if (! window.axios) {
        notify('Voice search failed to initialize. Please refresh the page.', 'error');
        return false;
    }

    return true;
}

function notify(message, type = 'error') {
    window.showRepomartToast?.(message, type) ?? alert(message);
}

function getModalTitle(mode = 'listening') {
    const titles = {
        listening: "I'm listening",
        speaking: 'I hear you',
        processing: 'One moment',
        results: 'Your results',
    };

    return titles[mode] ?? titles.listening;
}

function showModalStatus(message) {
    const status = document.querySelector('[data-voice-listening-status]');

    if (! status) {
        return;
    }

    status.textContent = message;
    status.classList.toggle('hidden', ! message);
}

function setModalActionsVisible(visible) {
    const actions = document.querySelector('[data-voice-listening-actions]');

    if (! actions) {
        return;
    }

    actions.classList.toggle('hidden', ! visible);
    actions.classList.toggle('flex', visible);
}

function setModalRecordingReady() {
    setModalActionsVisible(true);
    updateListeningModalStatus(voiceSearchCopy.listening.speakNow);
}

function handleModalClose() {
    if (activeRecorder || activeRecognition) {
        cancelVoiceSearch();
        return;
    }

    closeListeningModal();
}

function setModalAnnouncement(message, mode = 'listening') {
    lastModalDisplayMessage = message;

    const title = document.getElementById('voice-search-listening-title');
    const body = document.getElementById('voice-search-listening-message');

    if (title) {
        title.textContent = getModalTitle(mode);
    }

    if (body) {
        body.textContent = message;
    }

    showModalStatus('');
}

function updateListeningModalStatus(message) {
    showModalStatus(message);
}

function openListeningModal(mode = 'listening', message = null) {
    const modal = document.getElementById('voice-search-listening-modal');
    if (! modal) {
        return;
    }

    resetVoiceVisualizer();
    setListeningModalMode(mode);

    if (message) {
        setModalAnnouncement(message, mode);
    } else {
        const title = document.getElementById('voice-search-listening-title');
        if (title) {
            title.textContent = getModalTitle(mode);
        }
    }

    if (mode === 'processing' || mode === 'results') {
        setModalActionsVisible(false);
    }

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeListeningModal() {
    const modal = document.getElementById('voice-search-listening-modal');
    if (! modal) {
        return;
    }

    modal.classList.add('hidden');
    resetVoiceVisualizer();
    resetModalSpeech();
    setModalActionsVisible(false);

    if (document.getElementById('voice-search-permission-modal')?.classList.contains('hidden')) {
        document.body.classList.remove('overflow-hidden');
    }
}

function setListeningModalMode(mode) {
    const modal = document.getElementById('voice-search-listening-modal');
    const mic = modal?.querySelector('[data-voice-listening-mic]');
    const pulse = modal?.querySelector('[data-voice-listening-pulse]');
    const title = document.getElementById('voice-search-listening-title');

    mic?.classList.toggle('scale-100', mode !== 'processing');
    mic?.classList.toggle('scale-95', mode === 'processing');
    pulse?.classList.toggle('animate-pulse', mode === 'listening' || mode === 'speaking');
    pulse?.classList.toggle('animate-ping', mode === 'speaking');

    if (title && mode in { listening: 1, speaking: 1, processing: 1, results: 1 }) {
        title.textContent = getModalTitle(mode);
    }
}

function resetVoiceVisualizer() {
    document.querySelectorAll('[data-voice-level-bar]').forEach((bar) => {
        bar.style.height = '0.75rem';
        bar.style.opacity = '0.35';
    });

    const ring = document.querySelector('[data-voice-listening-ring]');
    const mic = document.querySelector('[data-voice-listening-mic]');

    if (ring) {
        ring.style.transform = 'scale(1)';
        ring.style.opacity = '0.35';
    }

    if (mic) {
        mic.style.transform = 'scale(1)';
    }
}

function updateVoiceVisualizer(level) {
    const bars = document.querySelectorAll('[data-voice-level-bar]');
    const ring = document.querySelector('[data-voice-listening-ring]');
    const mic = document.querySelector('[data-voice-listening-mic]');
    const normalized = Math.max(0.08, Math.min(1, level));

    bars.forEach((bar, index) => {
        const offset = Math.abs(index - Math.floor(bars.length / 2));
        const barLevel = Math.max(0.08, normalized - offset * 0.08);
        bar.style.height = `${0.75 + barLevel * 2.25}rem`;
        bar.style.opacity = String(0.35 + barLevel * 0.65);
    });

    if (ring) {
        ring.style.transform = `scale(${1 + normalized * 0.45})`;
        ring.style.opacity = String(0.25 + normalized * 0.45);
    }

    if (mic) {
        mic.style.transform = `scale(${1 + normalized * 0.08})`;
    }
}

function createVoiceLevelHandlers(recorderControl) {
    let heardSpeech = false;
    let silenceStartedAt = null;
    let speechStartedAt = null;
    let recordingStartedAt = Date.now();

    return {
        onLevel(level) {
            updateVoiceVisualizer(level);

            if (level >= SPEECH_LEVEL_THRESHOLD) {
                heardSpeech = true;
                silenceStartedAt = null;
                speechStartedAt ??= Date.now();
                setListeningModalMode('speaking');
                updateListeningModalStatus(voiceSearchCopy.listening.hearingYou);
                return;
            }

            const elapsed = Date.now() - recordingStartedAt;

            if (! heardSpeech || elapsed < MIN_RECORDING_MS) {
                setListeningModalMode('listening');
                updateListeningModalStatus(voiceSearchCopy.listening.speakNow);
                return;
            }

            const speechDuration = speechStartedAt ? Date.now() - speechStartedAt : 0;

            if (speechDuration < MIN_SPEECH_MS) {
                setListeningModalMode('listening');
                updateListeningModalStatus(voiceSearchCopy.listening.keepSpeaking);
                return;
            }

            silenceStartedAt ??= Date.now();
            setListeningModalMode('listening');

            if (Date.now() - silenceStartedAt >= SILENCE_STOP_MS) {
                updateListeningModalStatus(voiceSearchCopy.listening.lookingUp);
                setModalActionsVisible(false);
                recorderControl.stop();
            } else {
                updateListeningModalStatus(voiceSearchCopy.listening.finishingUp);
            }
        },
        onSpeakingChange(speaking) {
            if (speaking) {
                setListeningModalMode('speaking');
            }
        },
    };
}

function announceModalMessage(message, { mode = 'listening' } = {}) {
    setModalAnnouncement(message, mode);
}

async function openModalAndWelcome(mode = 'listening') {
    openListeningModal(mode);
    setModalActionsVisible(false);
    await announceModalMessage(voiceSearchCopy.listening.welcome, { force: true, mode });
}

function resetModalSpeech() {
    lastModalDisplayMessage = '';
}

function cancelVoiceSearch() {
    voiceSearchCancelled = true;
    activeRecognition?.abort();
    activeRecorder?.stop();
}

function finishRecording() {
    activeRecognition?.abort();
    activeRecorder?.stop();
}

function handleVoiceSearchClick(button) {
    if (activeRecognition || activeRecorder) {
        finishRecording();
        return;
    }

    if (! canUseVoiceSearch()) {
        return;
    }

    void beginVoiceSearch(button);
}

function handleVoiceSearchFabClick(button) {
    if (activeRecognition || activeRecorder) {
        finishRecording();
        return;
    }

    if (! canUseVoiceSearch()) {
        return;
    }

    void beginVoiceSearchFab(button);
}

async function beginVoiceSearch(button) {
    releaseVoiceMicStream();

    if (canUseServerVoiceSearch()) {
        await startServerVoiceSearch(button);
        return;
    }

    startBrowserVoiceSearch(button);
}

async function beginVoiceSearchFab(button) {
    releaseVoiceMicStream();

    if (canUseServerVoiceSearch()) {
        await startServerVoiceSearchFab(button);
        return;
    }

    notify('Voice search requires server transcription. Configure OPENAI_API_KEY.', 'warning');
}

function toggleFilterPanel(button) {
    const form = button.closest('[data-header-search]');
    const panel = form?.querySelector('[data-search-filter-panel]');
    if (! panel) return;

    const isOpen = ! panel.classList.contains('hidden');
    closeFilterPanels();

    if (! isOpen) {
        panel.classList.remove('hidden');
        button.setAttribute('aria-expanded', 'true');
    }
}

function closeFilterPanels() {
    document.querySelectorAll('[data-search-filter-panel]').forEach((panel) => panel.classList.add('hidden'));
    document.querySelectorAll('[data-search-filter-toggle]').forEach((btn) => btn.setAttribute('aria-expanded', 'false'));
}

function setListeningState(button, listening) {
    if (! button) {
        return;
    }

    const icon = button.querySelector('i');
    button.classList.toggle('animate-pulse', listening);
    button.setAttribute('aria-pressed', listening ? 'true' : 'false');

    if (button.matches('[data-voice-search-fab]')) {
        button.classList.toggle('bg-brand-700', listening);
        button.classList.toggle('ring-brand-300', listening);

        if (icon) {
            icon.classList.toggle('ri-mic-line', ! listening);
            icon.classList.toggle('ri-mic-fill', listening);
        }

        return;
    }

    button.classList.toggle('text-brand-600', listening);
    button.classList.toggle('bg-brand-50', listening);

    if (icon) {
        icon.classList.toggle('ri-mic-line', ! listening);
        icon.classList.toggle('ri-mic-fill', listening);
    }
}

function findSearchContext(button) {
    const form = button?.closest('[data-header-search]') ?? document.querySelector('[data-header-search]');
    const input = form?.querySelector('.header-search-input');
    const status = form?.querySelector('[data-voice-search-status]');

    return { form, input, status };
}

function announceVoiceSearchResultsLanding() {
    consumeVoiceSearchResultsLanding();
}

async function applyVoiceSearchTranscript(form, input, status, transcript) {
    setModalActionsVisible(false);
    setListeningModalMode('processing');
    await announceModalMessage(buildSearchingSpeech(transcript), { force: true, mode: 'processing' });

    const searchTotal = await searchProductsNow(transcript);

    if (searchTotal !== null) {
        if (status) {
            status.textContent = buildProductsSearchSpeech(transcript, searchTotal);
        }

        return true;
    }

    if (form && input) {
        input.value = transcript;

        if (status) {
            status.textContent = `Searching for "${transcript}"`;
        }

        await announceModalMessage(buildOpeningProductsSpeech(transcript), { force: true, mode: 'processing' });
        markVoiceSearchResultsLanding();

        if (form.matches('[data-products-search]')) {
            form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
            return true;
        }

        form.requestSubmit();
        return true;
    }

    await announceModalMessage(buildOpeningProductsSpeech(transcript), { force: true, mode: 'processing' });
    markVoiceSearchResultsLanding();

    const baseUrl = window.productsSearchUrl || '/products';
    const url = new URL(baseUrl, window.location.origin);
    url.searchParams.set('q', transcript);
    window.location.href = url.toString();

    return true;
}

function finishVoiceSearch(button, input, status, { delayMs = 400 } = {}) {
    const finalize = () => {
        activeRecognition = null;
        activeRecorder = null;
        activeVoiceControl = null;
        voiceSearchCancelled = false;
        setListeningState(button, false);
        closeListeningModal();
        releaseVoiceMicStream();
        playVoiceFeedback();

        if (status && ! input?.value) {
            status.textContent = '';
        }
    };

    if (delayMs > 0) {
        window.setTimeout(finalize, delayMs);
        return;
    }

    finalize();
}

function handleVoiceSearchError(event, status) {
    const messages = {
        'not-allowed': voiceSearchCopy.errors.notAllowed,
        'no-speech': voiceSearchCopy.errors.noSpeech,
        'aborted': 'Voice search cancelled.',
        'network': voiceSearchCopy.errors.network,
        'service-not-allowed': voiceSearchCopy.errors.serviceNotAllowed,
        'audio-capture': voiceSearchCopy.errors.audioCapture,
    };

    if (event.error === 'network' || event.error === 'not-allowed' || event.error === 'service-not-allowed') {
        clearMicrophonePermission().catch(() => {});
    }

    const message = messages[event.error] ?? voiceSearchCopy.errors.failed;

    if (event.error !== 'aborted') {
        notify(message, 'error');
        void announceModalMessage(message, { force: true, mode: 'processing' });
    }

    if (status) {
        status.textContent = '';
    }
}

async function transcribeVoiceBlob(blob) {
    const formData = new FormData();
    formData.append('audio', blob, voiceFilename(blob));

    const { data } = await window.axios.post('/voice-search/transcribe', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
    });

    const transcript = data.transcript?.trim();

    if (! transcript) {
        throw new Error(voiceSearchCopy.errors.noSpeech);
    }

    return transcript;
}

async function captureVoiceSearchAudio(button) {
    let recorder = null;

    recorder = createVoiceRecorder({
        ...createVoiceLevelHandlers({
            stop() {
                recorder?.stop();
            },
        }),
    });

    activeRecorder = recorder;
    activeVoiceControl = button;
    voiceSearchCancelled = false;

    setListeningState(button, true);
    await openModalAndWelcome('listening');
    setModalRecordingReady();

    let blob;

    try {
        blob = await recorder.start();
    } catch (error) {
        if (error?.name === 'NotAllowedError' || error?.message?.includes('permission')) {
            const modal = document.getElementById('voice-search-permission-modal');
            openVoicePermissionModal(modal, () => beginVoiceSearch(button), { micOnly: true });
        }

        throw error;
    }

    if (voiceSearchCancelled) {
        return null;
    }

    if (! blob?.size) {
        throw new Error(voiceSearchCopy.errors.noSpeech);
    }

    await waitForAudioHandoff(0);
    setModalActionsVisible(false);
    openListeningModal('processing');
    await announceModalMessage(voiceSearchCopy.listening.processingRecording, { force: true, mode: 'processing' });

    return blob;
}

async function startServerVoiceSearch(button) {
    const { form, input, status } = findSearchContext(button);

    try {
        if (status) {
            status.textContent = "I'm listening — go ahead and speak.";
        }

        const blob = await captureVoiceSearchAudio(button);

        if (! blob) {
            finishVoiceSearch(button, input, status);
            return;
        }

        if (status) {
            status.textContent = 'Working on it…';
        }

        const transcript = await transcribeVoiceBlob(blob);
        await announceModalMessage(buildHeardTranscriptSpeech(transcript), { force: true, mode: 'processing' });
        await applyVoiceSearchTranscript(form, input, status, transcript);
        finishVoiceSearch(button, input, status, { delayMs: 250 });
    } catch (error) {
        if (! voiceSearchCancelled) {
            const rawMessage = error?.response?.data?.message ?? error?.message ?? voiceSearchCopy.errors.failed;
            const message = friendlyVoiceSearchError(rawMessage);
            notify(rawMessage, 'error');
            announceModalMessage(message, { mode: 'processing' });
        }

        if (status) {
            status.textContent = '';
        }

        finishVoiceSearch(button, input, status);
    }
}

async function startServerVoiceSearchFab(button) {
    try {
        const blob = await captureVoiceSearchAudio(button);

        if (! blob) {
            finishVoiceSearch(button, null, null);
            return;
        }

        const transcript = await transcribeVoiceBlob(blob);
        const { form, input, status } = findSearchContext(button);
        await announceModalMessage(buildHeardTranscriptSpeech(transcript), { force: true, mode: 'processing' });
        await applyVoiceSearchTranscript(form, input, status, transcript);
        finishVoiceSearch(button, input, status, { delayMs: 250 });
    } catch (error) {
        if (! voiceSearchCancelled) {
            const rawMessage = error?.response?.data?.message ?? error?.message ?? voiceSearchCopy.errors.failed;
            const message = friendlyVoiceSearchError(rawMessage);
            notify(rawMessage, 'error');
            announceModalMessage(message, { mode: 'processing' });
        }

        finishVoiceSearch(button, null, null);
    }
}

function startBrowserVoiceSearch(button) {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    const { form, input, status } = findSearchContext(button);

    if (! SpeechRecognition) {
        notify('Voice search is not supported in this browser. Please type your search instead.', 'warning');
        return;
    }

    if (! input) {
        return;
    }

    if (! canUseVoiceSearch()) {
        return;
    }

    releaseVoiceMicStream();

    const recognition = new SpeechRecognition();
    activeRecognition = recognition;
    activeVoiceControl = button;
    recognition.lang = resolveSpeechLanguage();
    recognition.continuous = false;
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;

    let didSearch = false;

    recognition.onstart = () => {
        setModalRecordingReady();
        playVoiceFeedback();
    };

    recognition.onresult = async (event) => {
        const transcript = event.results[0][0].transcript.trim();
        didSearch = true;
        setModalActionsVisible(false);
        await applyVoiceSearchTranscript(form, input, status, transcript);
    };

    recognition.onerror = (event) => {
        handleVoiceSearchError(event, status);
    };

    recognition.onend = () => {
        finishVoiceSearch(button, input, status, { delayMs: didSearch ? 400 : 0 });
    };

    void (async () => {
        setListeningState(button, true);
        await openModalAndWelcome('listening');

        try {
            recognition.start();
        } catch (error) {
            activeRecognition = null;
            setListeningState(button, false);
            closeListeningModal();
            releaseVoiceMicStream();
            notify('Unable to start voice search. Please try again.', 'error');
        }
    })();
}
