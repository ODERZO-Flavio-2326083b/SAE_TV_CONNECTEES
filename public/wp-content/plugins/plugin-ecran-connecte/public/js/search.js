/**
 * Search an element in the table
 *
 * @param idTable
 */
function search(idTable)
{
    let input, filter, table, tr, td, allTd, j, i, txtValue, hide;
    input = document.getElementById("key"+idTable);
    filter = input.value.toUpperCase();
    table = document.getElementById("table"+idTable);
    tr = table.getElementsByTagName("tr");

    console.log(tr);

    for (i = 1; i < tr.length; ++i) {
        hide = 0;
        allTd = tr[i].getElementsByTagName("td");
        for(j = 0; j < allTd.length; ++j) {
            td = allTd[j];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    ++hide;
                }
            }
        }
        if(hide > 0) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
        hide = 0;
    }
}