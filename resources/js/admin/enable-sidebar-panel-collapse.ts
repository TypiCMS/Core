import alertify from 'alertify.js';
import fetcher from './fetcher';

export default (): void => {
    function updatePreferences(key: string, value: string): void {
        fetcher('/api/users/current/update-preferences', {
            method: 'POST',
            body: JSON.stringify({ [key]: value }),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Network response was not ok.');
                }
            })
            .catch((error) => {
                alertify.error("User preference couldn't be set.");
                console.error('There was a problem with the fetch operation:', error);
            });
    }

    document.querySelectorAll('.panel-collapse').forEach((panel: Element) => {
        panel.addEventListener('hide.bs.collapse', () => {
            updatePreferences(`menus_${panel.getAttribute('id')}_collapsed`, 'true');
        });

        panel.addEventListener('show.bs.collapse', () => {
            updatePreferences(`menus_${panel.getAttribute('id')}_collapsed`, '');
        });
    });
};
