document.addEventListener('DOMContentLoaded', function() {
    var includeCogCheckbox = document.querySelector('input[name="_include_profits"]');
    var useExistingCogCheckbox = document.querySelector('input[name="_use_existing_cog_field"]');
    var selectField = document.getElementById('_existing_cog_field_name');

    selectField.style.display = useExistingCogCheckbox.checked ? 'block' : 'none';

    includeCogCheckbox.addEventListener('change', function() {
        if (includeCogCheckbox.checked) {
            useExistingCogCheckbox.checked = false;
            selectField.style.display = 'none';
        } else {
            selectField.style.display = 'block';
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