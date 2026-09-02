import Alpine from 'alpinejs';

window.Alpine = Alpine;

// تعریف کامپوننت‌های Alpine (مثل searchBox)
document.addEventListener('alpine:init', () => {
    Alpine.data('searchBox', () => ({
        search: '',
        recentSearches: [],
        focused: false,
        init() {
            let stored = localStorage.getItem('recentSearches');
            this.recentSearches = stored ? JSON.parse(stored) : [];
        },
        saveSearch() {
            if (!this.search.trim()) return;
            let updated = [this.search, ...this.recentSearches.filter(s => s !== this.search)].slice(0, 8);
            this.recentSearches = updated;
            localStorage.setItem('recentSearches', JSON.stringify(updated));
            this.$el.submit();
        },
        fillSearch(item) {
            this.search = item;
            this.focused = false;
            this.$el.querySelector('input[name="query"]').focus();
            this.$dispatch('close-search-modal');
            this.$el.closest('form').submit()
        }
    }));
});

Alpine.start();