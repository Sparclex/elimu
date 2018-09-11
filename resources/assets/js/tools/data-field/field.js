Nova.booting((Vue, router) => {
    Vue.component('index-data', require('./components/IndexField'));
    Vue.component('detail-data', require('./components/DetailField'));
    Vue.component('form-data', require('./components/FormField'));
})
