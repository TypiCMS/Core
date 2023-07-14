// @ts-ignore
import alertify from 'alertify.js';
import fetcher from './fetcher';

const handleDeleteAttachment = async (button: HTMLElement): Promise<void> => {
    const field = button?.dataset?.field;

    if (!field) {
        return;
    }

    if (!confirm(`Delete ${field}?`)) {
        return;
    }

    try {
        const response = await fetcher('/admin/settings', {
            method: 'PATCH',
            body: JSON.stringify({ [field]: 'delete' }),
        });

        if (!response.ok) {
            throw new Error('Network response was not ok.');
        }

        document.querySelector('.fieldset-preview')?.remove();
    } catch (error) {
        alertify.error('An error occurred while deleting attachment.');
        console.error('There was a problem with the fetch operation:', error);
    }
};

export default (): void => {
    const buttons: NodeListOf<HTMLElement> =
        document.querySelectorAll('.delete-attachment');

    buttons.forEach((button) => {
        button?.addEventListener('click', () => handleDeleteAttachment(button));
    });
};
