(function ($, window, document) {
    'use strict';

    var FLASH_STATE_CLASSES = [
        'flash-success',
        'flash-error',
        'flash-warning',
        'flash-confirmation'
    ];
    var SUPPORTED_FORM_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    function getFlashBox() {
        return $('.flash-message');
    }

    function getFlashConfirm() {
        return getFlashBox().find('.flash-confirm');
    }

    function normalizeElementId(id) {
        return id ? String(id).replace(/^#/, '') : '';
    }

    function getElementByNormalizedId(id) {
        var normalizedId = normalizeElementId(id);

        return normalizedId ? document.getElementById(normalizedId) : null;
    }

    function removeElement(id) {
        var element = getElementByNormalizedId(id);

        if (element && element.parentNode) {
            element.parentNode.removeChild(element);
        }
    }

    function submitElement(id) {
        var element = getElementByNormalizedId(id);

        if (element && typeof element.submit === 'function') {
            element.submit();
        }
    }

    function resetFlashBox() {
        var $flashBox = getFlashBox();
        var $flashConfirm = getFlashConfirm();

        removeElement($flashConfirm.attr('data-form-auto-id'));

        $flashBox.removeClass('flash-message-active flash-message-window');
        $flashConfirm
            .attr('href', 'javascript:;')
            .removeAttr('data-form-id')
            .removeAttr('data-form-auto-id');
        $flashBox
            .find('.centralize-content')
            .removeClass(FLASH_STATE_CLASSES.join(' '))
            .find('p')
            .text('');
    }

    function createAutoForm(action, method, seed) {
        var upperMethod = String(method || '').toUpperCase();
        var token = $('meta[name="csrf-token"]').attr('content') || '';
        var formId = 'auto-form-generation-' + normalizeElementId(seed || upperMethod);

        $('#' + formId).remove();

        $('<form>', {
            id: formId,
            method: 'POST',
            action: action,
            css: {
                height: 0,
                width: 0,
                overflow: 'hidden'
            }
        })
            .append($('<input>', {type: 'hidden', name: '_token', value: token}))
            .append($('<input>', {type: 'hidden', name: '_method', value: upperMethod}))
            .prependTo('body');

        return formId;
    }

    function showConfirmation(message, formId, autoFormId, href) {
        var $flashConfirm = getFlashConfirm();

        resetFlashBox();

        if (href) {
            $flashConfirm.attr('href', href);
        }

        if (formId) {
            $flashConfirm.attr('data-form-id', formId);
        }

        if (autoFormId) {
            $flashConfirm.attr('data-form-auto-id', autoFormId);
        }

        getFlashBox()
            .find('.centralize-content')
            .addClass('flash-confirmation')
            .find('p')
            .text(message || '');
        getFlashBox().addClass('flash-message-active');
    }

    function handleFlashClose(event) {
        event.preventDefault();
        resetFlashBox();
    }

    function handleFlashConfirm(event) {
        var $confirm = $(this);
        var autoFormId = $confirm.attr('data-form-auto-id');
        var formId = $confirm.attr('data-form-id');

        if (autoFormId) {
            event.preventDefault();
            submitElement(autoFormId);
            resetFlashBox();
            return;
        }

        if (formId) {
            event.preventDefault();
            submitElement(formId);
            resetFlashBox();
            return;
        }

        resetFlashBox();
    }

    function handleConfirmation(event) {
        var $trigger = $(this);
        var formId = $trigger.attr('data-form-id');
        var method = String($trigger.attr('data-form-method') || '').toUpperCase();
        var href = $trigger.attr('href');
        var autoFormId = null;

        event.preventDefault();

        if (formId && SUPPORTED_FORM_METHODS.indexOf(method) !== -1) {
            autoFormId = createAutoForm(href, method, formId);
        }

        showConfirmation($trigger.attr('data-alert'), formId, autoFormId, formId ? null : href);
    }

    function initFlashMessages() {
        $(document)
            .on('click', '.flash-close', handleFlashClose)
            .on('click', '.flash-message-window', handleFlashClose)
            .on('click', '.flash-confirm', handleFlashConfirm)
            .on('click', '.confirmation', handleConfirmation);
    }

    function isEmptyLink(href) {
        var value = String(href || '').toLowerCase();

        return !value || value === '#' || value === 'javascript:;';
    }

    function initSidebar() {
        var $sidebarMenu = $('.sidebar-menu');

        if (!$sidebarMenu.length) {
            return;
        }

        $sidebarMenu
            .find('.active')
            .closest('.nav-treeview, .treeview-menu')
            .show()
            .closest('.treeview, .nav-item')
            .addClass('menu-open');

        $sidebarMenu.children('li').each(function () {
            var $item = $(this);
            var $link = $item.children('a').first();
            var href = $link.attr('href');
            var $dropdown = $item.children('ul').first();

            if (isEmptyLink(href) && !$item.find('li').length) {
                $item.remove();
                return;
            }

            if (!$dropdown.length) {
                return;
            }

            $item.addClass('treeview');
            $dropdown.addClass('nav-treeview');

            if (!$link.find('.nav-arrow').length) {
                $link.append('<i class="nav-arrow fa fa-angle-right"></i>');
            }

            if ($dropdown.find('li.active').length) {
                $item.addClass('menu-open');
            }
        });
    }

    function initICheck(selector, options) {
        var $inputs = $(selector);

        if ($inputs.length && $.fn.iCheck) {
            $inputs.iCheck(options);
        }
    }

    function initTooltips() {
        if (window.bootstrap && window.bootstrap.Tooltip) {
            document.querySelectorAll('[data-bs-toggle="tooltip"], [data-toggle="tooltip"]').forEach(function (element) {
                if (window.bootstrap.Tooltip.getOrCreateInstance) {
                    window.bootstrap.Tooltip.getOrCreateInstance(element);
                } else {
                    new window.bootstrap.Tooltip(element);
                }
            });
            return;
        }

        if ($.fn.tooltip) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    }

    function footerFixer() {
        return true;
    }

    window.flashBox = function (warnType, message) {
        getFlashBox()
            .find('.centralize-content')
            .addClass('flash-' + warnType)
            .find('p')
            .text(message || '');
        getFlashBox().addClass('flash-message-active flash-message-window');
    };

    $(window).on('resize', footerFixer);

    $(function () {
        footerFixer();
        initFlashMessages();
        initSidebar();
        initICheck('input[type="checkbox"].minimal, input[type="radio"].minimal', {
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        initICheck('input[type="checkbox"].flat-red, input[type="radio"].flat-red', {
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });
        initTooltips();
    });
})(jQuery, window, document);
