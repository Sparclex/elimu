export default {
    fetchAvailableStudies() {
        return Nova.request().get('/nova-vendor/lims/studies');
    },
    importBatchSamples(samples) {
        return Nova.request().post('/nova-vendor/lims/import-samples', {samples});
    }
}
