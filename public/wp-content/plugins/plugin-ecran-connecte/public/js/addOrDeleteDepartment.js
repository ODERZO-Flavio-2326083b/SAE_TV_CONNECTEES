let count = 0
let depSelector = codeHTML.department;

function addButtonDep() {
    count = count + 1;

    let div = $('<div>', {
        id: 'row-' + count,
        class: 'row'
    }).appendTo('#deptContainer');

    let newSelect = $(depSelector);
    newSelect.appendTo(div);

    let button = $('<input>', {
        id: count,
        class: 'btn button_ecran',
        type: 'button',
        onclick: 'deleteRow(this.id)',
        value: 'Supprimer'
    }).appendTo(div);
}

function deleteRow(id) {
    $('#row-' + id).remove();
}