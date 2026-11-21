(function () {
	'use strict';

	const flow = document.querySelector('[data-scs-enquiry-flow]');
	if (!flow) {
		return;
	}
	const introduction = flow.querySelector('#enquiry-start');
	const details = flow.querySelector('#enquiry-details');
	if (!introduction || !details) {
		return;
	}

	function showStep(moveFocus) {
		const response = details.querySelector('.forminator-response-message[aria-hidden="false"], .forminator-response-message.forminator-show');
		const showDetails = location.hash === '#enquiry-details' || (!location.hash && Boolean(response && response.textContent.trim()));
		introduction.hidden = showDetails;
		details.hidden = !showDetails;
		if (moveFocus) {
			const active = showDetails ? details : introduction;
			active.scrollIntoView({ block: 'start' });
			active.querySelector('[tabindex="-1"]').focus({ preventScroll: true });
		}
	}

	showStep(false);
	window.addEventListener('hashchange', function () {
		showStep(true);
	});
})();
