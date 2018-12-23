let rows, currentPage;

function init() {
    addEventListener();
    requestToralRows();
    requestData(1);
}


function filterData() {
    console.log("FilteringData over again");
    currentPage = 1;
    requestToralRows();
    requestData();
}

function addEventListener() {
    document.getElementById("search-input").addEventListener("keyup", function (event) {
        // event.preventDefault();
        if (event.keyCode == 13) {
            document.getElementById("btn-search").click();
        }

        if (this.value == "") {
            document.getElementById("btn-search").click();
        }
    });

    document.getElementById('limitRecords').addEventListener("change", e => {
        filterData();
    });
}

function requestToralRows() {
    let str = document.getElementById('search-input').value;

    let search;
    if (str.length > 0) {
        search = str;
    }

    $.ajax({
        url: 'ajax/library_branches_list.php',
        type: 'POST',
        data: {
            "search": search
        },
        async: false, //blocks window close
        error: function (error) {
            alert("error" + JSON.stringify(error));
        },
        success: function (data) {
            console.log("Rows1: " + data);

            // Update global values
            rows = Number(data);

        }
    });
}

function deleteLibBranch(id) {
    console.log("deleting...");

    if (confirm('Are you sure you want to delete the library branch with id \"' + id + '\" from the database?')) {
        $.ajax({
            url: 'ajax/library_branches_delete.php',
            type: 'POST',
            data: {
                "delete_id": id,
            },
            async: false, //blocks window close
            error: function (error) {
                alert("error" + JSON.stringify(error));
            },
            success: function (data) {
                console.log("done: " + data);

            }
        });
    }
    filterData();
}


function requestData(pn) {
    if (!pn) {
        pn = currentPage;
    }

    let rpp = document.getElementById('limitRecords').value;
    if (rpp === 'All') rpp = rows;

    let str = document.getElementById('search-input').value;

    let search;
    if (str.length > 0) {
        search = str;
    }


    if (rows !== 0) {
        let last = Math.ceil(rows / rpp);
        let booksTableArea = document.getElementById('booksTableArea');

        $.ajax({
            url: 'ajax/library_branches_list.php',
            type: 'POST',
            dataType: 'json',
            data: {
                "pn": pn,
                "rpp": rpp,
                "last": last,
                "search": search
            },
            error: function (error) {
                alert("error: " + JSON.stringify(error));
            },
            success: function (data) {
                // alert("Data: " + JSON.stringify(data));

                if (data !== null) {
                    // Table
                    let tableRows = data.rows;
                    let table = document.getElementById('booksTable');
                    // Empty tBody
                    //$("#usersTable > tbody").html("");
                    createTable(table, tableRows, data);
                } else {
                    console.log("PORBLEMS");
                }
            }
        });

        pagination(pn, last);
        currentPage = pn;

    } else {
        console.log("Rows er 0!!!");

        let table = document.getElementById('booksTable');
        removeRows(table);
        $('#booksTable tbody').html('<tr><td colspan="8" class="noResults" align="center">No match for your search \"<b>' + search + '</b>\"</td></tr>');
    }

    //  pagination(0, 0);

}


