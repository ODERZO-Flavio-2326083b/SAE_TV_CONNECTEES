let countTag = 0
let tagSelector = codeHTML.tagg;

function addButtonTag() {
    countTag = countTag + 1;

    let div = $('<div>', {
        id: 'row-' + countTag,
        class: 'row'
    }).appendTo('#tagDiv');

    let newSelect = $(tagSelector);
    newSelect.appendTo(div)

    let button = $('<input>', {
        id: countTag,
        class: 'btn button_ecran',
        type: 'button',
        onclick: 'tagDeleteRow(this.id)',
        value: 'Supprimer'
    }).appendTo(div);
}

function tagDeleteRow(id) {
    $('#row-' + id).remove();
}