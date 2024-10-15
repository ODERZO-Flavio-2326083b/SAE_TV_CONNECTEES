/**
 * Build the block
 */
(function( blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/modify-code', {
        title: 'Modifier le code ADE',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Modifie le code ADE sélectionné";
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