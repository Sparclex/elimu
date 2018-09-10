<template>
    <div>
        <canvas id="rdml-result" style="width: 100%" height="300"></canvas>
        <pre>{{data}}</pre>
    </div>
</template>
<script>
    import Chart from 'chart.js';
    export default {
        props: ['data'],
        data() {
            return {
                chart: null,
                selectedSampleId: '9181971'
            }
        },
        mounted() {

            this.chart = new Chart(document.getElementById("rdml-result"), {
                type: 'line',
                data: {
                    labels: [],
                    datasets: this.datasets
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });
        },
        computed: {
            datasets() {
                return this.data[this.selectedSampleId];
            },
            labels() {
                return this.data[this.selectedSampleId];
            },
        }
    }
</script>
