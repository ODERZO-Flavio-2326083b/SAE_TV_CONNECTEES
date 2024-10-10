/**
 * Build the block
 */
(function( blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/schedules', {
        title: 'Emploi du temps promo',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Affiche l\'emploi du temps d'une promo";
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