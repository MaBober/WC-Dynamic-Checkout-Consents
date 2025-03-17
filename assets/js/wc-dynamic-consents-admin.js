jQuery(function ($) {
    const categories = wcDynamicConsentsData.categories;
    const products = wcDynamicConsentsData.products;

    $('#dynamic-consents-table').on('click', '.remove-consent', function () {
        $(this).closest('tr').remove();
    });

    $('.add-consent').on('click', function () {
        const table = $('#dynamic-consents-table tbody');
        const rowCount = table.find('tr').length;

        const template = $('#consent-row-template').html();
        const newRow = template.replace(/__INDEX__/g, rowCount);

        const newElement = $(newRow);
        table.append(newElement);

        // 🔹 Automatycznie wywołujemy zmianę, żeby odświeżyć drugi select
        newElement.find('.consent-type').trigger('change');
    });

    $('#dynamic-consents-table').on('click', '.add-condition', function () {
        const conditionsWrapper = $(this).siblings('.consent-conditions');
        const consentIndex = $(this).closest('tr').index();
        const conditionCount = conditionsWrapper.children('.consent-condition').length;

        let conditionTemplate = $('#condition-row-template').html();
        conditionTemplate = conditionTemplate.replace(/__CONSENT_INDEX__/g, consentIndex);
        conditionTemplate = conditionTemplate.replace(/__CONDITION_INDEX__/g, conditionCount);

        const newCondition = $(conditionTemplate);
        conditionsWrapper.append(newCondition);

        // 🔹 Wywołujemy zmianę po dodaniu nowego warunku
        newCondition.find('.consent-type').trigger('change');
    });

    $('#dynamic-consents-table').on('click', '.remove-condition', function () {
        $(this).closest('.consent-condition').remove();
    });

    // 🔹 Dynamiczne aktualizowanie drugiego selecta po zmianie pierwszego
    $('#dynamic-consents-table').on('change', '.consent-type', function () {
        const type = $(this).val();
        const targetSelect = $(this).siblings('.consent-target');
        const selectedValue = targetSelect.attr('data-selected'); // Pobieramy zapisany wybór

        targetSelect.empty();
        targetSelect.append('<option value="all">All</option>');

        if (type === 'category') {
            categories.forEach(category => {
                targetSelect.append(`<option value="${category.id}" ${category.id === selectedValue ? 'selected' : ''}>${category.name}</option>`);
            });
        } else if (type === 'product') {
            products.forEach(product => {
                targetSelect.append(`<option value="${product.id}" ${product.id === selectedValue ? 'selected' : ''}>${product.name}</option>`);
            });
        }
    });

    // 🔹 Odświeżenie wszystkich warunków po załadowaniu strony
    $('.consent-type').each(function () {
        $(this).trigger('change');
    });
});
