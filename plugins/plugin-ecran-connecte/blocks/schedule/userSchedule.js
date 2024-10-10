/**
 * Build the block
 */
(function( blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/schedule', {
        title: 'Emploi du temps perso',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Affiche l\'emploi du temps de la personne connectée";
        },
        save: function() {
            return "Yo";
        },
    });
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.data,
));