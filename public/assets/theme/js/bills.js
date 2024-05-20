let articleWarehouses = [];
let partners = [];
let articleAmounts = [];
let totalPrice = [];
let currentTable = -1;

function fetchAllArticles()
{
    let url = '/company/articles/fetch-all';
    let list = document.getElementById('article-amounts');
    let id = 0;

    $.ajax({
        url: url,
        success: function (response) {
            response.forEach((articleWarehouse) => {
                articleWarehouses.push(
                    {
                        article_id: articleWarehouse.article_id,
                        article_title: articleWarehouse.article_title,
                        warehouse_id: articleWarehouse.warehouse_id,
                        warehouse_title: articleWarehouse.warehouse_title,
                        current_amount: articleWarehouse.current_amount,
                        price: articleWarehouse.price
                    }
                );
                list.innerHTML += '<option value="' + (id++) + '" label="' + articleWarehouse.article_title + ' - ' + articleWarehouse.warehouse_title + ' ' + articleWarehouse.price + ' RSD"></option>';
            });
        }
    });
}

function fetchAllPartners()
{
    let url = '/company/partners/fetch-all';
    let list = document.getElementById('virman-partner');
    let id = 0;

    $.ajax({
        url: url,
        success: function (response) {
            response.forEach((partner) => {
                partners.push(
                    {
                        partner_id: partner.partner_id,
                        partner_title: partner.partner_title,
                        partner_tin: partner.partner_tin
                    }
                );
                list.innerHTML += '<option value="' + (id++) + '" label="' + partner.partner_title + ' - ' + partner.partner_tin + '"></option>';
            });
        }
    });
}

function selectArticle()
{
    let id = document.getElementById('article-amounts').value;
    if (id >= 0)
    {
        document.getElementById('article-message').hidden = true;
        document.getElementById('article-current-amount').hidden = false;
        document.getElementById('current-amount').innerHTML = articleWarehouses[id].current_amount;
    }
    else
    {
        document.getElementById('article-message').hidden = false;
        document.getElementById('article-current-amount').hidden = true;
    }

    checkAmount();
}

function addArticle()
{
    document.getElementById('payment-methods').disabled = false;
    document.getElementById('bill').hidden = false;
    document.getElementById('empty-bill').hidden = true;

    let articleWarehouse = articleWarehouses[document.getElementById('article-amounts').value];
    let amount = document.getElementById('amount').value;

    articleWarehouse.current_amount -= amount;
    document.getElementById('current-amount').innerHTML = articleWarehouse.current_amount;
    totalPrice[currentTable] += amount * articleWarehouse.price;
    document.getElementById('total-price').innerHTML = totalPrice[currentTable];

    let itemFound = false;
    articleAmounts[currentTable].forEach((articleAmount) => {
        if (articleAmount.article_id == articleWarehouse.article_id && articleAmount.warehouse_id == articleWarehouse.warehouse_id )
        {
            articleAmount.amount = Number(amount) + Number(articleAmount.amount);
            itemFound = true;
        }
    });

    if (!itemFound) {
        articleAmounts[currentTable].push({
            article_id: articleWarehouse.article_id,
            article_title: articleWarehouse.article_title,
            warehouse_id: articleWarehouse.warehouse_id,
            warehouse_title: articleWarehouse.warehouse_title,
            amount: amount,
            price: articleWarehouse.price
        });
    }

    document.getElementById('article-amounts').value = -1;
    document.getElementById('amount').value = null;

    checkAmount();
    checkTables();
}

function updateBillView()
{
    let list = document.getElementById('bill-list');
    list.innerHTML = '';

    articleAmounts[currentTable].forEach((articleAmount) => {
        list.innerHTML += '<tr>'
            + '<td>' + articleAmount.article_title + '</td>'
            + '<td>' + articleAmount.warehouse_title + '</td>'
            + '<td>' + articleAmount.amount + '</td>'
            + '<td>' + articleAmount.price + '</td>'
            + '</tr>';
    });
}

function selectPaymentMethod()
{
    let value = document.getElementById('payment-methods').value;
    document.getElementById('cash').hidden = value != 1;
    document.getElementById('check').hidden = value != 2;
    document.getElementById('card').hidden = value != 3;
    document.getElementById('virman').hidden = value != 4;
    checkSubmit();
}

function checkAmount()
{
    document.getElementById('button').disabled =
        document.getElementById('article-amounts').value == -1 ||
        document.getElementById('amount').value <= 0 ||
        document.getElementById('amount').value > articleWarehouses[document.getElementById('article-amounts').value].current_amount;
}

