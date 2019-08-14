// Adminhtml Category

function categorySubmit2(url, useAjax) {
    document.getElementById('apply_button').value = 'all_products';
    categorySubmit(url, useAjax);
}

function categorySubmit3(url, useAjax) {
    document.getElementById('apply_button').value = 'all_products_without_commoditites';
    categorySubmit(url, useAjax);
}