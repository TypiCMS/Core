export default (): void => {
    window.addEventListener('scroll', (): void => {
        const anchorTop: HTMLDivElement = document.getElementById('anchor-top') as HTMLDivElement;
        if (window.scrollY > 300) {
            anchorTop.classList.remove('disabled');
        } else {
            anchorTop.classList.add('disabled');
        }
    });
};
