/**
 * Script pour créer le bloc de modification de département
 */
(function( blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/modify-department', {
        title: 'Modifier le département défini en GET',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Modifie le département défini en GET";
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