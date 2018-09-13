Nova.booting((Vue, router) => {
    Vue.component('index-additional-data', require('./components/IndexField'));
    Vue.component('detail-additional-data', require('./components/DetailField'));
    Vue.component('form-additional-data', require('./components/FormField'));
})
