(function () {
	'use strict';

	const pending = new WeakMap();

	function visible(element) {
		return element && element.getClientRects().length > 0
			&& window.getComputedStyle(element).visibility !== 'hidden';
	}

	function singleRadioQuestion(step, selected) {
		const fields = Array.from(step.querySelectorAll('input:not([type="hidden"]):not([type="submit"]):not([type="button"]), select, textarea'))
			.filter(function (field) {
				return !field.disabled && visible(field.closest('.forminator-field') || field);
			});
		return fields.length > 0 && fields.every(function (field) {
			return field.type === 'radio' && field.name === selected.name;
		});
	}

	document.addEventListener('change', function (event) {
		const selected = event.target;
		if (!(selected instanceof HTMLInputElement) || selected.type !== 'radio' || !selected.checked) {
			return;
		}
		const form = selected.closest('form.forminator-custom-form');
		const step = selected.closest('.forminator-pagination');
		if (!form || !form.closest('[data-scs-planner]') || !step || !visible(step)) {
			return;
		}

		window.clearTimeout(pending.get(form));
		pending.set(form, window.setTimeout(function () {
			pending.delete(form);
			const next = form.querySelector('.forminator-pagination-footer .forminator-button-next');
			if (selected.checked && visible(step) && singleRadioQuestion(step, selected)
				&& next && !next.disabled && visible(next) && !next.classList.contains('forminator-hidden')) {
				next.click();
			}
		}, 400));
	});

	// Do not let a pending selection override a visitor's manual navigation.
	document.addEventListener('click', function (event) {
		const button = event.target.closest('.forminator-pagination-footer button');
		const form = button && button.closest('form.forminator-custom-form');
		if (form) {
			window.clearTimeout(pending.get(form));
			pending.delete(form);
		}
	});
})();
