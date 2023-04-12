import fetcher from './fetcher';
import alertify from 'alertify.js';

type DeleteButton = HTMLElement | null;

export default (): void => {
    const attachmentElements: NodeListOf<DeleteButton> = document.querySelectorAll('.delete-attachment');

    attachmentElements.forEach((attachment: DeleteButton) => {
        const field: string | undefined = attachment?.dataset?.field;

        if (field) {
            if (attachment) {
                attachment.addEventListener('click', async () => {
                    if (!confirm(`Delete ${field}?`)) {
                        return false;
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
                });
            }
        }
    });
};
