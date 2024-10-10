/**
 * Build the block
 */
(function(blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/manage-alert', {
        title: 'Affiche les alertes',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Affiche toutes les alertes de l\'utilisateur";
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