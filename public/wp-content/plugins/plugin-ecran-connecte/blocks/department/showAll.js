/**
 * Script pour créer le bloc d'affichage de tous les départements
 */
(function(blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/showall-department', {
        title: 'Afficher les départements',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Affiche les départments par un tableau";
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