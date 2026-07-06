<link rel="stylesheet" href="{{ asset('common/vendors/datetimepicker/css/bootstrap-datetimepicker.min.css') }}">
<style>
    .system-notice-form-page .admin-datetime-input-group .form-control {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .system-notice-form-page .admin-datetime-input-group .admin-datetime-trigger {
        min-width: 2.75rem;
        border-color: var(--bs-border-color);
        color: var(--bs-secondary-color);
        background: var(--bs-tertiary-bg);
        cursor: pointer;
    }

    .system-notice-form-page .admin-datetime-input-group:focus-within .admin-datetime-trigger {
        border-color: rgba(var(--bs-primary-rgb), .6);
        color: var(--bs-primary);
        background: rgba(var(--bs-primary-rgb), .08);
    }

    .bootstrap-datetimepicker-widget.dropdown-menu {
        z-index: 2055 !important;
        width: auto !important;
        min-width: 20rem;
        max-width: min(42rem, calc(100vw - 2rem));
        padding: .75rem !important;
        border: 1px solid var(--bs-border-color-translucent) !important;
        border-radius: .75rem !important;
        background: var(--bs-body-bg) !important;
        color: var(--bs-body-color) !important;
        box-shadow: var(--bs-box-shadow-lg) !important;
    }

    .bootstrap-datetimepicker-widget.dropdown-menu.timepicker-sbs {
        width: 38rem !important;
    }

    .bootstrap-datetimepicker-widget table {
        margin: 0;
        color: var(--bs-body-color);
    }

    .bootstrap-datetimepicker-widget table th,
    .bootstrap-datetimepicker-widget table td {
        border-radius: .45rem;
        color: var(--bs-body-color);
    }

    .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
    .bootstrap-datetimepicker-widget table td.day:hover,
    .bootstrap-datetimepicker-widget table td.hour:hover,
    .bootstrap-datetimepicker-widget table td.minute:hover,
    .bootstrap-datetimepicker-widget table td.second:hover,
    .bootstrap-datetimepicker-widget table td span:hover {
        background: var(--bs-tertiary-bg);
    }

    .bootstrap-datetimepicker-widget table td.active,
    .bootstrap-datetimepicker-widget table td.active:hover,
    .bootstrap-datetimepicker-widget table td span.active {
        background: var(--bs-primary);
        color: #fff;
        text-shadow: none;
    }

    .bootstrap-datetimepicker-widget table td.old,
    .bootstrap-datetimepicker-widget table td.new,
    .bootstrap-datetimepicker-widget table th.disabled,
    .bootstrap-datetimepicker-widget table td.disabled,
    .bootstrap-datetimepicker-widget table td.disabled:hover {
        color: var(--bs-secondary-color);
    }

    .bootstrap-datetimepicker-widget a[data-action],
    .bootstrap-datetimepicker-widget .btn[data-action] {
        border: 0;
        color: var(--bs-primary);
        background: transparent;
    }

    .bootstrap-datetimepicker-widget .picker-switch td span {
        color: var(--bs-primary);
    }

    .bootstrap-datetimepicker-widget .row {
        --bs-gutter-x: .75rem;
    }

    .bootstrap-datetimepicker-widget .collapse.in {
        display: block;
    }

    @media (max-width: 767.98px) {
        .bootstrap-datetimepicker-widget.dropdown-menu.timepicker-sbs {
            width: 20rem !important;
        }

        .bootstrap-datetimepicker-widget.timepicker-sbs .row {
            display: block;
        }
    }
</style>
