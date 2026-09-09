export async function createVoiceLevelMonitor(stream, { onLevel, onSpeakingChange } = {}) {
    const AudioContextClass = window.AudioContext || window.webkitAudioContext;

    if (! AudioContextClass || ! stream) {
        return { stop() {} };
    }

    const context = new AudioContextClass();

    if (context.state === 'suspended') {
        try {
            await context.resume();
        } catch {
            return { stop() {} };
        }
    }

    const source = context.createMediaStreamSource(stream);
    const analyser = context.createAnalyser();

    analyser.fftSize = 256;
    analyser.smoothingTimeConstant = 0.75;
    source.connect(analyser);

    const frequencyData = new Uint8Array(analyser.frequencyBinCount);
    let animationFrame = null;
    let speaking = false;

    const sample = () => {
        analyser.getByteFrequencyData(frequencyData);

        const average = frequencyData.reduce((sum, value) => sum + value, 0) / frequencyData.length;
        const level = Math.min(1, average / 96);

        onLevel?.(level);

        const isSpeaking = level > 0.07;

        if (isSpeaking !== speaking) {
            speaking = isSpeaking;
            onSpeakingChange?.(isSpeaking);
        }

        animationFrame = window.requestAnimationFrame(sample);
    };

    animationFrame = window.requestAnimationFrame(sample);

    return {
        stop() {
            if (animationFrame) {
                window.cancelAnimationFrame(animationFrame);
            }

            source.disconnect();
            context.close().catch(() => {});
        },
    };
}
