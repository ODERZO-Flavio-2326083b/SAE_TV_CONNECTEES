let countDep = 0
let depSelector = codeHTML.infoCode;

function codeAddRow(container) {
    countDep = countDep + 1;
    let div = $('<div>', {
        id: 'row-' + countDep,
        class: 'row'
    }).appendTo('#codeContainer' + container);

    let newSelect = $(depSelector);
    newSelect.appendTo(div);

    let button = $('<input>', {
        id: countDep,
        class: 'btn button_ecran',
        type: 'button',
        onclick: 'codeDeleteRow(this.id)',
        value: 'Supprimer'
    }).appendTo(div);
}

function codeDeleteRow(id) {
    $('#row-' + id).remove();
}