/**
 * Build the block
 */
(function(blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/choose-account', {
        title: 'Choisi les modifications de compte souhaité',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Choisi les modifications de compte souhaité";
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