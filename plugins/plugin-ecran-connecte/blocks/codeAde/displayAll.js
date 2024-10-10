/**
 * Build the block
 */
(function(blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/manage-code', {
        title: 'Affiche les codes ADE',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Affiche tous les codes ADE";
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