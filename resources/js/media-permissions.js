const SOUND_STORAGE_KEY = 'repomart_voice_sound_enabled';
const MIC_STORAGE_KEY = 'repomart_voice_mic_enabled';

let voiceMicStream = null;

function isAuthenticated() {
    return document.body.dataset.userId !== undefined;
}

export function isSoundEnabled() {
    return document.body.dataset.soundEnabled === '1'
        || localStorage.getItem(SOUND_STORAGE_KEY) === '1';
}

export function isMicrophoneEnabled() {
    return document.body.dataset.micEnabled === '1'
        || localStorage.getItem(MIC_STORAGE_KEY) === '1';
}

export function hasVoiceSearchPermissions() {
    return isSoundEnabled() && isMicrophoneEnabled();
}

export function syncMediaPermissionsFromStorage() {
    if (localStorage.getItem(SOUND_STORAGE_KEY) === '1') {
        document.body.dataset.soundEnabled = '1';
    }

    if (localStorage.getItem(MIC_STORAGE_KEY) === '1') {
        document.body.dataset.micEnabled = '1';
    }
}

async function persistSoundEnabled(enabled) {
    localStorage.setItem(SOUND_STORAGE_KEY, enabled ? '1' : '0');
    document.body.dataset.soundEnabled = enabled ? '1' : '0';

    if (isAuthenticated()) {
        try {
            await window.axios.patch('/account/sound-alerts', { enabled });
        } catch {
            // Keep local permission even if account sync fails.
        }
    }
}

async function persistMicrophoneEnabled(granted) {
    localStorage.setItem(MIC_STORAGE_KEY, granted ? '1' : '0');
    document.body.dataset.micEnabled = granted ? '1' : '0';

    if (isAuthenticated()) {
        try {
            await window.axios.post('/account/permissions', { permission: 'microphone', granted });
        } catch {
            // Keep local permission even if account sync fails.
        }
    }
}

function getAudioContext() {
    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
    if (! AudioContextClass) {
        return null;
    }

    window._repomartAudioContext = window._repomartAudioContext ?? new AudioContextClass();

    return window._repomartAudioContext;
}

async function playBeep(frequency = 880, duration = 0.15) {
    const context = getAudioContext();
    if (! context) {
        return;
    }

    if (context.state === 'suspended') {
        await context.resume();
    }

    const oscillator = context.createOscillator();
    const gain = context.createGain();

    oscillator.type = 'sine';
    oscillator.frequency.value = frequency;
    gain.gain.setValueAtTime(0.08, context.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, context.currentTime + duration);

    oscillator.connect(gain);
    gain.connect(context.destination);

    oscillator.start(context.currentTime);
    oscillator.stop(context.currentTime + duration);

    await new Promise((resolve) => {
        window.setTimeout(resolve, duration * 1000);
    });
}

export async function requestSoundPermission() {
    await playBeep();
    await persistSoundEnabled(true);

    return true;
}

export async function requestMicrophonePermission() {
    if (! navigator.mediaDevices?.getUserMedia) {
        throw new Error('Microphone is not supported in this browser.');
    }

    releaseVoiceMicStream();

    voiceMicStream = await navigator.mediaDevices.getUserMedia({ audio: true });
    await persistMicrophoneEnabled(true);

    return true;
}

export function releaseVoiceMicStream() {
    voiceMicStream?.getTracks().forEach((track) => track.stop());
    voiceMicStream = null;
}

export function hasActiveVoiceMicStream() {
    return voiceMicStream?.active === true;
}

export function getVoiceMicStream() {
    return voiceMicStream?.active ? voiceMicStream : null;
}

export async function clearMicrophonePermission() {
    releaseVoiceMicStream();
    localStorage.setItem(MIC_STORAGE_KEY, '0');
    document.body.dataset.micEnabled = '0';

    if (isAuthenticated()) {
        try {
            await window.axios.post('/account/permissions', { permission: 'microphone', granted: false });
        } catch {
            // Ignore sync failures.
        }
    }
}

export async function unlockAudioOutput() {
    const context = getAudioContext();

    if (context?.state === 'suspended') {
        try {
            await context.resume();
        } catch {
            // Ignore resume failures.
        }
    }
}

export function playVoiceFeedback() {
    if (! isSoundEnabled()) {
        return;
    }

    playBeep(660, 0.12).catch(() => {});
}

