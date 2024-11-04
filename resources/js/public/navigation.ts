export default (): void => {
    const button: HTMLButtonElement = document.getElementById('menu-button') as HTMLButtonElement;
    button.addEventListener('click', function (event: any) {
        event.target.classList.toggle('hamburger-open');
        event.preventDefault();
        return false;
    });
};
