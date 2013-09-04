$(document).ready(function () {
    'use strict';

    $('a.launch-inline').on('click', function (event) {
        event.preventDefault();

        $.get($(this).attr('href'))
            .always(function () {
            })
            .done(function (data) {
                $('.control-inline').hide();
                $('.container-inline').append(data);
            })
        ;
    });
});