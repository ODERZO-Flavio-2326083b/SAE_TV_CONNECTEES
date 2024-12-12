/**
 * Build the block
 */
(function(blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/modify-css', {
        title: 'modifie le fichier css',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Modifie le fichier css pour modifier en cascade le style de la page";
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