//================================================
// MODALE DE CONFIRMATION PERSONNALISÉE
//================================================

// Injection de la modale dans le DOM
const modalHTML = `
<div id="confirmModal" style="
    display:none; position:fixed; inset:0; z-index:9999;
    background:rgba(15,5,32,0.55); backdrop-filter:blur(4px);
    align-items:center; justify-content:center;
">
    <div style="
        background:white; border-radius:20px; padding:36px 32px;
        max-width:400px; width:90%; box-shadow:0 24px 60px rgba(0,0,0,0.2);
        text-align:center; animation: popIn 0.2s ease;
    ">
        <div id="confirmIcon" style="
            width:56px; height:56px; border-radius:50%;
            background:#fee2e2; display:flex; align-items:center;
            justify-content:center; margin:0 auto 18px;
        ">
            <svg width="26" height="26" fill="none" stroke="#dc2626" stroke-width="2.5" viewBox="0 0 24 24">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14H6L5 6"/>
                <path d="M10 11v6M14 11v6"/>
                <path d="M9 6V4h6v2"/>
            </svg>
        </div>
        <h3 id="confirmTitle" style="color:#1a0533;font-size:18px;font-weight:700;margin-bottom:8px;"></h3>
        <p id="confirmText" style="color:#6b7280;font-size:14px;line-height:1.6;margin-bottom:28px;"></p>
        <div style="display:flex;gap:12px;justify-content:center;">
            <button id="confirmCancel" style="
                padding:10px 24px; border-radius:10px; border:1.5px solid #e5e7eb;
                background:white; color:#374151; font-size:14px; font-weight:600;
                cursor:pointer; transition:all 0.15s; font-family:inherit;
            ">Annuler</button>
            <button id="confirmOk" style="
                padding:10px 24px; border-radius:10px; border:none;
                background:#dc2626; color:white; font-size:14px; font-weight:600;
                cursor:pointer; transition:all 0.15s; font-family:inherit;
            ">Supprimer</button>
        </div>
    </div>
</div>
<style>
@keyframes popIn {
    from { transform: scale(0.9); opacity: 0; }
    to   { transform: scale(1);   opacity: 1; }
}
#confirmCancel:hover { background:#f9fafb; border-color:#d1d5db; }
#confirmOk:hover     { background:#b91c1c; }
</style>
`;
document.addEventListener('DOMContentLoaded', () => {
    document.body.insertAdjacentHTML('beforeend', modalHTML);
});

/**
 * Affiche la modale de confirmation personnalisée.
 * @param {string} title   - Titre de la modale
 * @param {string} text    - Message descriptif
 * @param {string} okLabel - Texte du bouton de confirmation
 * @returns {Promise<boolean>}
 */
function confirmer(title = 'Confirmer la suppression', text = 'Cette action est irréversible.', okLabel = 'Supprimer') {
    return new Promise((resolve) => {
        const modal  = document.getElementById('confirmModal');
        const btnOk  = document.getElementById('confirmOk');
        const btnAnn = document.getElementById('confirmCancel');

        document.getElementById('confirmTitle').textContent = title;
        document.getElementById('confirmText').textContent  = text;
        btnOk.textContent = okLabel;

        modal.style.display = 'flex';

        const close = (result) => {
            modal.style.display = 'none';
            btnOk.removeEventListener('click', onOk);
            btnAnn.removeEventListener('click', onCancel);
            modal.removeEventListener('click', onBackdrop);
            resolve(result);
        };

        const onOk      = () => close(true);
        const onCancel  = () => close(false);
        const onBackdrop = (e) => { if (e.target === modal) close(false); };

        btnOk.addEventListener('click', onOk);
        btnAnn.addEventListener('click', onCancel);
        modal.addEventListener('click', onBackdrop);
    });
}

