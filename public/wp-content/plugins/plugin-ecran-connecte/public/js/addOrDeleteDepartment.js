let count = 0
let DepSelector = codeHTML.department;

function addButtonDep() {
    count = count + 1;

    let div = $('<div>', {
        id: count,
        class: 'row'
    }).append(DepSelector).appendTo('#info');

    let button = $('<input>', {
        id: count,
        class: 'btn button_ecran',
        type: 'button',
        onclick: 'deleteRow(this.id)',
        value: 'Supprimer'
    }).appendTo(div);
}