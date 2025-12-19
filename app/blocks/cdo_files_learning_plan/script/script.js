let oldTable = false;
let idDocumentDelete = '';

$(document).ready(() => {

    if (document.location.href.includes("curpill")) {
        var block = $('#anchor').offset().top;
        $('html').animate({scrollTop: block})
    }

    closeAllModal();
    showHideTable();

    var clonelearning = $('#learningProgram').clone();
    clonelearning.attr("name", "learning_program");
    clonelearning.attr("id", "learning_program_files");
    clonelearning.addClass("d-none");

    $('#form-learning').append(clonelearning);

    $('#learningProgram').bind('change', () => {
        showHideTable();
        changeClone();
    })

    $('.delete-item').bind('click', function () {
        showConfirmDelete(this)
    })

    $('.delete-item-disc').bind('click', function () {
        showConfirmDeleteDisc(this)
    })

    $('.delete-item-link').bind('click', function () {
        showConfirmDeleteLink(this)
    })
})

const showHideTable = () => {

    const selected = $('#learningProgram option:selected').data('doc_number');

    if (oldTable) {
        $('#' + oldTable).toggleClass('d-none');
        $('.discipline_' + oldTable).toggleClass('d-none');
        $('#weblinks_' + oldTable).toggleClass('d-none');
        console.log('hidden', oldTable);
    }
    console.log('show', selected);
    $('#' + selected).toggleClass('d-none');
    $('.discipline_' + selected).toggleClass('d-none');
    $('#weblinks_' + selected).toggleClass('d-none');

    oldTable = selected;
}

const changeClone = () => {

    const selected = $('#learningProgram').val();
    $('#learning_program_files').val(selected);

}

$('#pills-tab a').on('click', function (e) {
    e.preventDefault();
    $('.tab-pane').removeClass("show");
    $('.tab-pane').removeClass("active");
    $(this).tab('show');
})


const showConfirmDelete = (_this) => {
    $('#new-modal').modal('show');
    openConfirm('new-modal', 'Удаление записи', 'После удаления востановление невозможно', 'Отмена', 'Удалить', 'btn-primary', 'btn-danger')
    idDocumentDelete = $(_this).attr('id');
    $('#new-modal-confirm').bind('click', () => {
        deleteItem(_this);
    })
}

const showConfirmDeleteDisc = (_this) => {
    var link_file = $(_this).parents('tr').find('[data-type="link_file"]').html();
    if (link_file == '')
        return;

    $('#new-modal').modal('show');
    openConfirm('new-modal', 'Удаление записи', 'После удаления востановление невозможно', 'Отмена', 'Удалить', 'btn-primary', 'btn-danger')
    $('#new-modal-confirm').bind('click', () => {
        deleteItemDisc(_this);
    })
}

const showConfirmDeleteLink = (_this) => {
    $('#new-modal').modal('show');
    openConfirm('new-modal', 'Удаление записи', 'После удаления востановление невозможно', 'Отмена', 'Удалить', 'btn-primary', 'btn-danger')
    idDocumentDelete = $(_this).attr('id');
    $('#new-modal-confirm').bind('click', () => {
        deleteItemLink(_this);
    })
}

const deleteItem = (param) => {
    $('#new-modal').modal('hide');

    const selected = $('#learningProgram option:selected').data('doc_number');
    $.ajax({
        url: "/blocks/files_learning_plan/update.php",
        method: "GET",
        data: {
            mode: 'delete_file_program',
            doc_id: selected,
            file_id: idDocumentDelete,
        },
        success: (answer) => {
            answer_$ = JSON.parse(answer);

            if (typeof answer_$.success !== "undefined") {
                $('#main-view-info-title').text('Удаление');
                $('#main-view-info-text').text('Запись успешно удалена');
                $(param).parents('tr').first().remove();
                $('#new-modal-info').modal('show');
            } else if (typeof answer_$.error !== "undefined") {
                $('#main-view-info-title').text('Удаление');
                $('#main-view-info-text').text('Ошибка - ' + answer_$.error);
                $('#new-modal-info').modal('show');
            } else {

            }
            console.log(answer);

        },
        error: (error) => {
            console.error(error);
        }
    });
}

