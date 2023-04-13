import fetcher from './fetcher';
import alertify from 'alertify.js';

type DeleteButton = HTMLElement | null;

const handleDeleteAttachment = async (attachment: DeleteButton): Promise<void> => {
    const field = attachment?.dataset?.field;

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
            throw new Error('Failed to delete attachment. Network response was not ok.');
        }

        document.querySelector('.fieldset-preview')?.remove();
    } catch (error) {
        alertify.error('An error occurred while deleting attachment.');
        console.error('There was a problem with the fetch operation:', error);
    }
};

export default (): void => {
    const attachmentElements: NodeListOf<DeleteButton> = document.querySelectorAll('.delete-attachment');

    attachmentElements.forEach((attachment) => {
        attachment?.addEventListener('click', () => handleDeleteAttachment(attachment));
    });
};
