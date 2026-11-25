(function () {
	'use strict';

	const pending = new WeakMap();

	function visible(element) {
		return element && element.getClientRects().length > 0
			&& window.getComputedStyle(element).visibility !== 'hidden';
	}

	function nextButton(form) {
		const next = form.querySelector('.forminator-pagination-footer .forminator-button-next');
		return next && !next.disabled && visible(next) && !next.classList.contains('forminator-hidden') ? next : null;
	}

	function initCalendars() {
		const $ = window.jQuery;
		if (!$ || !$.fn.datepicker) {
			return;
		}
		document.querySelectorAll('[data-scs-planner] .scs-date-picker').forEach(function (picker) {
			const input = picker.previousElementSibling?.querySelector('.forminator-datepicker');
			if (!input || !input.dataset.startDate) {
				return;
			}
			const label = input.labels[0];
			const calendar = picker.querySelector('.scs-date-calendar');
			const clear = picker.querySelector('.scs-date-clear');
			const format = input.dataset.format;
			const settings = window.datepickerLang || {};
			const $calendar = $(calendar);
			let restoreFocus = '';

			if (label) {
				calendar.setAttribute('role', 'group');
				calendar.setAttribute('aria-labelledby', label.id);
			}
			if (input.hasAttribute('aria-describedby')) {
				calendar.setAttribute('aria-describedby', input.getAttribute('aria-describedby'));
			}

			function updateValue(date) {
				input.value = date;
				calendar.classList.toggle('scs-date-calendar--answered', Boolean(date));
				clear.disabled = !date;
				input.dispatchEvent(new Event('change', { bubbles: true }));
			}

			calendar.addEventListener('click', function (event) {
				const control = event.target.closest('.ui-datepicker-prev, .ui-datepicker-next, .ui-state-default');
				if (control) {
					restoreFocus = control.matches('.ui-datepicker-prev') ? '.ui-datepicker-prev'
						: control.matches('.ui-datepicker-next') ? '.ui-datepicker-next' : '.ui-datepicker-current-day a';
				}
			}, true);
			calendar.addEventListener('change', function (event) {
				if (event.target.matches('.ui-datepicker-month, .ui-datepicker-year')) {
					restoreFocus = event.target.matches('.ui-datepicker-month') ? '.ui-datepicker-month' : '.ui-datepicker-year';
				}
			}, true);
			calendar.addEventListener('keydown', function (event) {
				if ((event.key === 'Enter' || event.key === ' ') && event.target.matches('.ui-datepicker-prev, .ui-datepicker-next')) {
					event.preventDefault();
					event.target.click();
				}
			});

			$calendar.datepicker({
				...settings,
				dateFormat: format,
				minDate: $.datepicker.parseDate('yy-mm-dd', input.dataset.startDate),
				firstDay: Number(input.dataset.startOfWeek) || 0,
				changeMonth: true,
				changeYear: true,
				onSelect: updateValue,
				onUpdateDatepicker: function () {
					calendar.querySelectorAll('.ui-datepicker-prev:not(.ui-state-disabled), .ui-datepicker-next:not(.ui-state-disabled)').forEach(function (control) {
						control.tabIndex = 0;
						control.setAttribute('role', 'button');
						control.setAttribute('aria-label', control.title);
					});
					if (restoreFocus) {
						const target = calendar.querySelector(restoreFocus + ':not(.ui-state-disabled)')
							|| calendar.querySelector('.ui-datepicker-calendar a');
						restoreFocus = '';
						if (target) {
							target.focus();
						}
					}
				}
			});
			if (input.value) {
				$calendar.datepicker('setDate', input.value);
				calendar.classList.add('scs-date-calendar--answered');
			}
			clear.disabled = !input.value;
			clear.addEventListener('click', function () {
				$calendar.datepicker('setDate', null);
				updateValue('');
				calendar.querySelector('.ui-datepicker-calendar a')?.focus();
			});
			input.hidden = true;
			picker.hidden = false;
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initCalendars);
	} else {
		initCalendars();
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
			const next = nextButton(form);
			if (next && selected.checked && visible(step) && singleRadioQuestion(step, selected)) {
				next.click();
			}
		}, 400));
	});

	document.addEventListener('keydown', function (event) {
		if (event.key !== 'Enter' || event.defaultPrevented || event.isComposing
			|| event.altKey || event.ctrlKey || event.metaKey || event.shiftKey) {
			return;
		}
		const field = event.target;
		if (!field.matches('input:not([type="hidden"]):not([type="submit"]):not([type="button"]), textarea')) {
			return;
		}
		const form = field.closest('[data-scs-planner] form.forminator-custom-form');
		const step = field.closest('.forminator-pagination');
		const next = form && visible(step) && nextButton(form);
		if (next) {
			event.preventDefault();
			if (!event.repeat) {
				next.click();
			}
		}
	}, true);

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
