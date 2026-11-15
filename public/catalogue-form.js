const form = document.querySelector('#product-form');

if (form) {
    const categories = JSON.parse(form.dataset.categories);
    const category = form.elements.category;
    const container = document.querySelector('#variants');
    const template = document.querySelector('#variant-template');
    const add = document.querySelector('#add-variant');

    function setOptions(select, options) {
        const previous = select.value;
        select.replaceChildren(new Option('Choose an option', ''));
        for (const option of options) {
            select.add(new Option(option, option));
        }
        select.value = options.includes(previous) ? previous : '';
    }

    function refreshOptions(row) {
        const options = categories[category.value];
        const method = row.querySelector('[data-key="method"]');
        setOptions(row.querySelector('[data-key="size"]'), options?.sizes ?? []);
        setOptions(row.querySelector('[data-key="finish"]'), options?.finishes ?? []);
        setOptions(method, Object.keys(options?.methods ?? {}));
        setOptions(row.querySelector('[data-key="material"]'), options?.methods[method.value] ?? []);
    }

    function updateRows() {
        const rows = container.querySelectorAll('.variant');
        rows.forEach((row, index) => {
            row.querySelector('legend').textContent = 'Variant ' + (index + 1);
            row.querySelector('.remove-variant').hidden = rows.length === 1;
            for (const field of row.querySelectorAll('[data-key]')) {
                field.name = 'variants[' + index + '][' + field.dataset.key + ']';
            }
            refreshOptions(row);
        });
        add.disabled = rows.length >= 25;
    }

    add.addEventListener('click', () => {
        container.append(template.content.cloneNode(true));
        updateRows();
        container.lastElementChild.querySelector('select').focus();
    });

    container.addEventListener('click', (event) => {
        if (event.target.matches('.remove-variant')) {
            event.target.closest('.variant').remove();
            updateRows();
            add.focus();
        }
    });

    container.addEventListener('change', (event) => {
        if (event.target.matches('[data-key="method"]')) {
            refreshOptions(event.target.closest('.variant'));
        }
    });

    category.addEventListener('change', updateRows);
    updateRows();
}
