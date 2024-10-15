/**
 * Build the block
 */
(function(blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/modify-user', {
        title: 'Modifier l\'utilisateur',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Modifie l\'utilisateur sélectionné";
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