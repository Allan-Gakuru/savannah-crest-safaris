const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname, '../wp-content/plugins/scs-core/assets/form-flow.js'), 'utf8');
const listeners = {};
const timers = new Map();
let timerId = 0;
class Input {}
vm.runInNewContext(source, {
	HTMLInputElement: Input,
	document: { addEventListener: (name, callback) => { listeners[name] = callback; } },
	window: {
		setTimeout: (callback) => { timers.set(++timerId, callback); return timerId; },
		clearTimeout: (id) => timers.delete(id),
		getComputedStyle: (element) => ({ visibility: element.visibility || 'visible' })
	}
});
function flush() {
	const callbacks = [...timers.values()];
	timers.clear();
	callbacks.forEach((callback) => callback());
}
function fixture() {
	let clicks = 0;
	const rects = () => [1];
	const next = { disabled: false, getClientRects: rects, classList: { contains: () => false }, click: () => { clicks++; } };
	const step = { fields: [], getClientRects: rects, querySelectorAll() { return this.fields; } };
	const form = { closest: () => ({}), querySelector: () => next };
	function input(type, name, isVisible = true) {
		const field = Object.assign(new Input(), {
			type, name, checked: true, disabled: false,
			matches: () => !['hidden', 'submit', 'button'].includes(type),
			closest: (selector) => ({
				'form.forminator-custom-form': form,
				'[data-scs-planner] form.forminator-custom-form': form,
				'.forminator-pagination': step,
				'.forminator-field': { getClientRects: () => isVisible ? [1] : [] }
			})[selector]
		});
		step.fields.push(field);
		return field;
	}
	return { next, step, form, input, clicks: () => clicks };
}
function change(field) { listeners.change({ target: field }); }
function enter(field, overrides = {}) {
	const event = { key: 'Enter', target: field, defaultPrevented: false,
		preventDefault() { this.defaultPrevented = true; }, ...overrides };
	listeners.keydown(event);
	return event.defaultPrevented;
}

{
	const f = fixture();
	const selected = f.input('radio', 'radio-1');
	f.input('radio', 'radio-1');
	change(selected); flush();
	assert.equal(f.clicks(), 1, 'a single-choice answer advances');
}
{
	const f = fixture();
	change(f.input('checkbox', 'checkbox-1')); flush();
	assert.equal(f.clicks(), 0, 'multiple-choice answers do not advance');
}
{
	const f = fixture();
	const selected = f.input('radio', 'radio-1');
	f.input('text', 'other');
	change(selected); flush();
	assert.equal(f.clicks(), 0, 'a visible additional answer prevents skipping');
}
{
	const f = fixture();
	const selected = f.input('radio', 'radio-1');
	f.input('text', 'other', false);
	change(selected); flush();
	assert.equal(f.clicks(), 1, 'hidden conditional fields do not prevent advancing');
}
{
	const f = fixture();
	const selected = f.input('radio', 'radio-1');
	f.input('radio', 'radio-2');
	change(selected); flush();
	assert.equal(f.clicks(), 0, 'separate radio questions cannot be skipped');
}
{
	const f = fixture();
	const selected = f.input('radio', 'radio-1');
	change(selected);
	listeners.click({ target: { closest: () => ({ closest: () => f.form }) } });
	flush();
	assert.equal(f.clicks(), 0, 'manual navigation cancels a pending advance');
}
{
	const f = fixture();
	const selected = f.input('radio', 'radio-1');
	change(selected);
	f.step.getClientRects = () => [];
	flush();
	assert.equal(f.clicks(), 0, 'a step already left cannot advance again');
}
{
	const f = fixture();
	const selected = f.input('radio', 'radio-1');
	f.form.querySelector = () => null;
	change(selected); flush();
	assert.equal(f.clicks(), 0, 'the final step is never submitted automatically');
}
{
	const f = fixture();
	const selected = f.input('radio', 'radio-1');
	f.next.disabled = true;
	change(selected); flush();
	assert.equal(f.clicks(), 0, 'a disabled Next control is respected');
}
{
	const f = fixture();
	const field = f.input('email', 'email-1');
	assert.equal(enter(field), true, 'Enter overrides implicit form submission when Next is available');
	assert.equal(f.clicks(), 1, 'Enter clicks the same Next button as a pointer');
	assert.equal(enter(field, { repeat: true }), true, 'holding Enter suppresses the browser default');
	assert.equal(f.clicks(), 1, 'holding Enter cannot skip steps');
}
{
	const f = fixture();
	const field = f.input('text', 'text-1');
	assert.equal(enter(field, { isComposing: true }), false, 'Enter can finish text composition');
	assert.equal(enter(field, { shiftKey: true }), false, 'modified Enter retains its native behavior');
	f.next.classList.contains = () => true;
	assert.equal(enter(field), false, 'Enter does not click a hidden Next button');
	assert.equal(f.clicks(), 0);
}
console.log('Form keyboard and auto-advance regression checks passed.');
