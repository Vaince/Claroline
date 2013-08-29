$(document).ready(function () {
    'use strict';

    var modalNewForm = null;

    var addLink = $('a.launch-modal');
    addLink.on('click', function (event) {
        event.preventDefault();

        $.get($(this).attr('href'))
            .always(function () {
                if (modalNewForm !== null) {
                    modalNewForm.remove();
                }
            })
            .done(function (data) {
                $('body').append(data);
                modalNewForm = $('#modal-content');
                modalNewForm.modal('show');
            })
        ;
    });
});