export function voiceFilename(blob) {
    const type = (blob.type || 'audio/webm').split(';')[0].trim();
    const ext = {
        'audio/webm': 'webm',
        'audio/mp4': 'mp4',
        'audio/ogg': 'ogg',
        'audio/mpeg': 'mp3',
        'audio/wav': 'wav',
    }[type] ?? 'webm';

    return `voice.${ext}`;
}
