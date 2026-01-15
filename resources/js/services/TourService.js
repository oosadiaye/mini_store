import { driver } from "driver.js";
import "driver.js/dist/driver.css";

class TourManager {
    constructor() {
        this.driverObj = null;
        this.tours = {};
        this.currentTourId = null;
        this.tenantSlug = window.location.pathname.split('/')[1] || 'demo';
        this.completedTours = window.user ? (window.user.tours_completed || []) : [];
    }

    /**
     * Initialize the Tour Manager with definitions
     * @param {Object} tourDefinitions - Dictionary of tour steps
     */
    init(tourDefinitions) {
        this.tours = tourDefinitions;

        // Auto-start 'full_tour' if not completed
        if (!this.hasCompleted('full_tour') && window.location.pathname.endsWith('/admin/dashboard')) {
            // Small delay to ensure UI is ready
            setTimeout(() => this.startTour('full_tour'), 1500);
        }
    }

    /**
     * Start a specific tour by ID
     * @param {String} tourId 
     */
    startTour(tourId) {
        const steps = this.tours[tourId];
        if (!steps) {
            console.error(`Tour '${tourId}' not found.`);
            return;
        }

        this.currentTourId = tourId;

        // Mobile Sidebar Logic: Ensure sidebar is open for sidebar steps
        // This is a naive check; ideally we check screen width
        const isMobile = window.innerWidth < 768;

        // Configure Driver
        this.driverObj = driver({
            showProgress: true,
            animate: true,
            allowClose: true,  // Allow user to close/pause
            doneBtnText: 'Finish',
            nextBtnText: 'Next',
            prevBtnText: 'Previous',
            onHighlightStarted: (element, step, options) => {
                // If on mobile and step targets sidebar, try to open it
                if (isMobile && step.popover && step.popover.side === 'right') {
                    // Assume sidebar steps are mostly left-aligned or explicitly sidebar
                    // We might need a better heuristic, but let's see if we can trigger the toggle
                    const sidebarToggle = document.querySelector('[data-tour="sidebar-toggle"]');
                    if (sidebarToggle) sidebarToggle.click();
                }
            },
            onDestroyed: () => {
                // Determine if fully completed or just closed
                if (this.driverObj.hasNextStep()) {
                    // Closed early (Paused) - Do nothing or save progress locally
                    console.log('Tour paused/closed early');
                } else {
                    // Completed
                    this.markAsCompleted(this.currentTourId);
                }
            }
        });

        // Pre-process steps to inject dynamic tenant slug
        const processedSteps = steps.map(step => {
            // Deep copy to avoid mutating original
            const newStep = JSON.parse(JSON.stringify(step));
            // You can add more dynamic logic here if needed
            return newStep;
        });

        this.driverObj.setSteps(processedSteps);
        this.driverObj.drive();
    }

    /**
     * Check if a tour has been completed
     */
    hasCompleted(tourId) {
        return this.completedTours.includes(tourId);
    }

    /**
     * Mark tour as completed in backend
     */
    async markAsCompleted(tourId) {
        if (!this.completedTours.includes(tourId)) {
            this.completedTours.push(tourId);

            try {
                await fetch(`/${this.tenantSlug}/api/user/tour-complete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ tour_id: tourId })
                });
                console.log(`Tour '${tourId}' marked as completed.`);
            } catch (err) {
                console.error('Failed to save tour progress', err);
            }
        }
    }
}

export const TourService = new TourManager();
