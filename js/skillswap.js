// SkillSwap — skillswap.js
// Validation réelle des formulaires : cours, chapitre, demande

document.addEventListener('DOMContentLoaded', function () {

    // ══════════════════════════════════════════════════════════════
    // UTILITAIRES
    // ══════════════════════════════════════════════════════════════

    function showError(field, message) {
        clearError(field);
        field.style.borderColor = '#ef4444';
        var err = document.createElement('span');
        err.className = 'js-error';
        err.style.cssText = 'color:#ef4444;font-size:12px;display:block;margin-top:4px;margin-bottom:10px';
        err.textContent = '⚠ ' + message;
        field.insertAdjacentElement('afterend', err);
    }

    function showSuccess(field) {
        clearError(field);
        field.style.borderColor = '#22c55e';
    }

    function clearError(field) {
        field.style.borderColor = '';
        var existing = field.parentNode.querySelector('.js-error');
        if (existing) existing.remove();
    }

    // ══════════════════════════════════════════════════════════════
    // RÈGLES DE VALIDATION PAR CHAMP
    // ══════════════════════════════════════════════════════════════

    var rules = {

        // ── Cours ──────────────────────────────────────────────────
        'cours-titre': {
            validate: function (val) {
                if (val.trim() === '')           return 'Le titre du cours est obligatoire.';
                if (val.trim().length < 3)       return 'Le titre doit contenir au moins 3 caractères.';
                if (val.trim().length > 100)     return 'Le titre ne peut pas dépasser 100 caractères.';
                if (/^\d+$/.test(val.trim()))    return 'Le titre ne peut pas être uniquement des chiffres.';
                return null;
            }
        },
        'cours-description': {
            validate: function (val) {
                if (val.trim() === '')           return 'La description du cours est obligatoire.';
                if (val.trim().length < 10)      return 'La description doit contenir au moins 10 caractères.';
                if (val.trim().length > 500)     return 'La description ne peut pas dépasser 500 caractères.';
                return null;
            }
        },

        // ── Chapitre ───────────────────────────────────────────────
        'chapitre-titre': {
            validate: function (val) {
                if (val.trim() === '')           return 'Le titre du chapitre est obligatoire.';
                if (val.trim().length < 3)       return 'Le titre doit contenir au moins 3 caractères.';
                if (val.trim().length > 100)     return 'Le titre ne peut pas dépasser 100 caractères.';
                if (/^\d+$/.test(val.trim()))    return 'Le titre ne peut pas être uniquement des chiffres.';
                return null;
            }
        },
        'chapitre-contenu': {
            validate: function (val) {
                if (val.trim() === '')           return 'Le contenu du chapitre est obligatoire.';
                if (val.trim().length < 10)      return 'Le contenu doit contenir au moins 10 caractères.';
                if (val.trim().length > 2000)    return 'Le contenu ne peut pas dépasser 2000 caractères.';
                return null;
            }
        },
        'chapitre-pdf': {
            validate: function (val, input) {
                if (!input.files || input.files.length === 0) return null;
                var file = input.files[0];
                if (!file.name.toLowerCase().endsWith('.pdf'))  return 'Seuls les fichiers PDF sont acceptés.';
                if (file.size > 5 * 1024 * 1024)               return 'Le fichier PDF ne doit pas dépasser 5 Mo.';
                return null;
            }
        },

        // ── Demande ────────────────────────────────────────────────
        'demande-titre': {
            validate: function (val) {
                if (val.trim() === '')           return 'Le titre de la demande est obligatoire.';
                if (val.trim().length < 3)       return 'Le titre doit contenir au moins 3 caractères.';
                if (val.trim().length > 100)     return 'Le titre ne peut pas dépasser 100 caractères.';
                if (/^\d+$/.test(val.trim()))    return 'Le titre ne peut pas être uniquement des chiffres.';
                return null;
            }
        },
        'demande-description': {
            validate: function (val) {
                if (val.trim() === '')           return 'La description de la demande est obligatoire.';
                if (val.trim().length < 10)      return 'La description doit contenir au moins 10 caractères.';
                if (val.trim().length > 500)     return 'La description ne peut pas dépasser 500 caractères.';
                return null;
            }
        }
    };

    // ══════════════════════════════════════════════════════════════
    // DÉTECTION DU TYPE DE FORMULAIRE
    // ══════════════════════════════════════════════════════════════

    function detectFormType(form) {
        var action = form.action || '';
        if (action.indexOf('cours') !== -1)    return 'cours';
        if (action.indexOf('chapitre') !== -1) return 'chapitre';
        if (action.indexOf('demande') !== -1)  return 'demande';
        return null;
    }

    // ══════════════════════════════════════════════════════════════
    // COMPTEUR DE CARACTÈRES
    // ══════════════════════════════════════════════════════════════

    var charLimits = {
        'cours-titre': 100,
        'cours-description': 500,
        'chapitre-titre': 100,
        'chapitre-contenu': 2000,
        'demande-titre': 100,
        'demande-description': 500
    };

    function addCharCounter(field, key) {
        var limit = charLimits[key];
        if (!limit) return;
        var counter = document.createElement('small');
        counter.className = 'js-char-counter';
        counter.style.cssText = 'display:block;text-align:right;font-size:11px;color:#999;margin-top:-8px;margin-bottom:10px';
        counter.textContent = field.value.length + ' / ' + limit;
        field.insertAdjacentElement('afterend', counter);
        field.addEventListener('input', function () {
            var len = field.value.length;
            counter.textContent = len + ' / ' + limit;
            counter.style.color = len > limit ? '#ef4444' : (len > limit * 0.85 ? '#f59e0b' : '#999');
        });
    }

    // ══════════════════════════════════════════════════════════════
    // VALIDATION EN TEMPS RÉEL + À LA SOUMISSION
    // ══════════════════════════════════════════════════════════════

    document.querySelectorAll('form').forEach(function (form) {
        var formType = detectFormType(form);
        if (!formType) return;

        form.querySelectorAll('input[type="text"], textarea, input[type="file"]').forEach(function (field) {
            var ruleKey = formType + '-' + field.name;
            var rule = rules[ruleKey];
            if (!rule) return;

            if (field.type !== 'file') addCharCounter(field, ruleKey);

            field.addEventListener('blur', function () {
                var error = rule.validate(field.value, field);
                if (error) showError(field, error);
                else showSuccess(field);
            });

            field.addEventListener('input', function () {
                var error = rule.validate(field.value, field);
                if (!error) showSuccess(field);
            });

            if (field.type === 'file') {
                field.addEventListener('change', function () {
                    var error = rule.validate(field.value, field);
                    if (error) showError(field, error);
                    else showSuccess(field);
                });
            }
        });

        form.addEventListener('submit', function (e) {
            var valid = true;
            var firstErrorField = null;

            form.querySelectorAll('input[type="text"], textarea, input[type="file"]').forEach(function (field) {
                var ruleKey = formType + '-' + field.name;
                var rule = rules[ruleKey];
                if (!rule) return;
                var error = rule.validate(field.value, field);
                if (error) {
                    showError(field, error);
                    valid = false;
                    if (!firstErrorField) firstErrorField = field;
                } else {
                    showSuccess(field);
                }
            });

            if (!valid) {
                e.preventDefault();
                if (firstErrorField) {
                    firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstErrorField.focus();
                }
                return;
            }

            var btn = form.querySelector('button[type="submit"]');
            if (btn) { btn.disabled = true; btn.textContent = 'Enregistrement...'; }
        });
    });

    // ══════════════════════════════════════════════════════════════
    // APERÇU FICHIER PDF
    // ══════════════════════════════════════════════════════════════

    document.querySelectorAll('input[type="file"][accept=".pdf"]').forEach(function (input) {
        input.addEventListener('change', function () {
            var existing = input.parentNode.querySelector('.js-file-preview');
            if (existing) existing.remove();
            if (input.files.length > 0) {
                var file = input.files[0];
                var preview = document.createElement('p');
                preview.className = 'js-file-preview';
                var sizeKo = (file.size / 1024).toFixed(1);
                var color = file.size > 5 * 1024 * 1024 ? '#ef4444' : '#7b2ff7';
                preview.style.cssText = 'font-size:13px;color:' + color + ';margin-bottom:12px';
                preview.textContent = '📄 ' + file.name + ' (' + sizeKo + ' Ko)';
                input.insertAdjacentElement('afterend', preview);
            }
        });
    });

    // ══════════════════════════════════════════════════════════════
    // CONFIRMATION SUPPRESSION
    // ══════════════════════════════════════════════════════════════

    document.querySelectorAll('a[href*="delete_"]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.')) {
                e.preventDefault();
            }
        });
    });

});
