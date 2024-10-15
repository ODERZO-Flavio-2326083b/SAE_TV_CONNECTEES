/**
 * Build the block
 */
(function( blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/management-user', {
        title: 'Affiche les utilisateurs demandés',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Affiche les utilisateurs demandés";
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