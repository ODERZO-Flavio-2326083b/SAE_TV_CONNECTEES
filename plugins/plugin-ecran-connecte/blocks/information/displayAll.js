/**
 * Build the block
 */
(function( blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/manage-information', {
        title: 'Affiche les informations',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Affiche toutes les informations de l\'utilisateur";
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