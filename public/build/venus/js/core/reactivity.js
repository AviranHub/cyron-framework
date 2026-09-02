// Cyron Reactive System
class CyronReactive {
    constructor(data) {
        this._data = data;
        this._watchers = new Map();
        this._listeners = new Map();
        this._proxy = this._createProxy(data);
        return this._proxy;
    }
    
    _createProxy(obj, path = '') {
        const self = this;
        
        return new Proxy(obj, {
            get(target, key) {
                const value = target[key];
                const newPath = path ? `${path}.${key}` : key;
                
                // Track dependency
                if (typeof value === 'object' && value !== null) {
                    return self._createProxy(value, newPath);
                }
                
                return value;
            },
            
            set(target, key, value) {
                const oldValue = target[key];
                target[key] = value;
                const newPath = path ? `${path}.${key}` : key;
                
                // Notify watchers
                if (self._watchers.has(newPath)) {
                    self._watchers.get(newPath).forEach(callback => {
                        callback(value, oldValue);
                    });
                }
                
                // Notify listeners
                self._trigger(key, value);
                
                return true;
            }
        });
    }
    
    watch(path, callback) {
        if (!this._watchers.has(path)) {
            this._watchers.set(path, []);
        }
        this._watchers.get(path).push(callback);
    }
    
    on(event, callback) {
        if (!this._listeners.has(event)) {
            this._listeners.set(event, []);
        }
        this._listeners.get(event).push(callback);
    }
    
    _trigger(event, data) {
        if (this._listeners.has(event)) {
            this._listeners.get(event).forEach(callback => callback(data));
        }
    }
}

// Cyron Component System
class CyronComponent {
    constructor(config) {
        this._data = config.data || {};
        this._methods = config.methods || {};
        this._mounted = config.mounted;
        this._el = null;
        this._reactive = new CyronReactive(this._data);
        
        // Bind methods to this instance
        Object.keys(this._methods).forEach(key => {
            this._methods[key] = this._methods[key].bind(this);
        });
        
        return this;
    }
    
    mount(selector) {
        this._el = document.querySelector(selector);
        if (!this._el) return;
        
        // Process directives
        this._processDirectives(this._el);
        
        // Call mounted hook
        if (this._mounted) {
            this._mounted.call(this);
        }
        
        return this;
    }
    
    _processDirectives(element) {
        const attributes = element.attributes;
        
        Array.from(attributes).forEach(attr => {
            if (attr.name.startsWith('c-')) {
                const directive = attr.name.slice(2);
                const expression = attr.value;
                
                this._handleDirective(element, directive, expression);
            }
        });
        
        // Process children
        Array.from(element.children).forEach(child => {
            this._processDirectives(child);
        });
    }
    
    _handleDirective(element, directive, expression) {
        switch(directive) {
            case 'text':
                this._handleText(element, expression);
                break;
            case 'show':
                this._handleShow(element, expression);
                break;
            case 'hide':
                this._handleHide(element, expression);
                break;
            case 'click':
                this._handleClick(element, expression);
                break;
            case 'model':
                this._handleModel(element, expression);
                break;
            case 'for':
                this._handleFor(element, expression);
                break;
            case 'if':
                this._handleIf(element, expression);
                break;
            case 'html':
                this._handleHtml(element, expression);
                break;
        }
    }
    
    _handleText(element, expression) {
        const value = this._evaluate(expression);
        element.textContent = value;
        
        // Watch for changes
        this._reactive.watch(expression, (newValue) => {
            element.textContent = newValue;
        });
    }
    
    _handleShow(element, expression) {
        const value = this._evaluate(expression);
        element.style.display = value ? '' : 'none';
        
        this._reactive.watch(expression, (newValue) => {
            element.style.display = newValue ? '' : 'none';
        });
    }
    
    _handleHide(element, expression) {
        const value = this._evaluate(expression);
        element.style.display = value ? 'none' : '';
        
        this._reactive.watch(expression, (newValue) => {
            element.style.display = newValue ? 'none' : '';
        });
    }
    
    _handleClick(element, expression) {
        element.addEventListener('click', (e) => {
            const method = this._methods[expression];
            if (method) {
                method(e);
            } else {
                this._evaluate(expression);
            }
        });
    }
    
    _handleModel(element, expression) {
        const value = this._evaluate(expression);
        element.value = value;
        
        // Update on input
        element.addEventListener('input', (e) => {
            this._setValue(expression, e.target.value);
        });
        
        // Watch for external changes
        this._reactive.watch(expression, (newValue) => {
            element.value = newValue;
        });
    }
    
    _handleFor(element, expression) {
        // Parse: item in items
        const match = expression.match(/(\w+)\s+in\s+(\w+)/);
        if (!match) return;
        
        const itemName = match[1];
        const itemsPath = match[2];
        const items = this._evaluate(itemsPath);
        const parent = element.parentElement;
        const template = element.cloneNode(true);
        
        element.remove();
        
        items.forEach((item, index) => {
            const clone = template.cloneNode(true);
            clone.removeAttribute('c-for');
            
            // Create local scope
            const scope = { [itemName]: item, index: index };
            
            // Process directives with scope
            this._processWithScope(clone, scope);
            
            parent.appendChild(clone);
        });
    }
    
    _processWithScope(element, scope) {
        // Similar to _processDirectives but with scope
        // ... implementation
    }
    
    _handleIf(element, expression) {
        const value = this._evaluate(expression);
        if (!value) {
            element.remove();
        }
    }
    
    _handleHtml(element, expression) {
        const value = this._evaluate(expression);
        element.innerHTML = value;
        
        this._reactive.watch(expression, (newValue) => {
            element.innerHTML = newValue;
        });
    }
    
    _evaluate(expression) {
        try {
            // Simple evaluation
            const keys = expression.split('.');
            let value = this._reactive;
            
            for (let key of keys) {
                value = value[key];
                if (value === undefined) break;
            }
            
            return value;
        } catch(e) {
            console.error('Evaluation error:', e);
            return null;
        }
    }
    
    _setValue(path, value) {
        const keys = path.split('.');
        let target = this._reactive;
        
        for (let i = 0; i < keys.length - 1; i++) {
            target = target[keys[i]];
        }
        
        target[keys[keys.length - 1]] = value;
    }
}

// Global Cyron object
window.Cyron = {
    reactive: (data) => new CyronReactive(data),
    component: (config) => new CyronComponent(config),
    createApp: (config) => {
        const app = new CyronComponent(config);
        return {
            mount: (selector) => app.mount(selector)
        };
    }
};

// Auto-mount components with c-app directive
document.addEventListener('DOMContentLoaded', () => {
    const appElements = document.querySelectorAll('[c-app]');
    appElements.forEach(el => {
        const config = {
            data: JSON.parse(el.getAttribute('c-data') || '{}')
        };
        Cyron.createApp(config).mount(`[c-app="${el.getAttribute('c-app')}"]`);
    });
});