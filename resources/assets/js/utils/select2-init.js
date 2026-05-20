export function initSelect2(elements, { dropdownParent, ...options } = {}) {
    const $ = window.jQuery;
    if (!$ || typeof $.fn?.select2 !== 'function') return false;

    const list = Array.isArray(elements) ? elements : [elements];
    list.forEach((el) => {
        if (!el) return;
        $(el).select2({
            minimumResultsForSearch: Infinity,
            width: '100%',
            ...(dropdownParent ? { dropdownParent: $(dropdownParent) } : {}),
            ...options,
        });
    });
    return true;
}

export function resetSelect2(el) {
    const $ = window.jQuery;
    if (!el) return;
    el.value = '';
    if ($ && $.fn?.select2) $(el).trigger('change.select2');
}
