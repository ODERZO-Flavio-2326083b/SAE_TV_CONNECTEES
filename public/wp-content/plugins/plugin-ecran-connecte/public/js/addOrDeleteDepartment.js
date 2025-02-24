console.log("Le script est chargé");
console.log("Test codeHTML:", typeof codeHTML !== "undefined" ? codeHTML : "codeHTML est undefined");

let count = 0

function addButtonDep() {
    count = count + 1;

    let div = $('<div>', {
        id: 'row-' + count,
        class: 'row'
    }).appendTo('#deptContainer');

    if (codeHTML.department) {
        let newSelect = $(codeHTML.department).clone();
        newSelect.appendTo(div);
    } else {
        console.error("codeHTML.department n'est pas défini ou ne contient pas un <select>");
    }

    let button = $('<input>', {
        id: count,
        class: 'btn button_ecran',
        type: 'button',
        onclick: 'deleteRow(this.id)',
        value: 'Supprimer'
    }).appendTo(div);
}

function deleteRow(id) {
    document.getElementById(id).remove();
}