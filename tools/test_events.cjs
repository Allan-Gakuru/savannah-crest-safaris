const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname, '../wp-content/plugins/scs-core/assets/events.js'), 'utf8');

function simulate(consent, dataset = { scsContext: 'trip', scsSlug: 'approved-safari' }) {
	const events = [];
	const handlers = new Map();
	const formHandlers = new Map();
	const pluginHandlers = new Map();
	const planner = {
		dataset,
		addEventListener: (name, callback) => formHandlers.set(name, callback)
	};
	const globals = {
		CustomEvent: class {
			constructor(type, settings) {
				this.type = type;
				this.detail = settings.detail;
			}
		},
		window: {
			wp_has_consent: () => consent,
			jQuery: () => ({ on: (name, selector, callback) => pluginHandlers.set(name, callback) }),
			requestAnimationFrame: (callback) => callback(),
			getComputedStyle: () => ({ visibility: 'visible' })
		},
		document: {
			querySelector: (selector) => selector === '[data-scs-planner]' ? planner : null,
			addEventListener: (name, callback) => handlers.set(name, callback),
			dispatchEvent: (event) => events.push(event.detail)
		}
	};
	vm.runInNewContext(source, globals);
	formHandlers.get('focusin')({ target: { closest: () => true } });
	formHandlers.get('input')({ target: { closest: () => true } });
	const form = { querySelectorAll: () => [{}, { getClientRects: () => [1] }] };
	pluginHandlers.get('forminator.front.pagination.move').call(form);
	pluginHandlers.get('forminator.front.pagination.move').call(form);
	pluginHandlers.get('forminator:form:submit:success').call(form, null, { email: 'private@example.test' });
	pluginHandlers.get('forminator:form:submit:success').call(form, null, { email: 'private@example.test' });
	return JSON.parse(JSON.stringify(events));
}

assert.deepEqual(simulate(false), []);
assert.deepEqual(simulate(true), [
	{ event: 'enquiry_start', context: 'trip', trip: 'approved-safari' },
	{ event: 'enquiry_step_complete', context: 'trip', trip: 'approved-safari', step: 1 },
	{ event: 'enquiry_submit_success', context: 'trip', trip: 'approved-safari', surface: 'planner' }
]);
assert.deepEqual(simulate(true, { scsContext: 'general', scsSurface: 'enquiry' }).pop(), {
	event: 'enquiry_submit_success', context: 'general', surface: 'enquiry'
});

const enquiry = fs.readFileSync(path.join(__dirname, '../wp-content/plugins/scs-core/assets/enquiry.js'), 'utf8');
const location = { hash: '' };
const focus = [];
const step = (name) => ({
	querySelector: (selector) => selector === '[tabindex="-1"]' ? { focus: () => focus.push(name) } : null,
	scrollIntoView: () => {}
});
const introduction = step('intro');
const details = step('details');
let hashChange;
vm.runInNewContext(enquiry, {
	document: { querySelector: () => ({ querySelector: (selector) => selector === '#enquiry-start' ? introduction : details }) },
	location,
	window: { addEventListener: (event, callback) => { if (event === 'hashchange') hashChange = callback; } }
});
assert.deepEqual([introduction.hidden, details.hidden], [false, true]);
location.hash = '#enquiry-details';
hashChange();
assert.deepEqual([introduction.hidden, details.hidden], [true, false]);
location.hash = '#enquiry-start';
hashChange();
assert.deepEqual([introduction.hidden, details.hidden], [false, true]);
assert.deepEqual(focus, ['details', 'intro']);
console.log('Browser event regression checks passed.');
