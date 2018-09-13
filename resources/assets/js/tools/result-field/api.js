export default {
    fetchData(id) {
        return Nova.request().get('/nova-vendor/lims/sample-data/' + id);
    },
}
