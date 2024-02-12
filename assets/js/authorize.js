var base_url = mydataninja_vars.base_url;
var currency = mydataninja_vars.currency;
var store_name = mydataninja_vars.name;
var front_base_url = mydataninja_vars.front_base_url;

function authorize() {
    window.open(`${front_base_url}/crm/woocommerce?name=${store_name}&currency=${currency}&base_url=${base_url}`);
}