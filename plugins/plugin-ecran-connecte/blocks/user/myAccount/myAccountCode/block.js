/**
 * Build the block
 */
( function( blocks, element, data  ) {

    var el = element.createElement;

    blocks.registerBlockType( 'tvconnecteeamu/code-account', {
        title: 'Modifier codes étudiant connecté',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Modifie les codes de l\'étudiant connecté";
        },
        save: function() {
            return "test";
        },
    } );
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.data,
) );