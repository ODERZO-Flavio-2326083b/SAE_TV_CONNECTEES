/**
 * Build the block
 */
(function(blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/help-map', {
        title: 'Affiche la carte d\'aide',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Affiche la carte des points intéressants a proximité pour les étudiants";
        },
        save: function() {
            return "A l'aide";
        },
    });
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.data,
));