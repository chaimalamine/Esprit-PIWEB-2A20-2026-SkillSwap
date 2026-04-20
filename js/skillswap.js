// Utilitaire validation
function validerChamp(valeur, msgId, options) {
    const el = document.getElementById(msgId);
    if (!el) return true;
    const v = valeur.trim();

    if (options.requis && v.length === 0) {
        el.textContent = 'Ce champ est obligatoire';
        el.style.color = 'red';
        return false;
    }
    if (options.min && v.length < options.min) {
        el.textContent = 'Minimum ' + options.min + ' caractères';
        el.style.color = 'red';
        return false;
    }
    if (options.max && v.length > options.max) {
        el.textContent = 'Maximum ' + options.max + ' caractères';
        el.style.color = 'red';
        return false;
    }
    if (options.noOnlyDigits && /^\d+$/.test(v)) {
        el.textContent = 'Ne peut pas contenir uniquement des chiffres';
        el.style.color = 'red';
        return false;
    }
    if (v.length > 0) {
        el.textContent = options.okMsg || '✓ Valide';
        el.style.color = 'green';
    } else {
        el.textContent = '';
    }
    return true;
}

// COURS

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

    // Catégorie (optionnel mais si rempli : min 2, max 50)
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

// CHAPITRE

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

// DEMANDE
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

    // Compétence souhaitée (optionnel)
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