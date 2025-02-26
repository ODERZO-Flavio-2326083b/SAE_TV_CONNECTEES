let countDep = 0
let depSelector = codeHTML.department;

function addButtonDep() {
    countDep = countDep + 1;

    let div = $('<div>', {
        id: 'row-' + countDep,
        class: 'row'
    }).appendTo('#deptContainer');

    let newSelect = $(depSelector);
    newSelect.appendTo(div);

    let button = $('<input>', {
        id: countDep,
        class: 'btn button_ecran',
        type: 'button',
        onclick: 'deleteRow(this.id)',
        value: 'Supprimer'
    }).appendTo(div);
}

function deleteRow(id) {
    $('#row-' + id).remove();
}