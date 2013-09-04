$(document).ready(function () {
    'use strict';

    var modalNewForm = null;
-
    $('a.launch-modal').on('click', function (event) {
        event.preventDefault();

        $.get($(this).attr('href'))
            .always(function () {
                if (modalNewForm !== null) {
                    modalNewForm.remove();
                }
            })
            .done(function (data) {
                console.log(data);
                $('body').append(data);
                modalNewForm = $('#modal-content');
                modalNewForm.modal('show');
            })
        ;
    });
});