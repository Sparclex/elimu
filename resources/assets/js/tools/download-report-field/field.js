Nova.booting((Vue, router) => {
    Vue.component('index-download-report', require('./components/IndexField'));
    Vue.component('detail-download-report', require('./components/DetailField'));
    Vue.component('form-download-report', require('./components/FormField'));
    Vue.component('download-report-modal', require('./components/DownloadModal'));
})
