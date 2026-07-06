(function ($) {
    'use strict';

    function closestGroup($item) {
        return $item.closest('.role-permission-group, .route-group');
    }

    function closestSubgroup($item) {
        return $item.closest('.role-subgroup, .route-subgroup');
    }

    function setInputState($input, checked) {
        $input.prop('checked', checked);
    }

    function setVisualState($input, checked) {
        $input.parent().toggleClass('checked', checked);
        $input.closest('.role-check').toggleClass('checked', checked);
    }

    function syncSubgroup($subgroup) {
        var $items = $subgroup.find('.route-item');
        var checked = $items.length > 0 && $items.length === $items.filter(':checked').length;
        var $subModule = $subgroup.find('.sub-module').first();

        setInputState($subModule, checked);
        setVisualState($subModule, checked);
    }

    function syncGroup($group) {
        var $items = $group.find('.route-item');
        var checked = $items.length > 0 && $items.length === $items.filter(':checked').length;
        var $module = $group.find('.module').first();

        setInputState($module, checked);
        setVisualState($module, checked);
        $group.find('.role-subgroup, .route-subgroup').each(function () {
            syncSubgroup($(this));
        });
    }

    $('.role-permission-group, .route-group').each(function () {
        syncGroup($(this));
    });

    $(document).on('ifChanged change', 'input.route-item', function () {
        var $item = $(this);

        syncSubgroup(closestSubgroup($item));
        syncGroup(closestGroup($item));
    });
})(jQuery);
