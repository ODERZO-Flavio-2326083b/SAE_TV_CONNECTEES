/**
 * Build the block
 */
(function(blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType( 'tvconnecteeamu/modify-pass', {
        title: 'Modifier le mot de passe de l\'utilisateur',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Modifie le mot de passe de  l\'utilisateur";
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