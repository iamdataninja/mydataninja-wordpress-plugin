document.addEventListener('DOMContentLoaded', function() {
    var tabLinks = document.querySelectorAll('.tab-link');
    var tabContents = document.querySelectorAll('.tab-content');

    tabLinks.forEach(function(tabLink) {
        tabLink.addEventListener('click', function() {
            var tabId = this.getAttribute('data-tab');

            // Remove current class from all tabs and contents
            tabLinks.forEach(function(tabLink) {
                tabLink.classList.remove('current');
            });
            tabContents.forEach(function(tabContent) {
                tabContent.classList.remove('current');
            });

            // Add current class to clicked tab and its content
            this.classList.add('current');
            document.getElementById(tabId).classList.add('current');
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var includeCogCheckbox = document.querySelector('input[name="_include_profits"]');
    var useExistingCogCheckbox = document.querySelector('input[name="_use_existing_cog_field"]');
    var selectField = document.getElementById('_existing_cog_field_name');

    selectField.style.display = useExistingCogCheckbox.checked ? 'block' : 'none';

    includeCogCheckbox.addEventListener('change', function() {
        if (includeCogCheckbox.checked) {
            useExistingCogCheckbox.checked = false;
            selectField.style.display = 'none';
        }
    });

    useExistingCogCheckbox.addEventListener('change', function() {
        if (useExistingCogCheckbox.checked) {
            includeCogCheckbox.checked = false;
            selectField.style.display = 'block';
            selectField.selectedIndex = 0;
        } else {
            selectField.style.display = 'none';
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var tabLinks = document.querySelectorAll('.tab-link');
    var tabContents = document.querySelectorAll('.tab-content');

    tabLinks.forEach(function(tabLink) {
        tabLink.addEventListener('click', function() {
            var tabId = this.getAttribute('data-tab');

            tabLinks.forEach(function(tabLink) {
                tabLink.classList.remove('current');
            });
            tabContents.forEach(function(tabContent) {
                tabContent.classList.remove('current');
            });

            this.classList.add('current');
            document.getElementById(tabId).classList.add('current');

            const tabs = {
                'tab-1': 'mydataninja-settings',
                'tab-2': 'mydataninja-reports',
                'tab-3': 'mydataninja-form'
            };

            const newUrl = '/wp-admin/admin.php?page=' + tabs[tabId];
            window.history.pushState({}, '', newUrl);
        });
    });
});

document.addEventListener('DOMContentLoaded', (event) => {
    const copyIcons = document.querySelectorAll('.copy-icon');

    copyIcons.forEach((icon) => {
        icon.addEventListener('click', (event) => {
            event.preventDefault();

            const anchor = event.target.tagName === 'A' ? event.target : event.target.parentElement;
            const inputId = anchor.getAttribute('data-input-id');
            const input = document.getElementById(inputId);

            if (input) {
                input.select();
                document.execCommand('copy');

                alert('Content copied to clipboard');
            }
        });
    });
});

async function sendRequest(url) {
    const response = await fetch(url, {
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + php_vars.accessToken,
        }
    });
    return await response.json();
}

function createDiv(title, value) {
    return `
    <div style="display: flex; justify-content: center; flex-direction: column; align-items: center;">
        <h4 style="font-size: 0.875rem; text-align: center; margin-bottom: 0;">${title}</h4>
        <h1 style="font-size: 1.125rem; margin-top: 0;">$${value ?? 0}</h1>
    </div>
`;
}

async function fetchAndRenderData() {
    try {
        const totals = document.getElementById('totals');
        const groupedNetworks = document.getElementById('groupedNetworks');
        const totalSales = document.getElementById('totalSales');

        const [totalsData, groupedNetworksData, totalSalesData] = await Promise.all([
            sendRequest(php_vars.apiBaseUrl + '/api/workspace/dashboard/data/totals'),
            sendRequest(php_vars.apiBaseUrl + '/api/workspace/dashboard/data/grouped-network-reports'),
            sendRequest(php_vars.apiBaseUrl + '/api/workspace/dashboard/data/totalSales')
        ]);

        renderData(totals, totalsData, ['Revenue', 'Spent', 'ROI']);
        renderGroupedNetworks(groupedNetworks, groupedNetworksData);
        renderData(totalSales, totalSalesData, ['Quantity', 'AOV']);
    } catch (error) {
        console.error('Error:', error);
        const widgetContainer = document.querySelector('.widget-container');
        widgetContainer.innerHTML = "<p>Currently, we are unable to retrieve MyDataNinja Widgets.</p>";
    }
}

function renderData(container, data, keys) {
    container.innerHTML += createDiv('Total Profit', data.profit ?? data.income);
    const dataDiv = document.createElement('div');
    dataDiv.style.display = 'flex';
    dataDiv.style.justifyContent = 'space-between';
    keys.forEach(key => dataDiv.innerHTML += createDiv(key, data[key.toLowerCase()]));
    container.appendChild(dataDiv);
}

function renderGroupedNetworks(container, data) {
    for (const group in data.data) {
        const groupDiv = document.createElement('div');
        groupDiv.className = 'widget';
        container.appendChild(groupDiv);
        groupDiv.innerHTML += `<h3>${group.charAt(0).toUpperCase() + group.slice(1)}</h3>`;
        renderData(groupDiv, data.data[group], ['Income', 'Spent', 'ROI']);
    }
}

function renderChart() {
    var ctx = document.getElementById('ordersChart').getContext('2d');
    var ordersChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                'Today (' + php_vars.todayOrders + ' - ' + php_vars.currencySymbol + php_vars.todayAverage + ')',
                'This Month (' + php_vars.monthOrders + ' - ' + php_vars.currencySymbol + php_vars.monthAverage + ')',
                'All Time (' + php_vars.allTimeOrders + ' - ' + php_vars.currencySymbol + php_vars.allTimeAverage + ')'
            ],
            datasets: [
                {
                    label: 'Average Order Total',
                    data: [php_vars.todayAverage, php_vars.monthAverage, php_vars.allTimeAverage],
                    type: 'line',
                    fill: false,
                    borderColor: '#3B82F6',
                    yAxisID: 'y2',
                },
                {
                    label: '# of Orders',
                    data: [php_vars.todayOrders, php_vars.monthOrders, php_vars.allTimeOrders],
                    backgroundColor: 'rgba(255, 78, 0, 0.7)',
                    borderColor: 'rgba(255, 78, 0, 0.8)',
                    borderWidth: 1,
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            layout: {
                padding: {
                    left: 0,
                    right: 0,
                    top: 0,
                    bottom: 0
                }
            },
            scales: {
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                },
                y2: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                },
            }
        }
    });
}

fetchAndRenderData();
renderChart();