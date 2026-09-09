export const voiceSearchCopy = {
    modal: {
        waiting: "I'm here whenever you're ready. Just tell me what you're looking for.",
        processing: 'Got it! Give me a quick second while I figure out what you said.',
    },
    listening: {
        welcome: "Hey there! Welcome back! What can I help you find today?",
        prompt: "Go ahead — tell me what you need.",
        browserPrompt: "Hey there! What can I help you find today?",
        hearingYou: "Perfect, I'm hearing you.",
        speakNow: 'Your turn — say what you need whenever you are ready.',
        keepSpeaking: "I'm still listening — take your time.",
        finishingUp: 'Almost done — just a moment.',
        lookingUp: 'Thanks! Let me look that up for you now.',
        processingRecording: "Got it! I'm listening back and getting your search ready.",
    },
    search: {
        searching: (term) => `Alright, let me search for ${term} for you.`,
        openingProducts: (term) => `Great pick! I'm opening ${term} in the marketplace now.`,
        heard: (term) => `I heard ${term}. Let me find that for you.`,
    },
    results: {
        allProducts: "Here's everything available in the marketplace right now.",
        none: (term) => `Sorry, I couldn't find anything for ${term}. Want to try saying it a different way?`,
        one: (term) => `Good news! I found 1 item for ${term}.`,
        many: (term, total) => `Great news! I found ${total} items for ${term}.`,
        checkoutHint: 'When you are done shopping, tap the cart icon at the top to checkout.',
    },
    errors: {
        noSpeech: "Sorry, I didn't catch that. Could you say it one more time?",
        failed: 'Oops, something went wrong on my side. Mind trying again?',
        notAllowed: 'I need your microphone so I can hear you. Please allow mic access and try again.',
        network: "I couldn't connect just now. You can also use the mic button at the bottom of the screen.",
        serviceNotAllowed: "Voice search isn't available in this browser, but you can still type your search.",
        audioCapture: "I couldn't reach your microphone. It may be in use by another app.",
    },
};

export function buildHeardTranscriptSpeech(transcript) {
    return voiceSearchCopy.search.heard(transcript.trim());
}

export function buildSearchingSpeech(transcript) {
    return voiceSearchCopy.search.searching(transcript.trim());
}

export function buildOpeningProductsSpeech(transcript) {
    return voiceSearchCopy.search.openingProducts(transcript.trim());
}

export function buildProductsSearchSpeech(query, total) {
    const term = query.trim();

    if (! term) {
        return voiceSearchCopy.results.allProducts;
    }

    if (total <= 0) {
        return voiceSearchCopy.results.none(term);
    }

    if (total === 1) {
        return voiceSearchCopy.results.one(term);
    }

    return voiceSearchCopy.results.many(term, total);
}

export function buildProductsCheckoutHintSpeech() {
    return voiceSearchCopy.results.checkoutHint;
}

export function friendlyVoiceSearchError(message) {
    const normalized = message.trim().toLowerCase();

    if (normalized.includes('no speech detected') || normalized.includes("didn't catch")) {
        return voiceSearchCopy.errors.noSpeech;
    }

    if (normalized.includes('voice search failed') || normalized.includes('something went wrong')) {
        return voiceSearchCopy.errors.failed;
    }

    return message;
}
