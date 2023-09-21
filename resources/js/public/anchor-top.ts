export default (): void => {
    window.addEventListener('scroll', (): void => {
        const anchorTop: HTMLAnchorElement = document.getElementById('anchor-top') as HTMLAnchorElement;
        if (window.scrollY > 300) {
            anchorTop.classList.remove('disabled');
        } else {
            anchorTop.classList.add('disabled');
        }
    });
};
