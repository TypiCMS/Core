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
                    alertify.error("User preference couldn't be set.");
                }
            })
            .catch(() => {
                alertify.error("User preference couldn't be set.");
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
