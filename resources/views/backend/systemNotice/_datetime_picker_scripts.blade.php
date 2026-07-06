<script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
<script src="{{ asset('backend/assets/js/moment.js') }}"></script>
<script src="{{ asset('common/vendors/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
<script type="text/javascript">
    (function ($) {
        'use strict';

        var dateTimeFormat = 'YYYY-MM-DD HH:mm:ss';
        var dateTimePickerOptions = {
            format: dateTimeFormat,
            useStrict: true,
            collapse: false,
            sideBySide: true,
            showTodayButton: true,
            showClear: true,
            showClose: true,
            allowInputToggle: true,
            widgetParent: 'body',
            widgetPositioning: {
                horizontal: 'auto',
                vertical: 'bottom'
            },
            icons: {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-crosshairs',
                clear: 'fa fa-trash',
                close: 'fa fa-times'
            }
        };

        function parsedDate(value) {
            var date = moment(value, dateTimeFormat, true);

            return date.isValid() ? date : null;
        }

        function syncPickerLimits($startTime, $endTime) {
            var startDate = parsedDate($startTime.val());
            var endDate = parsedDate($endTime.val());

            if (startDate) {
                $endTime.data('DateTimePicker').minDate(startDate);
            }

            if (endDate) {
                $startTime.data('DateTimePicker').maxDate(endDate);
            }
        }

        $(document).ready(function () {
            var $startTime = $('#start_time');
            var $endTime = $('#end_time');

            $startTime.datetimepicker($.extend(true, {}, dateTimePickerOptions));
            $endTime.datetimepicker($.extend(true, {}, dateTimePickerOptions, {
                useCurrent: false
            }));

            syncPickerLimits($startTime, $endTime);

            $startTime.on('dp.change', function (event) {
                $endTime.data('DateTimePicker').minDate(event.date || false);
            });

            $endTime.on('dp.change', function (event) {
                $startTime.data('DateTimePicker').maxDate(event.date || false);
            });

            $('.admin-datetime-trigger').on('click', function () {
                $($(this).data('datetime-target')).datetimepicker('show');
            });

            $('.system-notice-form').cValidate({});
        });
    })(jQuery);
</script>