// Intercepter tous les liens de suppression (class="btn-red" ou onclick=confirm)
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('a[href*="supprimer"], a[href*="del_chapitre"], a[href*="delete"]').forEach(link => {
        link.removeAttribute('onclick');
        link.addEventListener('click', async (e) => {
            e.preventDefault();
            const confirmed = await confirmer(
                'Voulez-vous supprimer ?',
                'Cette action est définitive et ne peut pas être annulée.',
                'Oui, supprimer'
            );
            if (confirmed) window.location.href = link.href;
        });
    });
});

//================================================
// UTILITAIRE VALIDATION
//================================================
function validerChamp(valeur, msgId, options) {
    const el = document.getElementById(msgId);
    if (!el) return true;
    const v = valeur.trim();

    if (options.requis && v.length === 0) {
        el.textContent = 'Ce champ est obligatoire';
        el.style.color = '#dc2626';
        return false;
    }
    if (options.min && v.length < options.min) {
        el.textContent = 'Minimum ' + options.min + ' caractères';
        el.style.color = '#dc2626';
        return false;
    }
    if (options.max && v.length > options.max) {
        el.textContent = 'Maximum ' + options.max + ' caractères';
        el.style.color = '#dc2626';
        return false;
    }
    if (options.noOnlyDigits && /^\d+$/.test(v)) {
        el.textContent = 'Ne peut pas contenir uniquement des chiffres';
        el.style.color = '#dc2626';
        return false;
    }
    if (v.length > 0) {
        el.textContent = options.okMsg || '✓ Valide';
        el.style.color = '#16a34a';
    } else {
        el.textContent = '';
    }
    return true;
}

//================================================
// COURS
//================================================
document.getElementById('coursForm') && document.getElementById('coursForm').addEventListener('submit', function (e) {
    e.preventDefault();
    let valid = true;

    valid &= validerChamp(
        document.getElementById('titre').value,
        'titreMsg',
        { requis: true, min: 3, max: 100, noOnlyDigits: true, okMsg: '✓ Titre valide' }
    );

    valid &= validerChamp(
        document.getElementById('description').value,
        'descMsg',
        { requis: true, min: 10, max: 500, okMsg: '✓ Description valide' }
    );

    const catEl = document.getElementById('categorie');
    if (catEl && catEl.value.trim().length > 0) {
        valid &= validerChamp(
            catEl.value,
            'categorieMsg',
            { min: 2, max: 50, noOnlyDigits: true, okMsg: '✓ Catégorie valide' }
        );
    }

    if (valid) this.submit();
});

//================================================
// CHAPITRE
//================================================
document.getElementById('chapitreForm') && document.getElementById('chapitreForm').addEventListener('submit', function (e) {
    e.preventDefault();
    let valid = true;

    valid &= validerChamp(
        document.getElementById('titre').value,
        'titreMsg',
        { requis: true, min: 3, max: 100, noOnlyDigits: true, okMsg: '✓ Titre valide' }
    );

    valid &= validerChamp(
        document.getElementById('contenu').value,
        'contenuMsg',
        { requis: true, min: 10, max: 2000, okMsg: '✓ Contenu valide' }
    );

    if (valid) this.submit();
});

//================================================
// DEMANDE
//================================================
document.getElementById('demandeForm') && document.getElementById('demandeForm').addEventListener('submit', function (e) {
    e.preventDefault();
    let valid = true;

    valid &= validerChamp(
        document.getElementById('titre').value,
        'titreMsg',
        { requis: true, min: 3, max: 100, noOnlyDigits: true, okMsg: '✓ Titre valide' }
    );

    valid &= validerChamp(
        document.getElementById('description').value,
        'descMsg',
        { requis: true, min: 10, max: 500, okMsg: '✓ Description valide' }
    );

    const compEl = document.getElementById('competence_souhaitee');
    if (compEl && compEl.value.trim().length > 0) {
        valid &= validerChamp(
            compEl.value,
            'competenceMsg',
            { min: 2, max: 80, noOnlyDigits: true, okMsg: '✓ Compétence valide' }
        );
    }

    if (valid) this.submit();
});
