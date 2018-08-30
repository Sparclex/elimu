Nova.booting((Vue, router) => {
    Vue.component('index-html-readonly', require('./components/IndexField'));
    Vue.component('detail-html-readonly', require('./components/DetailField'));
    Vue.component('form-html-readonly', require('./components/FormField'));
})
