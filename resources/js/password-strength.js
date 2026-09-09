const MIN_LENGTH = 8;
const SEGMENT_COUNT = 10;

const HINTS = [
    { key: 'length', label: `Minimum number of characters is ${MIN_LENGTH}.`, test: (value) => value.length >= MIN_LENGTH },
    { key: 'lowercase', label: 'Should contain lowercase.', test: (value) => /[a-z]/.test(value) },
    { key: 'uppercase', label: 'Should contain uppercase.', test: (value) => /[A-Z]/.test(value) },
    { key: 'number', label: 'Should contain numbers.', test: (value) => /[0-9]/.test(value) },
    { key: 'special', label: 'Should contain special characters.', test: (value) => /[^A-Za-z0-9]/.test(value) },
];

function evaluatePassword(value) {
    const checks = Object.fromEntries(HINTS.map(({ key, test }) => [key, test(value)]));
    const score = Object.values(checks).filter(Boolean).length;

    let level = 'empty';
    let label = 'Empty';

    if (score <= 2 && score > 0) {
        level = 'weak';
        label = 'Weak';
    } else if (score === 3) {
        level = 'fair';
        label = 'Fair';
    } else if (score === 4) {
        level = 'good';
        label = 'Good';
    } else if (score >= 5) {
        level = 'strong';
        label = 'Strong';
    }

    return { score, level, label, checks };
}

function isStrongPassword(password) {
    return evaluatePassword(password).score >= HINTS.length;
}

const LEVEL_STYLES = {
    empty: { text: 'text-gray-900', bar: 'bg-gray-300' },
    weak: { text: 'text-red-600', bar: 'bg-red-500' },
    fair: { text: 'text-orange-600', bar: 'bg-orange-500' },
    good: { text: 'text-brand-600', bar: 'bg-brand-500' },
    strong: { text: 'text-forest-600', bar: 'bg-forest-600' },
};

function filledSegments(score) {
    if (score <= 0) {
        return 0;
    }

    return Math.min(SEGMENT_COUNT, score * 2);
}

function updateStrengthPanel(root, password) {
    const label = root.querySelector('[data-strength-label]');
    const segments = root.querySelectorAll('[data-strength-segment]');

    if (! label) {
        return;
    }

    const { score, level, label: strengthLabel, checks } = evaluatePassword(password);
    const styles = LEVEL_STYLES[level];
    const activeSegments = filledSegments(score);

    label.textContent = `${strengthLabel} (${activeSegments}/${SEGMENT_COUNT})`;
    label.className = `font-semibold ${styles.text}`;

    segments.forEach((segment, index) => {
        segment.className = 'h-2.5 flex-1 rounded-sm transition-colors duration-200';

        if (index < activeSegments) {
            segment.classList.add(styles.bar);
        } else {
            segment.classList.add('bg-brand-100');
        }
    });

    HINTS.forEach(({ key }) => {
        const hint = root.querySelector(`[data-hint="${key}"]`);

        if (! hint) {
            return;
        }

        const met = checks[key];
        hint.className = 'flex items-center gap-2 text-sm transition-colors duration-200';

        const icon = hint.querySelector('[data-hint-icon]');
        const text = hint.querySelector('[data-hint-text]');

        if (icon) {
            icon.className = met
                ? 'ri-check-line text-sm text-forest-600'
                : 'ri-close-line text-sm text-gray-400';
        }

        if (text) {
            text.className = met
                ? 'font-medium text-forest-600'
                : 'text-gray-500';
        }
    });
}

function updateMatchIndicator(root, password, confirmation) {
    const matchPanel = root.querySelector('[data-password-match-panel]');
    const matchLabel = root.querySelector('[data-password-match-label]');

    if (! matchPanel || ! matchLabel) {
        return;
    }

    if (! confirmation) {
        matchPanel.classList.add('hidden');
        return;
    }

    matchPanel.classList.remove('hidden');

    const matches = password === confirmation && password.length > 0;

    matchLabel.className = matches
        ? 'flex items-center gap-2 text-sm font-medium text-forest-700'
        : 'flex items-center gap-2 text-sm font-medium text-red-600';

    matchLabel.innerHTML = matches
        ? '<i class="ri-check-line text-sm" aria-hidden="true"></i><span>Passwords match</span>'
        : '<i class="ri-close-line text-sm" aria-hidden="true"></i><span>Passwords do not match</span>';
}

function updateContinueButton(root, password, confirmation) {
    const button = root.querySelector('[data-continue-button]');

    if (! button) {
        return;
    }

    const canContinue = isStrongPassword(password)
        && confirmation.length > 0
        && password === confirmation;

    button.disabled = ! canContinue;
}

function bindPasswordStrength(root) {
    if (root.dataset.passwordStrengthBound === 'true') {
        return;
    }

    root.dataset.passwordStrengthBound = 'true';

    const passwordInput = root.querySelector('[data-password-input]');
    const confirmInput = root.querySelector('[data-password-confirm]');

    if (! passwordInput) {
        return;
    }

    const sync = () => {
        const password = passwordInput.value;
        const confirmation = confirmInput?.value ?? '';

        updateStrengthPanel(root, password);
        updateMatchIndicator(root, password, confirmation);
        updateContinueButton(root, password, confirmation);
    };

    ['input', 'keyup', 'paste', 'change'].forEach((eventName) => {
        passwordInput.addEventListener(eventName, sync);
        confirmInput?.addEventListener(eventName, sync);
    });

    sync();
}

export function initPasswordStrength() {
    document.querySelectorAll('[data-password-strength]').forEach(bindPasswordStrength);
}
