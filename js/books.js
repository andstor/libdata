let rows, currentPage;

function init() {
    resetForm();
    addEventListener();
    requestToralRows();
    requestData(1);
}


function resetForm() {
    document.getElementById("genreSelectForm").reset();
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
    let genre = document.getElementById('genreSelect').value;
    let library = document.getElementById('librarySelect').value;
    let str = document.getElementById('search-input').value;

    let search;
    if (str.length > 0) {
        search = str;
    }

    $.ajax({
        url: 'ajax/get_books.php',
        type: 'POST',
        data: {
            "genre": genre,
            "library": library,
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


function requestData(pn) {
    if (!pn) {
        pn = currentPage;
    }

    let rpp = document.getElementById('limitRecords').value;
    if (rpp === 'All') rpp = rows;

    let genre = document.getElementById('genreSelect').value;
    let library = document.getElementById('librarySelect').value;
    let str = document.getElementById('search-input').value;

    let search;
    if (str.length > 0) {
        search = str;
    }


    if (rows !== 0) {
        let last = Math.ceil(rows / rpp);

        $.ajax({
            url: 'ajax/get_books.php',
            type: 'POST',
            dataType: 'json',
            data: {
                "pn": pn,
                "rpp": rpp,
                "last": last,
                "genre": genre,
                "library": library,
                "search": search
            },
            error: function (error) {
                alert("error: " + JSON.stringify(error));
            },
            success: function (data) {
                // alert("Data: " + JSON.stringify(data));

                if (data !== null) {
                    // Table
                    createGrid(data);
                } else {
                    console.log("PORBLEMS");
                }
            }
        });

        pagination(pn, last);
        currentPage = pn;

    } else {
        console.log("Rows er 0!!!");

        let gridContainer = document.getElementById('gridContainer');
        gridContainer.innerHTML = "";
        let searchRespond = '';
        if (search) searchRespond = '\"<b>' + search + '</b>\"'
        $('#gridContainer').html('<tr><td colspan="8" class="noResults" align="center">No match for your search ' + searchRespond + '</td></tr>');
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

function replaceMissingImages(imgElement) {
    imgElement.onerror = "";
    imgElement.src = 'images/cover-placeholder.jpg';
}

function createGrid(data) {
    let gridContainer = document.getElementById('gridContainer');
    let res = "";


    for (let book in data.books) {

        let buttons = "";
        if (data.books[book].num_books >= 1) {
            buttons += '                                <div class="btn-group">' +
                '                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.href=\'view_book_details.php?isbn=' + data.books[book].isbn + '\'">View</button>' +
                '                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.href=\'loan.php?isbn=' + data.books[book].isbn + '\'">Loan</button>' +
                '                                </div>'
        } else {
            buttons += '                                <div class="btn-group">' +
                '                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.href=\'view_book_details.php?isbn=' + data.books[book].isbn + '\'">View</button>' +
                '                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.href=\'waiting_list.php?isbn=' + data.books[book].isbn + '\'">Waiting list</button>' +
                '                                </div>'
        }

        res += '<div class="col-md-3">' +
            '                    <div class="card mb-3 box-shadow">' +
            '                        <img id="book_cover' + book + '" class="card-img-top"' +
            '                             src="uploads/cover_pictures/' + data.books[book].isbn + '.jpg"' +
            '                             alt="Card image cap" onerror="replaceMissingImages(this);"' +
            '                             onclick="location.href=\'view_book_details.php?isbn=' + data.books[book].isbn + '\'" style="cursor: pointer">' +
            '                        <div class="card-body">' +
            '                                <small class="text-muted">' + data.books[book].isbn + '</small>' +
            '                            <p class="card-text">' + data.books[book].title + '</p>' +
            '                            <small class="text-muted">' + data.books[book].publisher + '</small>' +
            '                            <div class="d-flex justify-content-between align-items-center">' +
            buttons +
            '                                <small class="text-muted">Available: ' + data.books[book].num_books + '</small>' +
            '                            </div>' +
            '                        </div>' +
            '                    </div>' +
            '                </div>';

    }

    gridContainer.innerHTML = "";
    gridContainer.insertAdjacentHTML('beforeend', res);
}


//Runs when the document is loaded. Initialises the script.
document.addEventListener('DOMContentLoaded', function () {
    //"use strict";
    init();
});