let counte = 0;
let alertSelector = codeHTML.alert;

/**
 * Create a new select to add a new group for the alert
 */
function addButtonAlert() {
    counte = counte + 1;

    let div = $('<div>', {
        id: counte,
        class: 'row'
    }).append(alertSelector).appendTo('#alert');

    let button = $('<input>', {
        id: counte,
        class: 'btn button_ecran',
        type: 'button',
        onclick: 'deleteRow(this.id)',
        value: 'Supprimer'
    }).appendTo(div);
}

/**
 * Supprime un élément par son ID.
 * Cette fonction est utilisée par le bouton 'Supprimer'.
 *
 * @param id id de l'élément.
 */
function deleteRowAlert(id) {
    document.getElementById(id).remove();
}