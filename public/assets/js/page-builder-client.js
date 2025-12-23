/**
 * Page Builder Client Script
 * Injected into the storefront iframe to enable visual editing interactions.
 */
document.addEventListener('DOMContentLoaded', () => {
    // Only run if we are in an iframe
    if (window.self === window.top) return;

    console.log('Page Builder Client Loaded');

    // Inject Styles
    const style = document.createElement('style');
    style.textContent = `
        .pb-hover-outline {
            outline: 2px solid #4f46e5 !important;
            outline-offset: -2px;
            cursor: pointer;
            z-index: 50; 
        }
        .pb-section-wrapper {
            min-height: 20px; 
            display: block;
        }
    `;
    document.head.appendChild(style);

    function attachListeners() {
        const sections = document.querySelectorAll('[data-pb-index]');
        console.log('Attaching listeners to', sections.length, 'sections');

        sections.forEach(section => {
            const target = section;

            // Remove old listeners? 
            // Since we are replacing HTML, elements are new, so no need to detach old listeners from old elements.
            // But if we re-attach to existing? safer to just assume new.

            target.addEventListener('mouseover', (e) => {
                e.stopPropagation();
                target.classList.add('pb-hover-outline');
            });

            target.addEventListener('mouseout', (e) => {
                e.stopPropagation();
                target.classList.remove('pb-hover-outline');
            });

            target.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const index = section.getAttribute('data-pb-index');
                console.log('Selected Section Index:', index);

                window.parent.postMessage({
                    type: 'sectionSelected',
                    index: parseInt(index)
                }, '*');
            });
        });
    }

    // Initial Attach
    attachListeners();

    // Listen for Updates from Parent
    window.addEventListener('message', (event) => {
        if (event.data.type === 'updateContent' && event.data.html) {
            console.log('Received HTML Update');
            const canvas = document.getElementById('pb-canvas');
            if (canvas) {
                canvas.innerHTML = event.data.html;
                // Re-attach listeners to new elements
                attachListeners();
            }
        }
    });
});
