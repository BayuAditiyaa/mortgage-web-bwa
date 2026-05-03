(function () {
    const toggleButtons = document.querySelectorAll('.dashboard-sidebar-toggle');
    const sidebar = document.querySelector('body > div.flex.min-h-screen > aside');

    if (!toggleButtons.length || !sidebar) {
        return;
    }

    const backdrop = document.createElement('button');
    backdrop.type = 'button';
    backdrop.className = 'dashboard-sidebar-backdrop';
    backdrop.setAttribute('aria-label', 'Close sidebar');
    document.body.appendChild(backdrop);

    const setOpen = (isOpen) => {
        document.body.classList.toggle('dashboard-sidebar-open', isOpen);
        toggleButtons.forEach((button) => {
            button.setAttribute('aria-expanded', String(isOpen));
        });
    };

    toggleButtons.forEach((button) => {
        button.setAttribute('aria-controls', 'CustomerSidebar');
        button.setAttribute('aria-expanded', 'false');
        button.addEventListener('click', () => {
            setOpen(!document.body.classList.contains('dashboard-sidebar-open'));
        });
    });

    sidebar.id = 'CustomerSidebar';
    backdrop.addEventListener('click', () => setOpen(false));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });
})();
