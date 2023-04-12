import alertify from 'alertify.js';

export default function enableSidebarPanelCollapse(): boolean {
    function updatePreferences(key: string, value: string): boolean {
        let data: { [key: string]: string } = {};
        data[key] = value;

        const apiTokenElement: HTMLElement | null = document.head.querySelector('meta[name="api-token"]');

        if (apiTokenElement instanceof HTMLMetaElement) {
            fetch('/api/users/current/update-preferences', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    Authorization: `Bearer ${apiTokenElement.content}`,
                },
                body: JSON.stringify(data),
            }).catch(function () {
                alertify.error("User preference couldn't be set.");
            });
        }
        return false;
    }

    document.querySelectorAll('.panel-collapse').forEach((panel: Element) => {
        panel.addEventListener('hide.bs.collapse', () => {
            updatePreferences(`menus_${panel.getAttribute('id')}_collapsed`, 'true');
        });

        panel.addEventListener('show.bs.collapse', () => {
            updatePreferences(`menus_${panel.getAttribute('id')}_collapsed`, '');
        });
    });

    return false;
}