const deleteItemDisc = (_this) => {
    $('#new-modal').modal('hide');

    let guidfile = $(_this).data('guidfile');
    $.ajax({
        url: "/blocks/files_learning_plan/update.php",
        method: "GET",
        data: {
            mode: 'delete_file',
            doc_id: $('#learningProgram option:selected').data('doc_number'),
            discipline_id: $(_this).attr('id'),
            guidfile: guidfile,
        },
        success: (answer) => {
            console.log(answer);
            var answer_$ = JSON.parse(answer);

            if (typeof answer_$.error !== "undefined") {
                showInfo('Удаление', 'Ошибка - ' + answer_$.error);
            } else {
                showInfo('Удаление', 'Запись успешно удалена');
                $(_this).parents('td').find(`[data-guidfile="${guidfile}"]`).remove();
                $(_this).remove();
            }


        },
        error: (error) => {
            console.error(error);
        }
    });
}

const deleteItemLink = (param) => {
    $('#new-modal').modal('hide');

    const selected = $('#learningProgram option:selected').data('doc_number');
    $.ajax({
        url: "/blocks/files_learning_plan/update.php",
        data: {
            mode: 'delete_link',
            doc_id: selected,
            link_guid: idDocumentDelete
        },
        method: "GET",
        success: (answer) => {
            answer_$ = JSON.parse(answer);

            if (typeof answer_$.error !== "undefined") {
                $('#main-view-info-title').text('Удаление');
                $('#main-view-info-text').text('Ошибка - ' + answer_$.error);
                $('#new-modal-info').modal('show');
            } else {
                $('#main-view-info-title').text('Удаление');
                $('#main-view-info-text').text('Запись успешно удалена');
                $(param).parents('tr').remove();
                $('#new-modal-info').modal('show');
            }
            console.log(answer);

        },
        error: (error) => {
            console.error(error);
        }
    });
}

const closeAllModal = () => {
    $('body').bind('click', () => {
        $('#new-modal').modal('hide');
        $('#new-modal-info').modal('hide');
    })
};

const openConfirm = (
    selector = 'new-modal',
    title = '',
    body = '',
    close = '',
    confirm = '',
    closeCLass = '',
    confirmClass = ''
) => {

    _selectorTitle = '#' + selector + '-title';
    _selectorBody = '#' + selector + '-body';
    _selectorClose = '#' + selector + '-close';
    _selectorConfirm = '#' + selector + '-confirm';

    $(_selectorTitle).text(title);
    $(_selectorBody).text(body);
    $(_selectorClose).text(close);
    $(_selectorConfirm).text(confirm);

    $(_selectorClose).addClass(closeCLass ? closeCLass : 'btn-danger');
    $(_selectorConfirm).addClass(confirmClass ? confirmClass : 'btn-primary');

};

const showInfo = (title, text) => {
    $('#main-view-info-title').text(title);
    $('#main-view-info-text').text(text);
    $('#new-modal-info').modal('show');
}

$(".link_add_file").click(function (event) {

    var parent = $(this).parents('tr');
    $('#modal_discipline').val(parent.find('[data-type="discipline"]').html());
    $('#modal_discipline_id').val($(this).data('discipline_id'));
    $('#modal_section').val($(this).data('section'));
    $('#modal_section_name').val($(this).data('section_name'));
    $('#modal_program').val($('#learningProgram option:selected').html());
    $('#modal_mode').val('new_file');

});

