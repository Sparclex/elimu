Nova.booting((Vue, router) => {
    Vue.component('index-reagent-field', require('./components/IndexField'));
    Vue.component('detail-reagent-field', require('./components/DetailField'));
    Vue.component('form-reagent-field', require('./components/FormField'));
})
