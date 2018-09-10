export default {
    fetchAvailableStudies() {
        return Nova.request().get('/nova-vendor/lims/studies');
    },
    importBatchSamples(study, samples) {
        return Nova.request().post('/nova-vendor/lims/import-samples', {study, samples});
    }
}
