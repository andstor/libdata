var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

function init() {
    getLoans();
}


function getLoans(loanID) {
    console.log("returning...");

        $.ajax({
            url: 'ajax/get_chart_data.php',
            type: 'POST',
            data: {
            },
            async: false, //blocks window close
            error: function (error) {
                alert("error" + JSON.stringify(error));
            },
            success: function (data) {
                console.log("done: " + JSON.stringify(data));

                let chartDataLoans = [0,0,0,0,0,0,0,0,0,0,0,0,];
                let chartDataReturns = [0,0,0,0,0,0,0,0,0,0,0,0,];

                data['loans'].forEach(e => {
                    let month = MONTHS.indexOf(e.loan_month);
                    chartDataLoans[month] = Number(e.num_loans);
                });

                data['returns'].forEach(e => {
                    let month = MONTHS.indexOf(e.return_month);
                    chartDataReturns[month] = Number(e.num_returns);
                });

                console.log(chartDataLoans)
                console.log(chartDataReturns)

                renderChart(chartDataLoans, chartDataReturns)
            }
        });
}

function renderChart(data1, data2) {


    var ctx = document.getElementById("myChart");
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: MONTHS,
            datasets: [{
                label: 'Book return',
                data: data2,
                backgroundColor: 'rgba(75, 192, 192, 0.3)',
                borderColor: 'rgb(75, 192, 192)',
                borderWidth: 3,
                pointBackgroundColor: 'rgb(75, 192, 192)',
                // borderDash: [5,10]
                },{
                label: 'Book loan',
                data: data1,
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgb(255, 99, 132)',
                pointBackgroundColor: 'rgb(255, 99, 132)',
                borderWidth: 3

            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function (value) { if (Number.isInteger(value)) { return value; } },
                        stepSize: 1
                    }
                }]
            }
        }
    });



    console.log(myChart)
}

//Runs when the document is loaded. Initialises the script.
document.addEventListener('DOMContentLoaded', function () {
    //"use strict";
    init();
});