/**
 * Bloc permettant la création du formulaire d'ajout du département
 */
(function(blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/add-department', {
        title: 'Ajouter un département',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Ajoute des départments par un formulaire";
        },
        save: function() {
            return "test";
        },
    });
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.data,
));