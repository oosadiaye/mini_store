/**
 * Defined Tour Steps
 * Selectors must match DOM elements (add id or data-tour attributes to HTML if needed)
 */

export const tourDefinitions = {
    dashboard: [
        {
            element: 'header',  // General area
            popover: {
                title: 'Welcome to your Dashboard',
                description: 'This is your command center. Let\'s take a quick tour of the key features.',
                side: 'bottom',
                align: 'start'
            }
        },
        {
            element: '[data-tour="global-search"]', // We need to add this attribute
            popover: {
                title: 'Smart Search',
                description: 'Press Cmd+K or click here to search for ANY feature instantly (e.g., "Add Product", "Reports").',
                side: 'bottom'
            }
        },
        {
            element: '[data-tour="sidebar-toggle"]', // Sidebar toggle
            popover: {
                title: 'Navigation Menu',
                description: 'Access all your store modules here. On mobile, tap this to open the menu.',
                side: 'bottom'
            }
        },
        {
            element: '[data-tour="user-dropdown"]',
            popover: {
                title: 'Profile & Settings',
                description: 'Manage your account settings, profile, and logout here.',
                side: 'left'
            }
        },
        {
            popover: {
                title: 'You are all set!',
                description: 'Explore the sidebar to manage Inventory, Sales, and more. You can replay this tour anytime from the Help menu.',
            }
        }
    ],

    // Comprehensive Tour covering all requested modules
    full_tour: [
        {
            element: 'header',
            popover: { title: 'Welcome to Mini Store', description: 'Let\'s take a quick tour of your new dashboard features.', side: 'bottom' }
        },
        {
            element: '[data-tour="user-dropdown"]',
            popover: { title: 'Account & Settings', description: 'Manage your profile, password, and subscription settings here.', side: 'left' }
        },
        {
            element: '[href*="products"]',
            popover: { title: 'Inventory & Stock', description: 'Track your products, manage categories, and monitor stock levels.', side: 'right' }
        },
        {
            element: '[href*="pos"]',
            popover: { title: 'Sales & POS', description: 'Process sales in-store with the Point of Sale terminal. Barcode scanner supported!', side: 'right' }
        },
        {
            element: '[href*="store-content"]',
            popover: { title: 'Online Store', description: 'Manage your e-commerce storefront, customize themes, and update content.', side: 'right' }
        },
        {
            element: '[href*="incomes"]',
            popover: { title: 'Accounting', description: 'Track incomes, expenses, and manage your chart of accounts.', side: 'right' }
        },
        {
            element: '[href*="coupons"]',
            popover: { title: 'Marketing', description: 'Create coupons, banners, and manage product enquiries.', side: 'right' }
        },
        {
            element: '[href*="users"]',
            popover: { title: 'Team & Access', description: 'Add staff members and assign roles/permissions.', side: 'right' }
        },
        {
            element: '[href*="reports"]',
            popover: { title: 'Reports & Analytics', description: 'View detailed sales, inventory, and financial reports.', side: 'right' }
        },
        {
            element: '[href*="support"]',
            popover: { title: 'Help & Support', description: 'Raise support tickets or contact customer care.', side: 'right' }
        },
        {
            element: '[data-tour="global-search"]',
            popover: { title: 'Quick Navigation', description: 'Pro Tip: Press Cmd+K to jump to any features instantly!', side: 'bottom' }
        }
    ]
};
