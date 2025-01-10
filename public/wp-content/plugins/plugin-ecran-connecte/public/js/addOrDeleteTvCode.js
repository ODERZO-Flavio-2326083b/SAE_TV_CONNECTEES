let count = 0;
let tvSelector = codeHTML.tv;

/**
 * Crée un nouveau menu déroulant pour sélectionner un code ADE supplémentaire.
 */
function addButtonTv() {
    count = count + 1;

    let div = $('<div>', {
        id: count,
        class: 'row'
    }).append(tvSelector).appendTo('#registerTvForm');

    let button = $('<input>', {
        id: count,
        class: 'btn button_ecran',
        type: 'button',
        onclick: 'deleteRow(this.id)',
        value: 'Supprimer'
    }).appendTo(div);
}

/**
 * Delete the select
 *
 * @param id
 */
function deleteRow(id) {
    document.getElementById(id).remove();
}