/**
 * Iframe Preview Listener
 * Receives PostMessage updates from parent and applies CSS dynamically
 */

(function () {
    'use strict';

    let dynamicStyleTag = null;

    /**
     * Initialize the listener
     */
    function init() {
        // Create dynamic style tag
        dynamicStyleTag = document.createElement('style');
        dynamicStyleTag.id = 'page-builder-dynamic-styles';
        document.head.appendChild(dynamicStyleTag);

        // Listen for messages from parent
        window.addEventListener('message', handleMessage);

        console.log('Preview listener initialized');
    }

    /**
     * Handle incoming messages
     */
    function handleMessage(event) {
        const { type, css, sections, device, html } = event.data;

        switch (type) {
            case 'updateStyles':
                updateStyles(css);
                break;

            case 'updateContent':
                updateContent(html);
                break;

            case 'updateDevice':
                updateDevice(device);
                break;

            case 'reloadPreview':
                window.location.reload();
                break;
        }
    }

    /**
     * Update CSS styles
     */
    function updateStyles(css) {
        if (!dynamicStyleTag) return;

        dynamicStyleTag.textContent = css;
        console.log('Styles updated:', css.length, 'characters');
    }

    /**
     * Update HTML content
     */
    function updateContent(html) {
        const container = document.getElementById('page-builder-content');
        if (container) {
            container.innerHTML = html;
            console.log('Content updated');
        }
    }

    /**
     * Update device mode
     */
    function updateDevice(device) {
        document.body.setAttribute('data-device', device);

        // Apply device-specific classes
        document.body.classList.remove('device-desktop', 'device-mobile', 'device-tablet');
        document.body.classList.add(`device-${device}`);

        console.log('Device mode:', device);
    }

    /**
     * Send message to parent
     */
    function sendToParent(type, data) {
        window.parent.postMessage({ type, ...data }, '*');
    }

    /**
     * Handle section clicks for editing
     */
    function setupSectionClicks() {
        document.addEventListener('click', (e) => {
            const section = e.target.closest('[data-section-index]');
            if (section) {
                const index = parseInt(section.dataset.sectionIndex);
                sendToParent('sectionSelected', { index });

                // Highlight selected section
                document.querySelectorAll('[data-section-index]').forEach(el => {
                    el.classList.remove('page-builder-selected');
                });
                section.classList.add('page-builder-selected');

                e.preventDefault();
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Setup section clicks after init
    setTimeout(setupSectionClicks, 100);

})();
