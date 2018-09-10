Nova.booting((Vue, router) => {
    Vue.component('index-status', require('./components/IndexField'));
    Vue.component('detail-status', require('./components/DetailField'));
    Vue.component('form-status', require('./components/FormField'));
})
