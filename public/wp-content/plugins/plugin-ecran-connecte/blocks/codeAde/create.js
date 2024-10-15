/**
 * Build the block
 */
(function(blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/add-code', {
        title: 'Ajouter des codes ADE',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Ajoute des code ADE via un formulaire";
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