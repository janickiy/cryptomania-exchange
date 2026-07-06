(function ($, window, document) {
    'use strict';

    var SUPPORTED_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    function getCsrfToken($link) {
        return $link.data('token') || $('meta[name="csrf-token"]').attr('content') || '';
    }

    function getHttpMethod($link) {
        return String($link.data('method') || '').toUpperCase();
    }

    function isSupportedMethod(method) {
        return SUPPORTED_METHODS.indexOf(method) !== -1;
    }

    function shouldContinue($link) {
        var confirmation = $link.data('confirm');

        return !confirmation || window.confirm(confirmation);
    }

    function createForm($link, method) {
        return $('<form>', {
            method: 'POST',
            action: $link.attr('href')
        })
            .append($('<input>', {
                type: 'hidden',
                name: '_token',
                value: getCsrfToken($link)
            }))
            .append($('<input>', {
                type: 'hidden',
                name: '_method',
                value: method
            }))
            .appendTo('body');
    }

    $(document).on('click', 'a[data-method]', function (event) {
        var $link = $(this);
        var method = getHttpMethod($link);

        if (!isSupportedMethod(method) || !shouldContinue($link)) {
            return;
        }

        event.preventDefault();
        createForm($link, method).trigger('submit');
    });
})(jQuery, window, document);
