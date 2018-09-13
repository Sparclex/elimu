<template>
    <div v-if="displayData">
        <canvas id="rdml-result" style="width: 100%" height="300"></canvas>
    </div>
</template>
<script>
    import Chart from 'chart.js';

    export default {
        props: ['displayData'],
        data() {
            return {
                chart: null,
                selectedSampleId: '9181971'
            }
        },
        mounted() {
            console.log(this.displayData);
            this.chart = new Chart(document.getElementById("rdml-result"), {
                type: 'line',
                data: {
                    labels: Array.from(Array(this.displayData[0].data.length).keys()),
                    datasets: this.displayData
                },
                options: {
                    scales: {
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: 'Fluor'
                            }
                        }],
                        xAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: 'Cycles'
                            }
                        }]
                    }
                }
            });
        }
    }
</script>
