const updateOptions = (options: RequestInit): RequestInit => {
    const update = { ...options };
    const apiTokenElement = document.head.querySelector<HTMLMetaElement>('meta[name="api-token"]');

    if (apiTokenElement) {
        update.headers = {
            ...update.headers,
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            Authorization: `Bearer ${apiTokenElement.content}`,
        };
    }

    return update;
};

const fetchWithHeaders = (url: string, options: RequestInit): Promise<Response> => {
    const updatedOptions = updateOptions(options);
    return fetch(url, updatedOptions);
};

export default fetchWithHeaders;
