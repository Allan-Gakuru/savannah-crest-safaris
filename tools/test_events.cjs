const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname, '../wp-content/plugins/scs-core/assets/events.js'), 'utf8');

function simulate(consent) {
	const events = [];
	const handlers = new Map();
	const formHandlers = new Map();
	const pluginHandlers = new Map();
	const planner = {
		dataset: { scsContext: 'trip', scsSlug: 'approved-safari' },
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
console.log('Browser event regression checks passed.');
