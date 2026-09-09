let alertAudio = null;

export function initSoundAlerts() {
    document.querySelectorAll('[data-enable-sound]').forEach((button) => {
        button.addEventListener('click', () => enableSound(button));
    });

    window.addEventListener('repomart:notification', () => {
        if (document.body.dataset.soundEnabled === '1') {
            playAlert();
        }
    });
}

function enableSound(button) {
    alertAudio = new Audio('/sounds/alert.mp3');
    alertAudio.play().then(async () => {
        alertAudio.pause();
        alertAudio.currentTime = 0;
        await window.axios.patch('/account/sound-alerts', { enabled: true });
        document.body.dataset.soundEnabled = '1';
        button.textContent = 'Sound Alerts Enabled';
        button.disabled = true;
        const statusEl = document.querySelector('[data-sound-status]');
        if (statusEl) statusEl.textContent = 'Enabled';
    }).catch(() => {
        alert('Could not enable sound. Please interact with the page and try again.');
    });
}

function playAlert() {
    if (!alertAudio) {
        alertAudio = new Audio('/sounds/alert.wav');
    }
    alertAudio.currentTime = 0;
    alertAudio.play().catch(() => {});
}

export function initMicrophone() {
    document.querySelectorAll('[data-enable-microphone]').forEach((button) => {
        button.addEventListener('click', async () => {
            try {
                await navigator.mediaDevices.getUserMedia({ audio: true });
                await window.axios.post('/account/permissions', { permission: 'microphone', granted: true });
                const statusEl = document.querySelector('[data-mic-status]');
                if (statusEl) statusEl.textContent = 'Allowed';
                button.textContent = 'Microphone Allowed';
                button.disabled = true;
            } catch (e) {
                alert('Microphone permission was denied.');
                await window.axios.post('/account/permissions', { permission: 'microphone', granted: false });
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initMicrophone();
});
