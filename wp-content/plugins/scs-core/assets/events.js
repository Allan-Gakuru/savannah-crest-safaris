(function () {
	'use strict';

	const planner = document.querySelector('[data-scs-planner]');
	const finished = new WeakSet();
	const firstSteps = new WeakSet();
	let started = false;

	function emit(name, detail) {
		if (typeof window.wp_has_consent !== 'function' || !window.wp_has_consent('statistics')) {
			return;
		}
		document.dispatchEvent(new CustomEvent('scs:analytics', { detail: { event: name, ...detail } }));
	}

	const trip = document.querySelector('[data-scs-trip]');
	if (trip && trip.dataset.scsTrip) {
		emit('safari_view', { trip: trip.dataset.scsTrip });
	}

	document.addEventListener('submit', function (event) {
		const form = event.target;
		if (!form.classList.contains('scs-filter-form')) {
			return;
		}
		const selections = {};
		['where', 'experience', 'duration'].forEach(function (key) {
			const field = form.elements.namedItem(key);
			if (field && field.value) {
				selections[key] = field.value;
			}
		});
		emit('safari_filter_apply', selections);
	});

	document.addEventListener('click', function (event) {
		const action = event.target instanceof Element ? event.target.closest('[data-scs-enquiry]') : null;
		if (action) {
			emit('enquiry_cta_click', { surface: action.dataset.scsEnquiry, ...(trip ? { trip: trip.dataset.scsTrip } : {}) });
		}
	});

	if (!planner) {
		return;
	}

	const context = { context: planner.dataset.scsContext, ...(planner.dataset.scsContext === 'trip' ? { trip: planner.dataset.scsSlug } : {}) };
	function start(event) {
		if (!started && event.target.closest('.forminator-custom-form')) {
			started = true;
			emit('enquiry_start', context);
		}
	}
	planner.addEventListener('focusin', start);
	planner.addEventListener('input', start);

	if (window.jQuery) {
		const $planner = window.jQuery(planner);
		$planner.on('forminator.front.pagination.move', 'form', function () {
			const form = this;
			window.requestAnimationFrame(function () {
				const steps = form.querySelectorAll('.forminator-pagination');
				if (!firstSteps.has(form) && steps[1] && steps[1].getClientRects().length
					&& window.getComputedStyle(steps[1]).visibility !== 'hidden') {
					firstSteps.add(form);
					emit('enquiry_step_complete', { ...context, step: 1 });
				}
			});
		});
		$planner.on('forminator:form:submit:success', 'form', function () {
			if (finished.has(this)) {
				return;
			}
			finished.add(this);
			emit('enquiry_submit_success', { ...context, surface: 'planner' });
		});
	}
})();
