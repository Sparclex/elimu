Nova.booting((Vue, router) => {
    Vue.component('index-select-has-many', require('./components/IndexField'));
    Vue.component('detail-select-has-many', require('./components/DetailField'));
    Vue.component('form-select-has-many', require('./components/FormField'));
})
