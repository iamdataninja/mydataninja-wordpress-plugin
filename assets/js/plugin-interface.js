document.addEventListener('DOMContentLoaded', function() {
    var checkbox = document.getElementById('_use_existing_cog_field');
    var label = document.getElementById('_existing_cog_field_name_label');

    label.style.display = checkbox.checked ? 'block' : 'none';

    checkbox.addEventListener('change', function() {
        label.style.display = checkbox.checked ? 'block' : 'none';
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var checkbox1 = document.getElementById('_include_profits');
    var checkbox2 = document.getElementById('_use_existing_cog_field');
    var label = document.getElementById('_existing_cog_field_name_label');
    var selectField = document.getElementById('_existing_cog_field_name');

    label.style.display = checkbox2.checked ? 'block' : 'none';

    checkbox1.addEventListener('change', function() {
        if (checkbox1.checked) {
            checkbox2.checked = false;
            label.style.display = 'none';
            selectField.value = '_mydataninja_cost_of_goods';
            selectField.disabled = true;
        } else {
            selectField.disabled = false;
        }
    });

    checkbox2.addEventListener('change', function() {
        if (checkbox2.checked) {
            checkbox1.checked = false;
            label.style.display = 'block';
            selectField.disabled = false;
            selectField.selectedIndex = 0;
        } else {
            label.style.display = 'none';
            selectField.value = '_mydataninja_cost_of_goods';
            selectField.disabled = true;
        }
    });
});