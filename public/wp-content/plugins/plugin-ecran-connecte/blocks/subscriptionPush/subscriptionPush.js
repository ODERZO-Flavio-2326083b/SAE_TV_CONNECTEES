/**
 * Build the block
 */
( function(blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/subscription', {
        title: 'Bouton abonnement',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Affiche le bouton pour l'abonnement";
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