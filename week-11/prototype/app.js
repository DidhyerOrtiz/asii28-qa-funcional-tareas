const titles = {
    overview: 'Resumen operativo',
    campaigns: 'Campanas de regresion',
    execution: 'Ejecucion manual',
    defects: 'Seguimiento de defectos',
};

const views = [...document.querySelectorAll('[data-view]')];
const navButtons = [...document.querySelectorAll('[data-nav]')];
const viewTitle = document.querySelector('#view-title');
const content = document.querySelector('#content');
const toast = document.querySelector('#toast');

function showView(name, announce = false) {
    if (!titles[name]) return;

    views.forEach((view) => {
        const selected = view.dataset.view === name;
        view.hidden = !selected;
        view.classList.toggle('is-visible', selected);
    });

    navButtons.forEach((button) => {
        const selected = button.dataset.nav === name;
        button.classList.toggle('is-active', selected);
        if (selected) button.setAttribute('aria-current', 'page');
        else button.removeAttribute('aria-current');
    });

    viewTitle.textContent = titles[name];
    history.replaceState(null, '', `#${name}`);
    if (announce) content.focus();
}

navButtons.forEach((button) => {
    button.addEventListener('click', () => showView(button.dataset.nav, true));
});

document.querySelectorAll('[data-go]').forEach((button) => {
    button.addEventListener('click', () => showView(button.dataset.go, true));
});

document.querySelectorAll('[data-filter]').forEach((button) => {
    button.addEventListener('click', () => {
        const filter = button.dataset.filter;
        document.querySelectorAll('[data-filter]').forEach((item) => item.classList.toggle('is-active', item === button));
        document.querySelectorAll('[data-campaign-state]').forEach((card) => {
            card.hidden = filter !== 'all' && card.dataset.campaignState !== filter;
        });
    });
});

const statusInputs = [...document.querySelectorAll('input[name="status"]')];
const failureFields = document.querySelector('#failure-fields');
statusInputs.forEach((input) => {
    input.addEventListener('change', () => {
        const failed = input.value === 'FAILED' && input.checked;
        failureFields.hidden = !failed;
        failureFields.querySelectorAll('textarea, select, input').forEach((field) => {
            field.required = failed;
        });
    });
});

document.querySelector('#result-form').addEventListener('submit', (event) => {
    event.preventDefault();
    if (!event.currentTarget.reportValidity()) return;

    const status = new FormData(event.currentTarget).get('status');
    toast.textContent = `Resultado ${status} preparado localmente. Este prototipo no envio datos.`;
    toast.classList.add('is-visible');
    window.setTimeout(() => toast.classList.remove('is-visible'), 4200);
});

const initialView = location.hash.slice(1);
showView(titles[initialView] ? initialView : 'overview');
