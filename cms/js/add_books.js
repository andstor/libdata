let numAuthors = 0;
let inputTitle = "";
let existingBook = false;

function init() {
    addEventListeners();
    showOrHideForm(null, false);
}


function addAuthorField() {
    let authorsContainer = document.getElementById('authors-container');
    let $authorsContainer = $(authorsContainer);

    let deleteVar = '\'authorInputGroup' + numAuthors + "\'";
    // language=HTML
    let authorInputsTemplate = ' <div class="form-inline" id="authorInputGroup' + numAuthors + '">\n' +
        '                                <div class="form-group mb-2">\n' +
        '                                        <input type="text" id="inputAuthor" class="form-control" name="newAuthorFirstName[]"\n' +
        '                                               placeholder="Author\'s first name">\n' +
        '                                    </div>\n' +
        '                                <div class="form-group mx-sm-3 mb-2">\n' +
        '                                        <input type="text" id="inputAuthorLastName" class="form-control" name="newAuthorLastName[]"\n' +
        '                                               placeholder="Author\'s last name">\n' +
        '                            </div>\n' +
        '                                  <button type="button" onclick="deleteAuthorField(' + deleteVar + ');" class="btn btn-outline-danger mb-2">X</button>\n\n' +
        '                         </div>\n';

    $authorsContainer.append(authorInputsTemplate);
    numAuthors++;
}

function addEventListeners() {
    document.getElementById('inputISBN').addEventListener("input", function (event) {
        let isbn = this.value;
        if (isbn.length === 13 && isbn.match(/^[0-9]{13}$/)) {
            filterISBN(isbn);
        }
    });
}


function showOrHideForm(show = false) {
    let additionalFields = $('.additionalFields');

    if (show) {
        additionalFields.show();
    } else {
        additionalFields.hide();
    }
}

function filterISBN(isbn) {
    let titleInput = $('#inputTitle');
    if (!existingBook) {
        inputTitle = titleInput.val();
    }
    $.ajax({
        url: 'ajax/get_books.php',
        type: 'POST',
        dataType: 'json',
        data: {
            "isbn": isbn
        },
        error: function (error) {
            alert("error: " + JSON.stringify(error));
        },
        success: function (data) {
            if (!Array.isArray(data) || !data.length) {
                showOrHideForm(true);
                titleInput.prop('readonly', false);
                titleInput.val(inputTitle);
                existingBook = false;
            } else {
                inputTitle = titleInput.val();

                showOrHideForm(false);
                titleInput.val(data[0].title);
                titleInput.prop('readonly', true);
                existingBook = true;
            }
        }
    });

}


function deleteAuthorField(elemId) {
    $('#' + elemId).remove();

}

function readURL(input) {

    var url = input.value;

    var reader = new FileReader();

    reader.onload = function (e) {
        $('#bookImg > img').attr('src', e.target.result);
    };
    reader.readAsDataURL(input.files[0]);

    $('#fileName').val(input.files[0].name);

}


$("#upload").change(function () {
    readURL(this);
});


//Runs when the document is loaded. Initialises the script.
document.addEventListener('DOMContentLoaded', function () {
    //"use strict";
    init();
});