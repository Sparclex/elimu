Nova.booting((Vue, router) => {
    Vue.component('csv-result', require('./components/CsvResult'));
    Vue.component('rdml-result', require('./components/RdmlResult'));
    Vue.component('index-result', require('./components/IndexField'));
    Vue.component('detail-result', require('./components/DetailField'));
    Vue.component('form-result', require('./components/FormField'));
})
