let rows, currentPage;

function init() {
    resetForm();
    addEventListeners();
    requestToralRows();
    requestData(1);
}


function resetForm() {
    document.getElementById("roleSelectForm").reset();
}


function filterData() {
    console.log("FilteringData over again");
    currentPage = 1;
    requestToralRows();
    requestData();
}

function addEventListeners() {
    document.getElementById("search-input").addEventListener("keyup", function (event) {
        // event.preventDefault();
        if (event.keyCode === 13) {
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

function deleteUser(id) {
    console.log("deleting...");

    if (confirm('Are you sure you want to delete the user with id \"' + id + '\" from the database?')) {
        $.ajax({
            url: 'ajax/users_delete.php',
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

function requestToralRows() {
    let role = document.getElementById('roleSelect').value;
    let str = document.getElementById('search-input').value;

    let search;
    if (str.length > 0) {
        search = str;
    }

    $.ajax({
        url: 'ajax/users_list.php',
        type: 'POST',
        data: {
            "role": role,
            "search": search
        },
        async: false, //blocks window close
        error: function (error) {
            alert("error" + JSON.stringify(error));
        },
        success: function (data) {
            // console.log("Rows1: " + data);

            // Update global values
            rows = Number(data);

        }
    });
}


function requestData(pn) {
    if (!pn) {
        pn = currentPage;
    }

    let rpp = document.getElementById('limitRecords').value;
    if (rpp === 'All') rpp = rows;

    let role = document.getElementById('roleSelect').value;
    let str = document.getElementById('search-input').value;

    let search;
    if (str.length > 0) {
        search = str;
    }


    if (rows !== 0) {
        let last = Math.ceil(rows / rpp);
        let usersTableArea = document.getElementById('usersTableArea');

        $.ajax({
            url: 'ajax/users_list.php',
            type: 'POST',
            dataType: 'json',
            data: {
                "pn": pn,
                "rpp": rpp,
                "last": last,
                "role": role,
                "search": search
            },
            error: function (error) {
                alert("error: " + JSON.stringify(error));
            },
            success: function (data) {
                // console.log("Data: " + JSON.stringify(data));

                if (data !== null) {
                    // Table
                    let tableRows = data.users.length;
                    let table = document.getElementById('usersTable');
                    // Empty tBody
                    //$("#usersTable > tbody").html("");
                    // removeRows(table);
                    createTable(table, tableRows, 6, data);
                } else {
                    console.log("PORBLEMS");
                }
            }
        });

        pagination(pn, last);
        currentPage = pn;

    } else {
        // console.log("Rows er 0!!!");

        let table = document.getElementById('usersTable');
        removeRows(table);
        $('#usersTable tbody').html('<tr><td colspan="8" class="noResults" align="center">No match for your search \"<b>' + search + '</b>\"</td></tr>');
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


//**********CREATE TABLE*********
function createTable(table, rows, cells, data) {

    if (!table) table = document.createElement('tbody');

    removeRows(table);
    let tableRef = table.getElementsByTagName('tbody')[0];

    for (let i = 0; i < rows; ++i) {
        let row = tableRef.insertRow(-1);

        for (let j = 0; j <= cells; ++j) {
            window["cell" + j] = row.insertCell(j);
        }

        cell0.innerHTML = data.users[i].user_id;
        cell1.innerHTML = data.users[i].user_name;
        cell2.innerHTML = data.users[i].first_name + " " + data.users[i].last_name;
        cell3.innerHTML = data.users[i].email;
        cell4.innerHTML = data.users[i].create_time;
        cell5.innerHTML = data.users[i].role;

        cell6.innerHTML = '' +
            '<button type="button" class="btn btn-success btn-circle"' +
            'onclick="location.href=\'view_user.php?user_id=' + data.users[i].user_id + '\'">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>' +
            '</button>' +

            '<!-- Delete user -->' +
            '<button type="button" class="btn btn-danger btn-circle" onclick="deleteUser(' + data.users[i].user_id + ')">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>' +
            '</button>';

        cell6.setAttribute("id", "text-center");
        cell6.setAttribute("class", "action");

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
