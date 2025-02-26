let countDep = 0
let depSelector = codeHTML.department;

function addButtonDep(container) {
    countDep = countDep + 1;

    console.log('bonjour' + container);
    let div = $('<div>', {
        id: 'row-' + countDep,
        class: 'row'
    }).appendTo('#deptContainer' + container);

    let newSelect = $(depSelector);
    newSelect.appendTo(div);

    let button = $('<input>', {
        id: countDep,
        class: 'btn button_ecran',
        type: 'button',
        onclick: 'depDeleteRow(this.id)',
        value: 'Supprimer'
    }).appendTo(div);
}

function depDeleteRow(id) {
    $('#row-' + id).remove();
}