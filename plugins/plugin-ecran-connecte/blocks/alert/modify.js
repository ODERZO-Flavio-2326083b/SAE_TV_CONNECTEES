/**
 * Build the block
 */
(function(blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/modify-alert', {
        title: 'Modifier l\'alerte',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Modifie l\'alerte sélectionnée";
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