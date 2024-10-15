/**
 * Valid all checkbox
 *
 * @param source
 * @param name
 */
function toggle(source, name)
{
    checkboxes = document.getElementsByName('checkboxStatus' + name + '[]');
    for (var i = 0, n = checkboxes.length; i < n; i++) {
        checkboxes[i].checked = source.checked;
    }
}