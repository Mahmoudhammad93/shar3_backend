@php
    $theme = \App\Support\AdminPanelSettings::themeVariables();
    $darkSidebar = $theme['sidebarStyle'] === 'dark';
@endphp
<style>
    :root {
        --admin-panel-sidebar: {{ $theme['sidebar'] }};
        --admin-panel-sidebar-dark: {{ $theme['sidebarDark'] }};
        --admin-panel-accent: {{ $theme['accent'] }};
        --admin-panel-background: {{ $theme['background'] }};
    }

    /* ─── Light mode: branded admin theme ─── */
    html:not(.dark) .fi-body {
        background-color: var(--admin-panel-background) !important;
    }

    @if ($darkSidebar)
        html:not(.dark) .fi-sidebar,
        html:not(.dark) .fi-sidebar-header-ctn,
        html:not(.dark) .fi-sidebar-header,
        html:not(.dark) .fi-sidebar-nav,
        html:not(.dark) .fi-sidebar-footer,
        html:not(.dark) .fi-main-sidebar {
            background-color: var(--admin-panel-sidebar-dark) !important;
        }

        html:not(.dark) .fi-sidebar-item-label,
        html:not(.dark) .fi-sidebar-group-label {
            color: rgb(255 255 255 / 0.88);
        }

        html:not(.dark) .fi-sidebar-item-btn {
            color: rgb(255 255 255 / 0.82);
        }

        html:not(.dark) .fi-sidebar-item-btn > .fi-icon,
        html:not(.dark) .fi-sidebar-group-btn > .fi-icon {
            color: rgb(255 255 255 / 0.72);
        }

        html:not(.dark) .fi-sidebar-item-btn:hover,
        html:not(.dark) .fi-sidebar-item-btn:focus-visible {
            background-color: rgb(255 255 255 / 0.08) !important;
        }

        html:not(.dark) .fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
            background-color: rgb(255 255 255 / 0.1) !important;
        }

        html:not(.dark) .fi-sidebar-nav-groups {
            --tw-ring-color: rgb(255 255 255 / 0.08);
        }
    @else
        html:not(.dark) .fi-sidebar,
        html:not(.dark) .fi-sidebar-header {
            background-color: #ffffff !important;
            border-inline-end: 1px solid rgb(0 0 0 / 0.06);
        }

        html.dark .fi-sidebar,
        html.dark .fi-sidebar-header-ctn,
        html.dark .fi-sidebar-header,
        html.dark .fi-sidebar-nav,
        html.dark .fi-sidebar-footer,
        html.dark .fi-main-sidebar {
            background-color: var(--admin-panel-sidebar-dark) !important;
        }

        html.dark .fi-sidebar-item-label,
        html.dark .fi-sidebar-group-label {
            color: rgb(255 255 255 / 0.9);
        }

        html.dark .fi-sidebar-item-btn > .fi-icon,
        html.dark .fi-sidebar-group-btn > .fi-icon {
            color: rgb(255 255 255 / 0.72);
        }
    @endif

    @if ($theme['showPattern'])
        html:not(.dark) .fi-sidebar::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.06;
            pointer-events: none;
            background-image: radial-gradient(circle at 1px 1px, rgb(255 255 255 / 0.35) 1px, transparent 0);
            background-size: 24px 24px;
        }
    @endif

    @if ($theme['style'] === 'modern')
        html:not(.dark) .fi-section,
        html:not(.dark) .fi-ta-ctn,
        html:not(.dark) .fi-wi-stats-overview-stat {
            box-shadow: 0 8px 28px -10px rgb(0 0 0 / 0.12) !important;
        }
    @endif

    @if ($theme['style'] === 'minimal')
        html:not(.dark) .fi-section,
        html:not(.dark) .fi-ta-ctn,
        html:not(.dark) .fi-wi-stats-overview-stat {
            box-shadow: none !important;
        }
    @endif

    /* ─── Dark mode: keep Filament defaults, fix branded sidebar + readability ─── */
    @if ($darkSidebar)
        html.dark .fi-sidebar,
        html.dark .fi-sidebar-header-ctn,
        html.dark .fi-sidebar-header,
        html.dark .fi-sidebar-nav,
        html.dark .fi-sidebar-footer,
        html.dark .fi-main-sidebar {
            background-color: var(--admin-panel-sidebar-dark) !important;
        }

        html.dark .fi-sidebar.fi-sidebar-open {
            box-shadow: none !important;
            border-inline-start: 1px solid rgb(255 255 255 / 0.08);
        }

        html.dark .fi-sidebar-item-label,
        html.dark .fi-sidebar-group-label {
            color: rgb(255 255 255 / 0.9);
        }

        html.dark .fi-sidebar-item-btn {
            color: rgb(255 255 255 / 0.82);
        }

        html.dark .fi-sidebar-item-btn > .fi-icon,
        html.dark .fi-sidebar-group-btn > .fi-icon {
            color: rgb(255 255 255 / 0.72);
        }

        html.dark .fi-sidebar-item-btn:hover,
        html.dark .fi-sidebar-item-btn:focus-visible {
            background-color: rgb(255 255 255 / 0.08) !important;
        }

        html.dark .fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
            background-color: rgb(255 255 255 / 0.1) !important;
        }

        @if ($theme['showPattern'])
            html.dark .fi-sidebar::before {
                content: '';
                position: absolute;
                inset: 0;
                opacity: 0.05;
                pointer-events: none;
                background-image: radial-gradient(circle at 1px 1px, rgb(255 255 255 / 0.35) 1px, transparent 0);
                background-size: 24px 24px;
            }
        @endif
    @endif

    html.dark .fi-sidebar-item-active .fi-sidebar-item-label,
    html.dark .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-sidebar-item-label {
        color: var(--admin-panel-accent) !important;
    }

    /* Forms — create / edit / settings pages */
    html.dark .fi-input-wrp {
        background-color: rgb(255 255 255 / 0.06) !important;
        --tw-ring-color: rgb(255 255 255 / 0.18) !important;
    }

    html.dark .fi-input-wrp:focus-within {
        --tw-ring-color: color-mix(in srgb, var(--admin-panel-accent) 65%, white) !important;
    }

    html.dark .fi-input,
    html.dark .fi-select-input,
    html.dark textarea.fi-input,
    html.dark .fi-fo-rich-editor-content {
        color: rgb(255 255 255 / 0.92) !important;
    }

    html.dark .fi-input::placeholder,
    html.dark textarea.fi-input::placeholder {
        color: rgb(255 255 255 / 0.42) !important;
    }

    html.dark .fi-fo-field-label-content,
    html.dark .fi-fo-field-label span {
        color: rgb(255 255 255 / 0.92) !important;
    }

    html.dark .fi-fo-field-wrp-helper-text,
    html.dark .fi-fo-field-wrp-hint {
        color: rgb(255 255 255 / 0.55) !important;
    }

    /* Tables — list/index pages */
    html.dark .fi-ta-ctn {
        background-color: rgb(17 24 39) !important;
        --tw-ring-color: rgb(255 255 255 / 0.1) !important;
    }

    html.dark .fi-ta-header-cell,
    html.dark .fi-ta-cell {
        color: rgb(255 255 255 / 0.88);
    }

    html.dark .fi-ta-empty-state-heading {
        color: rgb(255 255 255 / 0.9);
    }

    html.dark .fi-ta-empty-state-description {
        color: rgb(255 255 255 / 0.55);
    }

    /* Sections, widgets, dashboard */
    html.dark .fi-section:not(.fi-section-not-contained):not(.fi-aside) {
        background-color: rgb(17 24 39) !important;
        --tw-ring-color: rgb(255 255 255 / 0.1) !important;
    }

    html.dark .fi-section-header-heading,
    html.dark .fi-header-heading,
    html.dark .fi-simple-header-heading {
        color: rgb(255 255 255 / 0.95) !important;
    }

    html.dark .fi-section-header-description,
    html.dark .fi-header-subheading,
    html.dark .fi-simple-header-subheading {
        color: rgb(255 255 255 / 0.58) !important;
    }

    html.dark .fi-wi-stats-overview-stat {
        background-color: rgb(17 24 39) !important;
        --tw-ring-color: rgb(255 255 255 / 0.1) !important;
    }

    html.dark .fi-wi-stats-overview-stat-value {
        color: rgb(255 255 255 / 0.95);
    }

    html.dark .fi-wi-stats-overview-stat-label {
        color: rgb(255 255 255 / 0.58);
    }

    /* Topbar, breadcrumbs, page chrome */
    html.dark .fi-topbar {
        background-color: rgb(17 24 39) !important;
        --tw-ring-color: rgb(255 255 255 / 0.1) !important;
    }

    html.dark .fi-topbar-item-label {
        color: rgb(255 255 255 / 0.88);
    }

    html.dark .fi-breadcrumbs-item-label {
        color: rgb(255 255 255 / 0.65);
    }

    html.dark .fi-breadcrumbs-item-label-is-current,
    html.dark .fi-breadcrumbs-item-label:hover {
        color: rgb(255 255 255 / 0.92);
    }

    /* Modals, dropdowns, notifications */
    html.dark .fi-modal-window,
    html.dark .fi-dropdown-panel,
    html.dark .fi-fo-repeater-item {
        background-color: rgb(17 24 39) !important;
        --tw-ring-color: rgb(255 255 255 / 0.12) !important;
    }

    html.dark .fi-dropdown-list-item-label {
        color: rgb(255 255 255 / 0.88);
    }

    /* Login page */
    html.dark .fi-simple-main {
        background-color: rgb(17 24 39) !important;
        --tw-ring-color: rgb(255 255 255 / 0.1) !important;
    }

    /* Secondary / gray action buttons */
    html.dark .fi-btn.fi-color-gray {
        --tw-ring-color: rgb(255 255 255 / 0.15) !important;
        color: rgb(255 255 255 / 0.88) !important;
    }

    html.dark .fi-btn.fi-color-gray:hover {
        background-color: rgb(255 255 255 / 0.08) !important;
    }

    /* Tabs (relation managers, settings) */
    html.dark .fi-tabs-item-label {
        color: rgb(255 255 255 / 0.65);
    }

    html.dark .fi-tabs-item.fi-active .fi-tabs-item-label {
        color: rgb(255 255 255 / 0.95);
    }

    /* Shared — both modes */
    @if ($theme['compactMode'])
        .fi-main {
            --fi-main-padding: 1rem;
        }

        .fi-page,
        .fi-section-content {
            gap: 0.75rem;
        }
    @endif

    .fi-sidebar-item-active .fi-sidebar-item-button,
    .fi-sidebar-item-button:hover,
    .fi-sidebar-item-btn:hover {
        --tw-ring-color: color-mix(in srgb, var(--admin-panel-accent) 35%, transparent);
    }

    html:not(.dark) .fi-sidebar-item-active .fi-sidebar-item-label,
    html:not(.dark) .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-sidebar-item-label {
        color: var(--admin-panel-accent) !important;
    }
</style>