function checkSubmit()
{
    let paymentMethodValue = document.getElementById('payment-methods').value;
    document.getElementById('submit').disabled =
        paymentMethodValue == 0 ||
        (paymentMethodValue == 1 && (document.getElementById('cash-amount').value < totalPrice[currentTable])) ||
        (paymentMethodValue == 2 && (document.getElementById('check-first-name').value.trim() == '' || document.getElementById('check-last-name').value.trim() == '' || document.getElementById('check-id').value.trim() == '')) ||
        (paymentMethodValue == 3 && (document.getElementById('card-id').value.trim() == '' || document.getElementById('card-slip-bill').value.trim() == '')) ||
        (paymentMethodValue == 4 && document.getElementById('virman-partner').value == -1);

}

function submitForm()
{
    let paymentMethodValue = document.getElementById('payment-methods').value;
    let url = '/company/bill/create/' + paymentMethodValue;

    if (paymentMethodValue == 1)
    {
        url += '?cash-amount=' + document.getElementById('cash-amount').value;
        if (document.getElementById('cash-id').value.trim() != '')
        {
            url += '&cash-id=' + document.getElementById('cash-id').value;

        }
    }
    else if (paymentMethodValue == 2)
    {
        url +=
            '?check-first-name=' + document.getElementById('check-first-name').value +
            '&check-last-name=' + document.getElementById('check-last-name').value +
            '&check-id=' + document.getElementById('check-id').value;
    }
    else if (paymentMethodValue == 3)
    {
        url +=
            '?card-id=' + document.getElementById('card-id').value +
            '&card-slip=' + document.getElementById('card-slip-bill').value;
    }
    else
    {
        url += '?virman-partner=' + partners[document.getElementById('virman-partner').value].partner_id;
    }

    $.ajax({
        url: url,
        success: function (response) {
            let billId = response.bill_id;

            articleAmounts[currentTable].forEach((articleAmount) => {
                let aaUrl = '/company/bill/article-amount/' + billId + '/' + articleAmount.article_id + '/' + articleAmount.warehouse_id + '/' + articleAmount.amount;

                $.ajax({
                    url: aaUrl,
                    success: function (response) {

                    }
                });
            });

            resetForm();
        }
    });
    checkTables();
}

function resetForm()
{
    document.getElementById('cash-id').value =  null;
    document.getElementById('cash-amount').value =  null;
    document.getElementById('check-id').value =  null;
    document.getElementById('check-first-name').value =  null;
    document.getElementById('check-last-name').value =  null;
    document.getElementById('card-id').value =  null;
    document.getElementById('card-slip-bill').value =  null;
    document.getElementById('virman-partner').value =  -1;
    document.getElementById('payment-methods').value = 0;

    document.getElementById('payment-methods').disabled = true;
    document.getElementById('bill').hidden = true;
    document.getElementById('empty-bill').hidden = false;
    document.getElementById('bill-list').innerHTML = '';

    articleAmounts[currentTable] = [];
    totalPrice[currentTable] = 0;

    selectArticle();
    selectPaymentMethod();
    checkTables();
}

window.addEventListener('beforeunload', (event) => {
    articleAmounts.forEach((articleAmount) => {
        if (articleAmount.length > 0)
        {
            if (document.activeElement.href !== undefined)
            {
                if (!event) event = window.event;
                event.cancelBubble = true;

                if (event.stopPropagation)
                {
                    event.stopPropagation();
                    event.preventDefault();
                }

                event.returnValue = '';
            }
        }
    })
});

function selectTable(index)
{
    let tables = document.querySelectorAll('[data-type="table"]');
    tables.forEach((table) => {
        if (table.getAttribute('bill-id') == index)
        {
            table.classList.add('selected-table');
        }
        else
        {
            table.classList.remove('selected-table');
        }
    });
    currentTable = index;
    updateBillView();
    document.getElementById('total-price').innerHTML = totalPrice[currentTable];

    let currentTableEmptyBill = articleAmounts[currentTable].length == 0;
    document.getElementById('payment-methods').disabled = currentTableEmptyBill;
    document.getElementById('bill').hidden = currentTableEmptyBill;
    document.getElementById('empty-bill').hidden = !currentTableEmptyBill;

    checkTables();
}

function checkTables()
{
    let tables = document.querySelectorAll('[data-type="table"]');
    let index = 0;
    tables.forEach((table) => {
        if (articleAmounts[index++].length > 0)
        {
            table.classList.add('active-table');
            table.innerHTML = 'zauzet';
        }
        else
        {
            table.classList.remove('active-table');
            table.innerHTML = '';
        }
    });
}

window.onload = function (e)
{
    let tables = document.querySelectorAll('[data-type="table"]');
    let index = 0;
    tables.forEach((table) => {
        if (index == 0)
        {
            table.classList.add('selected-table');
            currentTable = 0;
        }
        table.innerHTML = '';
        table.removeAttribute('onclick');
        table.setAttribute('onclick', 'selectTable(' + index + ')');
        table.setAttribute('bill-id', index++);
        articleAmounts.push([]);
        totalPrice.push(0);
    });
    fetchAllArticles();
    fetchAllPartners();
}
