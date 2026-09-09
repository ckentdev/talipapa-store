export function initPsgcDropdowns() {
    document.querySelectorAll('[data-psgc-form]').forEach((form) => {
        const regionSelect = form.querySelector('[data-psgc-region]');
        const provinceSelect = form.querySelector('[data-psgc-province]');
        const citySelect = form.querySelector('[data-psgc-city]');
        const barangaySelect = form.querySelector('[data-psgc-barangay]');

        if (!regionSelect) return;

        regionSelect.addEventListener('change', () => loadProvinces(regionSelect, provinceSelect, citySelect, barangaySelect));
        provinceSelect?.addEventListener('change', () => loadCities(provinceSelect, citySelect, barangaySelect));
        citySelect?.addEventListener('change', () => loadBarangays(citySelect, barangaySelect));

        if (regionSelect.value || regionSelect.dataset.selected) {
            if (! regionSelect.value && regionSelect.dataset.selected) {
                regionSelect.value = regionSelect.dataset.selected;
            }

            loadProvinces(regionSelect, provinceSelect, citySelect, barangaySelect, {
                province: provinceSelect?.dataset.selected,
                city: citySelect?.dataset.selected,
                barangay: barangaySelect?.dataset.selected,
            });
        }
    });
}

export async function applyPsgcFromGeocode(form, codes = {}) {
    const psgcForm = form?.closest?.('[data-psgc-form]') ?? form;
    if (! psgcForm) return;

    const regionSelect = psgcForm.querySelector('[data-psgc-region]');
    const provinceSelect = psgcForm.querySelector('[data-psgc-province]');
    const citySelect = psgcForm.querySelector('[data-psgc-city]');
    const barangaySelect = psgcForm.querySelector('[data-psgc-barangay]');

    if (codes.region_code && regionSelect) {
        regionSelect.value = codes.region_code;
        await loadProvinces(regionSelect, provinceSelect, citySelect, barangaySelect, {
            province: codes.province_code,
            city: codes.city_code,
            barangay: codes.barangay_code,
        });
    }
}

async function loadProvinces(regionSelect, provinceSelect, citySelect, barangaySelect, selected = {}) {
    resetSelect(provinceSelect, 'Select Province / District');
    resetSelect(citySelect, 'Select City/Municipality');
    resetSelect(barangaySelect, 'Select Barangay');

    const code = regionSelect.value;
    if (! code) return;

    const { data } = await window.axios.get(`/psgc/provinces/${code}`);
    populateSelect(provinceSelect, data);

    if (selected.province) {
        provinceSelect.value = selected.province;
        await loadCities(provinceSelect, citySelect, barangaySelect, {
            city: selected.city,
            barangay: selected.barangay,
        });
    }
}

async function loadCities(provinceSelect, citySelect, barangaySelect, selected = {}) {
    resetSelect(citySelect, 'Select City/Municipality');
    resetSelect(barangaySelect, 'Select Barangay');

    const code = provinceSelect.value;
    if (! code) return;

    const { data } = await window.axios.get(`/psgc/cities/${code}`);
    populateSelect(citySelect, data);

    if (selected.city) {
        citySelect.value = selected.city;
        await loadBarangays(citySelect, barangaySelect, selected.barangay);
    }
}

async function loadBarangays(citySelect, barangaySelect, selectedBarangay = null) {
    resetSelect(barangaySelect, 'Select Barangay');

    const code = citySelect.value;
    if (! code) return;

    const { data } = await window.axios.get(`/psgc/barangays/${code}`);
    populateSelect(barangaySelect, data);

    if (selectedBarangay) {
        barangaySelect.value = selectedBarangay;
    }
}

function populateSelect(select, items) {
    if (! select) return;

    items.forEach((item) => {
        const option = document.createElement('option');
        option.value = item.code;
        option.textContent = item.name;
        select.appendChild(option);
    });
}

function resetSelect(select, placeholder) {
    if (! select) return;
    select.innerHTML = `<option value="">${placeholder}</option>`;
}