$(".file-save").click(function (event) {

    let modal_form = $("#modal_add_file"),
        form_data = new FormData(),
        doc_number = $('#learningProgram option:selected').data('doc_number');

    form_data.append("mode", $('#modal_mode').val());
    form_data.append("doc_id", doc_number);
    form_data.append("discipline_id", $('#modal_discipline_id').val());
    form_data.append("section", $('#modal_section').val());

    let files = $('#modal_file')[0].files;
    for (var i = 0; i < files.length; i++) {
        form_data.append(`imagefile[${i}]`, files[i]);
    }

    $.ajax({
        url: "/blocks/files_learning_plan/update.php",
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        method: "POST",
        success: function (answer) {
            console.log(answer);
            answer = JSON.parse(answer);
            if (typeof answer.error === 'undefined') {
                modal_form.modal('hide');
                //location.reload();
                // window.location.href = '/my?curpill=3';
                window.location = window.location.pathname + '?select=' + doc_number + '&curpill=3';
            } else {
                showInfo("Произошла ошибка!", answer.error);
            }
        }
    });

});

$(".link_edit_link").click(function (event) {

    var parent = $(this).parents('tr');
    $('#modal_link_name').val(parent.find('[data-type="link_name"]').html().trim());
    $('#modal_link').val(parent.find('[data-type="link_URL"]').html().trim());

    var link_guid = parent.find('[data-type="link_guid"]').html().trim();
    $('#modal_link_mode').val('update_link');
    $('#modal_link_guid').val(link_guid);

});

$(".link_add_link").click(function (event) {
    $('#modal_add_link').find('input').val('');
    $('#modal_link_mode').val('new_link');
});

$(".link-save").click(function (event) {

    let modal_form = $("#modal_add_link"),
        form_data = {},
        doc_number = $('#learningProgram option:selected').data('doc_number');

    form_data.mode = $('#modal_link_mode').val();
    form_data.doc_id = doc_number;
    form_data.link_guid = $('#modal_link_guid').val();
    form_data.link_name = $('#modal_link_name').val();
    form_data.link_URL = $('#modal_link').val();

    $.ajax({
        url: "/blocks/files_learning_plan/update.php",
        cache: false,
        data: form_data,
        method: "GET",
        success: function (answer) {
            console.log(answer);
            if (typeof answer.error === 'undefined') {
                modal_form.modal('hide');
                window.location = window.location.pathname + '?select=' + doc_number;
            } else {
                alert("Произошла ошибка!");
            }
        }
    });

});

$('#secretary').change(function () {
    var id = $(this).val();
    if (parseInt(id) !== 0)
        window.location = window.location.pathname + '?secretary=' + id;
    else
        window.location = window.location.pathname;
});

$('.edit-notes-disc').click(function () {

    let parent = $(this).parents('tr');
    $('.modal_discipline').val(parent.find('[data-type="discipline"]').html());
    $('.modal_discipline_id').val($(this).data('discipline_id'));
    $('.modal_section').val($(this).data('section'));
    $('.modal_section_name').val($(this).data('section_name'));
    $('.modal_program').val($('#learningProgram option:selected').html());
    $('.modal_mode').val('update_notes');
    $('.modal_notes').text(parent.find('[data-type="notes"]').html().trim());

})

$(".notes-save").click(function (event) {

    let modal_form = $("#form_edit_notes"),
        form_data = new FormData(modal_form[0]),
        discipline_id = modal_form.find('.modal_discipline_id').val(),
        notes = modal_form.find('.modal_notes').val();

    form_data.append("doc_id", $('#learningProgram option:selected').data('doc_number'));

    $.ajax({
        url: "/blocks/files_learning_plan/update.php",
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        method: "POST",
        success: function (answer) {
            answer = JSON.parse(answer);
            if (typeof answer.error === 'undefined') {
                $('#modal_edit_notes').modal('hide');
                $(`.item-notes[data-discipline_id="${discipline_id}"]`).text(notes);
            } else {
                showInfo("Произошла ошибка!", answer.error);
            }
        }
    });

});