import './core/reactivity.js';
import './core/dom.js';
import './components/dropdown.js';
import './components/modal.js';
import './components/tabs.js';

// Store system
window.Cyron.store = {
    _state: {},
    _listeners: new Map(),
    
    set(key, value) {
        this._state[key] = value;
        if (this._listeners.has(key)) {
            this._listeners.get(key).forEach(cb => cb(value));
        }
    },
    
    get(key) {
        return this._state[key];
    },
    
    watch(key, callback) {
        if (!this._listeners.has(key)) {
            this._listeners.set(key, []);
        }
        this._listeners.get(key).push(callback);
    }
};

// Form helper
window.Cyron.form = {
    validate(formId, rules) {
        const form = document.getElementById(formId);
        const errors = {};
        
        Object.keys(rules).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            const value = input.value;
            const fieldRules = rules[field];
            
            fieldRules.forEach(rule => {
                if (rule === 'required' && !value) {
                    errors[field] = 'This field is required';
                }
            });
        });
        
        return errors;
    }
};

// HTTP helper
window.Cyron.http = {
    async get(url) {
        const response = await fetch(url);
        return response.json();
    },
    
    async post(url, data) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        return response.json();
    }
};

console.log('Cyron Framework loaded successfully!');