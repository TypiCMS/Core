export default () => {
    // Create a heading checkbox.
    const headingCheckbox = document.createElement('input');
    headingCheckbox.type = 'checkbox';
    headingCheckbox.classList.add('permissions-modules-item-title-checkbox', 'form-check-input');

    // Add a clone of the heading checkbox into each label.
    const labels = document.querySelectorAll<HTMLLabelElement>('.permissions-modules-item-title');
    labels.forEach((label) => {
        let wrapper = document.createElement('div');
        wrapper.classList.add('form-check');
        label.parentNode?.insertBefore(wrapper, label);
        wrapper.appendChild(label);
        label.prepend(headingCheckbox.cloneNode(true));
    });

    const headingCheckboxes = document.querySelectorAll<HTMLInputElement>('.permissions-modules-item-title-checkbox');
    headingCheckboxes.forEach((headingCheckbox) => {
        // Add a click event listener to each heading checkbox.
        headingCheckbox.addEventListener('click', (event) => {
            const element = event.target as HTMLInputElement,
                parentItem = element.closest('.permissions-modules-item'),
                checkboxes =
                    parentItem?.querySelectorAll<HTMLInputElement>('input[type="checkbox"]:not(:disabled)') ?? [];
            checkboxes.forEach((headingCheckbox) => (headingCheckbox.checked = element.checked));
        });
        // Set the status of each heading checkbox to checked or unchecked.
        const parentItem = headingCheckbox.closest('.permissions-modules-item'),
            numberOfCheckboxes =
                parentItem?.querySelectorAll<HTMLInputElement>('input[name="checked_permissions[]"]').length ?? 0,
            numberOfCheckedCheckboxes =
                parentItem?.querySelectorAll<HTMLInputElement>('input[name="checked_permissions[]"]:checked').length ??
                0;
        if (numberOfCheckboxes === numberOfCheckedCheckboxes) {
            headingCheckbox.checked = true;
        }
    });
};
