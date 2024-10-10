/**
 * Build the block
 */
(function( blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/modify-information', {
        title: 'Modifier l\'information',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Modifie l\'information sélectionné";
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