function pagination(pn, last) {

    const pagination_controls = document.getElementById("pagination_controls");

    const maxLi = 9; // Must be odd number
    const numLi = last;
    const offset = Math.floor((maxLi - 4) / 2);

    let paginationCtrls = "";


    if (pn === 1) {
        paginationCtrls += '<li class="page-item disabled"><a class="page-link" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
    } else {
        paginationCtrls += '<li class="page-item"><a class="page-link" aria-label="Previous" onclick="requestData(' + (pn - 1) + ');"><span aria-hidden="true">&laquo;</span></a></li>';
    }

    if (last < maxLi) {
        for (let i = 1; i < last + 1; i++) {
            if (i === pn) {
                paginationCtrls += '<li class="page-item active"><a class="page-link"><span>' + i + '<span class="sr-only">(current)</span></span></a></li>';
            } else {
                paginationCtrls += '<li class="page-item"><a class="page-link" onclick="requestData(' + i + ');">' + i + '</a></li>';
            }
        }

    } else { // Include dotdotdot
        if (pn > 1 + Math.floor(maxLi / 2) && pn < last - Math.floor(maxLi / 2)) {
            paginationCtrls += '<li class="page-item"><a class="page-link"><span>1<span class="sr-only">(current)</span></span></a></li>';
            paginationCtrls += '<li class="page-item"><a class="page-link">...</a></li>';
            for (let j = pn - offset; j < pn + offset + 1; j++) {
                if (j === pn) {
                    paginationCtrls += '<li class="page-item active"><a class="page-link"><span>' + j + '<span class="sr-only">(current)</span></span></a></li>';
                } else {
                    paginationCtrls += '<li class="page-item"><a class="page-link" onclick="requestData(' + j + ');">' + j + '</a></li>';
                }
            }
            paginationCtrls += '<li class="page-item"><a class="page-link">...</a></li>';
            paginationCtrls += '<li class="page-item"><a class="page-link" onclick="requestData(' + last + ');">' + last + '</a></li>';


        } else if (pn < last - Math.floor(maxLi / 2)) {
            for (let k = 1; k < (maxLi + 1) - 2; k++) {
                if (k === pn) {
                    paginationCtrls += '<li class="page-item active"><a class="page-link"><span>' + k + '<span class="sr-only">(current)</span></span></a></li>';
                } else {
                    paginationCtrls += '<li class="page-item"><a class="page-link" onclick="requestData(' + k + ');">' + k + '</a></li>';
                }
            }
            paginationCtrls += '<li class="page-item"><a class="page-link">...</a></li>';
            paginationCtrls += '<li class="page-item"><a class="page-link" onclick="requestData(' + last + ');">' + last + '</a></li>';

        } else {
            paginationCtrls += '<li class="page-item"><a class="page-link" onclick="requestData(' + 1 + ');">' + 1 + '</a></li>';
            paginationCtrls += '<li class="page-item"><a class="page-link">...</a></li>';
            for (let l = last - (maxLi - 3); l <= last; l++) {
                if (l === pn) {
                    paginationCtrls += '<li class="page-item active"><a class="page-link"><span>' + l + '<span class="sr-only">(current)</span></span></a></li>';
                } else {
                    paginationCtrls += '<li class="page-item"><a class="page-link" onclick="requestData(' + l + ');">' + l + '</a></li>';
                }
            }
        }

    }

    if (pn === last) {
        paginationCtrls += '<li class="page-item  disabled"><a class="page-link" aria-label="Next"><span aria-hidden="true">&raquo;</span></li>';
    } else {
        paginationCtrls += '<li class="page-item"><a class="page-link" aria-label="Next" onclick="requestData(' + (pn + 1) + ');"><span aria-hidden="true">&raquo;</span></li>';
    }

    pagination_controls.innerHTML = paginationCtrls;

}

function removeRows(table) {
    //table.$('tbody').remove(); Delete all tBodies exept one.
    let tableRows = table.rows.length;
    if (tableRows > 1) {
        for (let i = 1; i < tableRows; i++) {
            table.deleteRow(1);
        }
    }

}


function flatternObj(obj) {
    let arr = [];
    for (let property in obj) {
        if (obj.hasOwnProperty(property)) {
            if (typeof obj[property] == "object") {
                arr.push(flatternObj(obj[property]).join(' '));
            } else {
                arr.push(obj[property]);
            }
        }
    }
    return arr;
}


//**********CREATE TABLE*********
function createTable(table, rows, data) {
    let numCells = 6;
    if (!table) table = document.createElement('tbody');
    removeRows(table);
    let tableRef = table.getElementsByTagName('tbody')[0];

    // bd.isbn, bd.title, CONCAT(u.first_name, ' ', u.last_name) AS full_name, bl.loan_date, r.return_date
    let i = 0;
    for (let branch in data.library_branches) {
        let row = tableRef.insertRow(-1);

        for (let j = 0; j <= numCells; ++j) {
            window["cell" + j] = row.insertCell(j);
        }
        cell0.innerHTML = data.library_branches[branch].id;
        cell1.innerHTML = data.library_branches[branch].name;
        cell2.innerHTML = data.library_branches[branch].city;
        cell3.innerHTML = data.library_branches[branch].region;
        cell4.innerHTML = data.library_branches[branch].country;

        let actions = '';

        actions +=
            '<button type="button" class="btn btn-danger btn-circle" onclick="deleteLibBranch(' + data.library_branches[branch].id + ')">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>' +
            '</button>';

        cell5.innerHTML = actions;

        cell5.setAttribute("id", "text-center");
        cell5.setAttribute("class", "action");

        i++;
    }

}


//********** STATUS ************
function statusLabel(data) {
    let status = "";

    switch (data /*[i].status_name*/) {
        case 'Active':
            status = '<span class="label label-success">Active</span>';
            break;

        case 'Unconfirmed':
            status = '<span class="label label-warning">Unconfirmed</span>';
            break;

        case 'Banned':
            status = '<span class="label label-danger">Disabeled</span>';
            break;
        default:
            status = '<span class="label label-default">Undefined</span>';
            break;
    }
    return status;
}


function uniq(a) {
    return Array.from(new Set(a));
}


//Runs when the document is loaded. Initialises the script.
document.addEventListener('DOMContentLoaded', function () {
    //"use strict";
    init();
});