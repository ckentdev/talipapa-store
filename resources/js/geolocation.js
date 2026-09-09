import { applyPsgcFromGeocode } from './psgc-dropdowns';

export function initGeolocation() {
    document.querySelectorAll('[data-use-location]').forEach((button) => {
        button.addEventListener('click', () => requestLocation(button));
    });

    document.querySelectorAll('[data-address-form]').forEach((form) => {
        const lat = form.querySelector('[name="latitude"]')?.value;
        const lng = form.querySelector('[name="longitude"]')?.value;
        if (lat && lng) {
            updateMapPreview(form, parseFloat(lat), parseFloat(lng));
        }
    });
}

async function requestLocation(button) {
    const form = button.closest('[data-address-form]') ?? button.closest('form');
    if (! navigator.geolocation) {
        showGeoMessage('Geolocation is not supported by your browser.', 'error');
        return;
    }

    const originalHtml = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span>Getting location...</span>';

    navigator.geolocation.getCurrentPosition(
        async (position) => {
            try {
                const { latitude, longitude } = position.coords;
                if (form) {
                    setField(form, 'latitude', latitude.toFixed(8));
                    setField(form, 'longitude', longitude.toFixed(8));
                }

                updateMapPreview(form, latitude, longitude);

                const { data } = await window.axios.get('/psgc/reverse-geocode', {
                    params: { lat: latitude, lng: longitude },
                });

                if (form) {
                    if (data.street_address) {
                        setField(form, 'street_address', data.street_address);
                    }
                    if (data.postal_code) {
                        setField(form, 'postal_code', data.postal_code);
                    }

                    await applyPsgcFromGeocode(form, data);
                    showDetectedAddress(form, data);
                }

                const matched = data.region_code && data.city_code;
                showGeoMessage(
                    matched
                        ? 'Address detected from your location.'
                        : 'Location pinned on map. Please complete any missing address fields.',
                    matched ? 'success' : 'info',
                );
                savePermissionStatus('location', true);
            } catch (error) {
                console.error(error);
                showGeoMessage('Location captured on map, but address lookup failed. Please fill in your address manually.', 'error');
            } finally {
                button.disabled = false;
                button.innerHTML = originalHtml;
            }
        },
        (error) => {
            const messages = {
                1: 'Location permission denied. Please enable location access in your browser settings.',
                2: 'Location unavailable. Please try again.',
                3: 'Location request timed out. Please try again.',
            };
            showGeoMessage(messages[error.code] ?? 'Unable to get your location.', 'error');
            savePermissionStatus('location', false);
            button.disabled = false;
            button.innerHTML = originalHtml;
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 },
    );
}

function setField(form, name, value) {
    const input = form.querySelector(`[name="${name}"], [data-field="${name}"]`);
    if (input) {
        input.value = value;
    }
}

function showDetectedAddress(form, data) {
    const target = form.querySelector('[data-detected-address]');
    if (! target) return;

    const parts = [
        data.street_address,
        data.barangay_name,
        data.city_name,
        data.province_name,
        data.region_name,
        data.postal_code,
    ].filter(Boolean);

    if (parts.length === 0 && data.display_name) {
        target.textContent = data.display_name;
    } else if (parts.length > 0) {
        target.textContent = parts.join(', ');
    } else {
        target.textContent = '';
    }

    target.closest('[data-detected-address-wrap]')?.classList.toggle('hidden', target.textContent === '');
}

function updateMapPreview(form, lat, lng) {
    const mapEl = form?.querySelector?.('[data-address-map]') ?? document.getElementById('address-map');
    if (! mapEl || ! window.L) return;

    mapEl.classList.remove('hidden');

    if (! mapEl._map) {
        mapEl._map = window.L.map(mapEl).setView([lat, lng], 16);
        window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
        }).addTo(mapEl._map);
        mapEl._marker = window.L.marker([lat, lng], { draggable: true }).addTo(mapEl._map);

        mapEl._marker.on('dragend', async () => {
            const { lat: markerLat, lng: markerLng } = mapEl._marker.getLatLng();
            setField(form, 'latitude', markerLat.toFixed(8));
            setField(form, 'longitude', markerLng.toFixed(8));
        });
    } else {
        mapEl._map.setView([lat, lng], 16);
        mapEl._marker.setLatLng([lat, lng]);
    }

    requestAnimationFrame(() => {
        mapEl._map.invalidateSize();
    });
}

function showGeoMessage(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('repomart:toast', { detail: { message, type } }));
}

async function savePermissionStatus(permission, granted) {
    try {
        await window.axios.post('/account/permissions', { permission, granted });
    } catch (e) {
        // silent when guest or unauthenticated
    }
}

export { requestLocation, updateMapPreview };
