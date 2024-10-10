/**
 * Build the block
 */
(function(blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/delete-account', {
        title: 'Supprime le compte de l\'utilisateur',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Supprime le compte de  l\'utilisateur";
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