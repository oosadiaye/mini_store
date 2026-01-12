/**
 * CMS Deep Setter Utility
 * Updates a deeply nested property in an object based on a path string.
 * Supports dot notation and array indices (e.g., 'sections[2].data.title').
 * Returns a new object (immutability) to ensure Alpine reactivity triggers.
 *
 * @param {Object} obj - The source object
 * @param {String} path - The path to update (e.g. 'hero.title')
 * @param {Any} value - The new value
 * @returns {Object} - A new deep-cloned object with the update applied
 */
export function updateConfigByPath(obj, path, value) {
    if (!obj || typeof obj !== 'object') return obj;

    // Deep clone to ensure immutability and consistent reactivity
    const newObj = JSON.parse(JSON.stringify(obj));

    // Split path into keys (handling dots and brackets)
    // defined as: matches property names or array indices [0]
    // Regex: match generic word characters OR value inside brackets
    const keys = path.replace(/\[(\d+)\]/g, '.$1').split('.');

    let current = newObj;

    for (let i = 0; i < keys.length; i++) {
        const key = keys[i];

        // If we're at the last key, set the value
        if (i === keys.length - 1) {
            current[key] = value;
        } else {
            // Otherwise, navigate deeper. Create structure if missing.
            if (!current[key]) {
                // If next key is a number, create array, else object
                current[key] = isNaN(keys[i + 1]) ? {} : [];
            }
            current = current[key];
        }
    }

    return newObj;
}
