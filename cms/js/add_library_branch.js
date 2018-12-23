let selectLock = false;


function init() {
    addEventListeners();
}



function addEventListeners() {

    $('#selectCountry').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        if (selectLock === false) {
            selectLock = true;
            filterRegion();
            selectLock = false;
        }
    });

    $('#selectRegion').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        if (selectLock === false) {
            selectLock = true;
            filterCity();
            selectLock = false;
        }

    });

}


function setSelectOptions(select, options, key) {
    select.children('option:not(:first)').remove();
    for (let i = 0; i < options.length; i++) {
        select
            .append($("<option></option>")
                .attr("value",options[i][key])
                .text(options[i][key]));
    }
    $('.selectpicker').selectpicker('refresh');
}

function filterRegion() {
    let country = $('#selectCountry').val();
    let region = $('#selectRegion');

    $.ajax({
        url: 'ajax/get_address_components.php',
        type: 'POST',
        dataType: 'json',
        data: {
            "country": country
        },
        error: function (error) {
            alert("error: " + JSON.stringify(error));
        },
        success: function (data) {
            setSelectOptions(region, data.regions, 'region');
        }
    });
}

function filterCity() {
    let country = $('#selectCountry').val();
    let region = $('#selectRegion').val();
    let cityElement = $('#selectCity');

    $.ajax({
        url: 'ajax/get_address_components.php',
        type: 'POST',
        dataType: 'json',
        data: {
            "country": country,
            "region": region
        },
        error: function (error) {
            alert("error: " + JSON.stringify(error));
        },
        success: function (data) {
            // alert(JSON.stringify(data))
            setSelectOptions(cityElement, data.cities, 'city');
        }
    });
}


function readURL(input) {

    var url = input.value;

    var reader = new FileReader();

    reader.onload = function (e) {
        $('#profileImg > img').attr('src', e.target.result);
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