export default {
    fetchAvailableStudies() {
        return Nova.request().get('/nova-vendor/sparclex/Lims/studies');
    },
    importBatchSamples(samples) {
        return Nova.request().post('/nova-vendor/sparclex/Lims/import-samples', {samples});
    }
}
