import { createVoiceLevelMonitor } from '../voice-level-monitor';

const MAX_RECORD_MS = 20000;

export function createVoiceRecorder({ onStream, onLevel, onSpeakingChange } = {}) {
    let mediaRecorder = null;
    let chunks = [];
    let stream = null;
    let stopTimer = null;
    let levelMonitor = null;

    async function ensureStream() {
        if (! navigator.mediaDevices?.getUserMedia) {
            throw new Error('Microphone is not supported in this browser.');
        }

        stream = await navigator.mediaDevices.getUserMedia({ audio: true });

        return stream;
    }

    function pickMimeType() {
        const types = ['audio/webm;codecs=opus', 'audio/webm', 'audio/ogg;codecs=opus', 'audio/mp4'];

        return types.find((type) => MediaRecorder.isTypeSupported(type)) ?? '';
    }

    function stopLevelMonitor() {
        levelMonitor?.stop();
        levelMonitor = null;
    }

    function cleanupStream() {
        stream?.getTracks().forEach((track) => track.stop());
        stream = null;
    }

    return {
        async start() {
            chunks = [];
            stream = await ensureStream();
            onStream?.(stream);

            if (onLevel || onSpeakingChange) {
                try {
                    levelMonitor = await createVoiceLevelMonitor(stream, { onLevel, onSpeakingChange });
                } catch {
                    levelMonitor = null;
                }
            }

            const mimeType = pickMimeType();
            const options = mimeType ? { mimeType } : undefined;
            mediaRecorder = new MediaRecorder(stream, options);

            mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    chunks.push(event.data);
                }
            };

            return new Promise((resolve, reject) => {
                mediaRecorder.onerror = () => {
                    stopLevelMonitor();
                    cleanupStream();
                    reject(new Error('Recording failed.'));
                };

                mediaRecorder.onstop = () => {
                    clearTimeout(stopTimer);
                    stopLevelMonitor();

                    const type = mediaRecorder.mimeType || mimeType || 'audio/webm';
                    const blob = new Blob(chunks, { type });
                    cleanupStream();
                    resolve(blob);
                };

                try {
                    mediaRecorder.start(250);
                } catch (error) {
                    stopLevelMonitor();
                    cleanupStream();
                    reject(error);
                    return;
                }

                stopTimer = window.setTimeout(() => {
                    if (mediaRecorder?.state === 'recording') {
                        mediaRecorder.stop();
                    }
                }, MAX_RECORD_MS);
            });
        },

        stop() {
            if (mediaRecorder?.state === 'recording') {
                try {
                    mediaRecorder.requestData();
                } catch {
                    // Some browsers do not support requestData().
                }

                mediaRecorder.stop();
            }
        },

        isRecording() {
            return mediaRecorder?.state === 'recording';
        },
    };
}
