jQuery(function ($) {
    const categories = wcDynamicConsentsData.categories;
    const products = wcDynamicConsentsData.products;

    /**
     * Removes a consent row from the table.
     */
    $('#dynamic-consents-table').on('click', '.remove-consent', function () {
        $(this).closest('tr').remove();
    });

    /**
     * Adds a new consent row to the table.
     */
    $('.add-consent').on('click', function () {
        const table = $('#dynamic-consents-table tbody');
        const rowCount = table.find('tr').length;

        const template = $('#consent-row-template').html();
        const newRow = template.replace(/__INDEX__/g, rowCount);

        const newElement = $(newRow);
        table.append(newElement);

        // Trigger change to refresh the second select dropdown
        newElement.find('.consent-type').trigger('change');
    });

    /**
     * Adds a new condition for a consent (e.g., for a product/category).
     */
    $('#dynamic-consents-table').on('click', '.add-condition', function () {
        const conditionsWrapper = $(this).siblings('.consent-conditions');
        const consentIndex = $(this).closest('tr').index();
        const conditionCount = conditionsWrapper.children('.consent-condition').length;

        let conditionTemplate = $('#condition-row-template').html();
        conditionTemplate = conditionTemplate.replace(/__CONSENT_INDEX__/g, consentIndex);
        conditionTemplate = conditionTemplate.replace(/__CONDITION_INDEX__/g, conditionCount);

        const newCondition = $(conditionTemplate);
        conditionsWrapper.append(newCondition);

        //  Trigger change event after adding a new condition
        newCondition.find('.consent-type').trigger('change');
    });

    /**
     * Removes a condition from a consent.
     */
    $('#dynamic-consents-table').on('click', '.remove-condition', function () {
        $(this).closest('.consent-condition').remove();
    });

    /**
     * Updates the second `select` dropdown based on the first `select` selection.
     */
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

    /**
     *  Refreshes all conditions on page load to ensure `select` elements are correctly populated.
     */
    $('.consent-type').each(function () {
        $(this).trigger('change');
    });
});
s