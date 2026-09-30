import { unlockAudioOutput } from '../media-permissions';

let currentUtterance = null;
let currentAudio = null;
let voicesReadyPromise = null;

const languageMap = {
    English: 'en-US',
    Tagalog: 'fil-PH',
    Bisaya: 'ceb-PH',
    Mixed: 'en-US',
};

function pickVoice(langCode) {
    const voices = window.speechSynthesis?.getVoices() ?? [];

    return voices.find((voice) => voice.lang.startsWith(langCode.slice(0, 2)))
        ?? voices.find((voice) => voice.lang.startsWith('en'))
        ?? voices[0]
        ?? null;
}

function waitForVoices(timeoutMs = 2500) {
    return new Promise((resolve) => {
        if (! window.speechSynthesis) {
            resolve([]);
            return;
        }

        const readVoices = () => window.speechSynthesis.getVoices();
        let voices = readVoices();

        if (voices.length) {
            resolve(voices);
            return;
        }

        const timeout = window.setTimeout(() => {
            window.speechSynthesis.onvoiceschanged = null;
            resolve(readVoices());
        }, timeoutMs);

        window.speechSynthesis.onvoiceschanged = () => {
            voices = readVoices();

            if (voices.length) {
                window.clearTimeout(timeout);
                window.speechSynthesis.onvoiceschanged = null;
                resolve(voices);
            }
        };
    });
}

function startSpeechKeepAlive() {
    const interval = window.setInterval(() => {
        if (! window.speechSynthesis?.speaking) {
            window.clearInterval(interval);
            return;
        }

        window.speechSynthesis.pause();
        window.speechSynthesis.resume();
    }, 250);

    return () => window.clearInterval(interval);
}

function stopCurrentAudio() {
    if (! currentAudio) {
        return;
    }

    currentAudio.pause();
    currentAudio.src = '';

    if (currentAudio._objectUrl) {
        URL.revokeObjectURL(currentAudio._objectUrl);
    }

    currentAudio = null;
}

function createUtterance(text, language) {
    const utterance = new SpeechSynthesisUtterance(text.trim());
    const langCode = languageMap[language] ?? 'en-US';

    utterance.lang = langCode;
    utterance.rate = 0.95;
    utterance.pitch = 1;
    utterance.volume = 1;

    const voice = pickVoice(langCode);

    if (voice) {
        utterance.voice = voice;
    }

    return utterance;
}

function estimateSpeechMs(text) {
    return Math.min(20000, Math.max(2500, text.length * 90));
}

async function speakViaBrowser(text, language = 'English') {
    if (! window.speechSynthesis) {
        return false;
    }

    window.speechSynthesis.getVoices();

    return new Promise((resolve) => {
        window.speechSynthesis.cancel();

        const utterance = createUtterance(text, language);
        let finished = false;
        let started = false;

        const finish = (ok) => {
            if (finished) {
                return;
            }

            finished = true;
            stopKeepAlive();
            window.clearTimeout(startTimeout);
            window.clearTimeout(maxTimeout);
            currentUtterance = null;
            resolve(ok);
        };

        const stopKeepAlive = startSpeechKeepAlive();

        utterance.onstart = () => {
            started = true;
            window.clearTimeout(startTimeout);
        };
        utterance.onend = () => finish(true);
        utterance.onerror = () => finish(false);

        const startTimeout = window.setTimeout(() => {
            if (! started) {
                finish(false);
            }
        }, 1200);

        const maxTimeout = window.setTimeout(() => finish(started), estimateSpeechMs(text));

        currentUtterance = utterance;
        window.speechSynthesis.speak(utterance);
        window.speechSynthesis.pause();
        window.speechSynthesis.resume();
    });
}

export function preloadVoices() {
    if (! window.speechSynthesis) {
        return;
    }

    voicesReadyPromise = waitForVoices();
    window.speechSynthesis.getVoices();
}

export function primeSpeechEngine() {
    if (! window.speechSynthesis) {
        return;
    }

    window.speechSynthesis.getVoices();

    const utterance = new SpeechSynthesisUtterance('\u200B');
    utterance.volume = 0.01;
    utterance.lang = 'en-US';
    window.speechSynthesis.speak(utterance);
}

export function speakText(text, language = 'English', options = {}) {
    void speakTextAndWait(text, language, options);
}

export async function speakTextAndWait(text, language = 'English') {
    const cleaned = text?.trim();

    if (! cleaned) {
        return false;
    }

    stopSpeaking();
    await unlockAudioOutput();

    if (! window.speechSynthesis) {
        return false;
    }

    return speakViaBrowser(cleaned, language);
}

export function stopSpeaking() {
    window.speechSynthesis?.cancel();
    currentUtterance = null;
    stopCurrentAudio();
}