export function updatePermissionModalUi(modal) {
    if (! modal) {
        return;
    }

    const soundGranted = isSoundEnabled();
    const micGranted = isMicrophoneEnabled();

    const soundStatus = modal.querySelector('[data-voice-perm-sound-status]');
    const micStatus = modal.querySelector('[data-voice-perm-mic-status]');
    const soundButton = modal.querySelector('[data-voice-perm-enable-sound]');
    const micButton = modal.querySelector('[data-voice-perm-enable-mic]');
    const startButton = modal.querySelector('[data-voice-perm-start]');

    if (soundStatus) {
        soundStatus.textContent = soundGranted ? 'Allowed' : 'Not enabled';
        soundStatus.classList.toggle('text-brand-600', soundGranted);
        soundStatus.classList.toggle('text-gray-500', ! soundGranted);
    }

    if (micStatus) {
        micStatus.textContent = micGranted ? 'Allowed' : 'Not enabled';
        micStatus.classList.toggle('text-brand-600', micGranted);
        micStatus.classList.toggle('text-gray-500', ! micGranted);
    }

    if (soundButton) {
        soundButton.disabled = soundGranted;
        soundButton.classList.toggle('opacity-50', soundGranted);
        soundButton.classList.toggle('cursor-not-allowed', soundGranted);
        soundButton.textContent = soundGranted ? 'Sound allowed' : 'Allow sound';
    }

    if (micButton) {
        micButton.disabled = micGranted;
        micButton.classList.toggle('opacity-50', micGranted);
        micButton.classList.toggle('cursor-not-allowed', micGranted);
        micButton.textContent = micGranted ? 'Microphone allowed' : 'Allow microphone';
    }

    if (startButton) {
        const micOnly = modal.dataset.voicePermMicOnly === '1';
        const canStart = micOnly ? micGranted : (soundGranted && micGranted);

        startButton.disabled = ! canStart;
        startButton.classList.toggle('opacity-50', ! canStart);
        startButton.classList.toggle('cursor-not-allowed', ! canStart);
    }
}

export function openVoicePermissionModal(modal, onReady, { micOnly = false } = {}) {
    if (! modal) {
        return;
    }

    modal.dataset.voicePermCallback = 'pending';
    modal.dataset.voicePermMicOnly = micOnly ? '1' : '0';
    modal._voicePermCallback = onReady;
    updatePermissionModalUi(modal);
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    (micOnly
        ? modal.querySelector('[data-voice-perm-enable-mic]')
        : modal.querySelector('[data-voice-perm-enable-sound]')
    )?.focus();
}

export function closeVoicePermissionModal(modal) {
    if (! modal) {
        return;
    }

    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    modal._voicePermCallback = null;
}

export function initVoicePermissionModal() {
    const modal = document.getElementById('voice-search-permission-modal');
    if (! modal) {
        return;
    }

    syncMediaPermissionsFromStorage();
    updatePermissionModalUi(modal);

    modal.querySelector('[data-voice-perm-enable-sound]')?.addEventListener('click', async () => {
        try {
            await requestSoundPermission();
            updatePermissionModalUi(modal);
        } catch {
            window.showRepomartToast?.('Could not enable sound. Please interact with the page and try again.', 'error')
                ?? alert('Could not enable sound. Please interact with the page and try again.');
        }
    });

    modal.querySelector('[data-voice-perm-enable-mic]')?.addEventListener('click', async () => {
        try {
            await requestMicrophonePermission();
            updatePermissionModalUi(modal);
        } catch {
            await persistMicrophoneEnabled(false).catch(() => {});
            updatePermissionModalUi(modal);
            window.showRepomartToast?.('Microphone permission was denied.', 'error')
                ?? alert('Microphone permission was denied.');
        }
    });

    modal.querySelector('[data-voice-perm-start]')?.addEventListener('click', async () => {
        const callback = modal._voicePermCallback;
        const micOnly = modal.dataset.voicePermMicOnly === '1';
        const canStart = micOnly ? isMicrophoneEnabled() : hasVoiceSearchPermissions();

        closeVoicePermissionModal(modal);

        if (typeof callback !== 'function' || ! canStart) {
            return;
        }

        if (! hasActiveVoiceMicStream()) {
            try {
                await requestMicrophonePermission();
            } catch {
                await clearMicrophonePermission().catch(() => {});
                updatePermissionModalUi(modal);
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                window.showRepomartToast?.('Microphone permission was denied.', 'error')
                    ?? alert('Microphone permission was denied.');
                return;
            }
        }

        callback();
    });

    modal.querySelector('[data-voice-perm-cancel]')?.addEventListener('click', () => {
        closeVoicePermissionModal(modal);
    });

    modal.querySelector('[data-voice-perm-backdrop]')?.addEventListener('click', () => {
        closeVoicePermissionModal(modal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && ! modal.classList.contains('hidden')) {
            closeVoicePermissionModal(modal);
        }
    });
}
