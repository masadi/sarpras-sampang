<template>
<div class="no">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 text-dark">Beranda</h1>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="col-12 row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{jml_sekolah}}</h3>
                        <p>Sekolah</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{jml_bangunan}}</h3>
                        <p>Bangunan</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{jml_ruang}}</h3>
                        <p>Ruang</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{jml_alat_dll}}</h3>
                        <p>Alat, Angkutan & Buku</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <div class="col-12 row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="text-lg text-center">Kondisi Bangunan</div>
                                    <div class="chartdiv" id="chartBangunan" ref="chartBangunan" style="width: 100%;height: 500px;"></div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="text-lg text-center">Kondisi Ruang</div>
                                    <div class="chartdiv" id="chartRuang" ref="chartRuang" style="width: 100%;height: 500px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
        </div>
        </section>
    <my-loader />
</div>
</template>

<script>
/* Imports */
import * as am4core from "@amcharts/amcharts4/core";
import * as am4charts from "@amcharts/amcharts4/charts";
import am4themes_material from "@amcharts/amcharts4/themes/material";
import am4themes_animated from "@amcharts/amcharts4/themes/animated";
import axios from 'axios' //IMPORT AXIOS
export default {
    //KETIKA COMPONENT INI DILOAD
    created() {
        //MAKA AKAN MENJALANKAN FUNGSI BERIKUT
        this.loadPostsData()
    },
    data() {
        return {
            user: user,
            jml_sekolah: 0,
            jml_bangunan: 0,
            jml_ruang: 0,
            jml_alat_dll: 0,
        }
    },
    //mounted() {
    //this.createChart('kemajuan', this.planetChartData);
    //},
    methods: {
        createChart(chartID, chartData) {
            /* Chart code */
            // Themes begin
            am4core.useTheme(am4themes_material);
            am4core.useTheme(am4themes_animated);
            // Themes end

            let chart = am4core.create(chartID, am4charts.PieChart3D);
            chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

            chart.legend = new am4charts.Legend();
            console.log(chartData);
            chart.data = [
                {
                    category: "Baik",
                    value: chartData.baik
                },
                {
                    category: "Rusak Ringan",
                    value: chartData.ringan
                },
                {
                    category: "Rusak Sedang",
                    value: chartData.sedang
                },
                {
                    category: "Rusak Berat",
                    value: chartData.berat
                }
            ];

            let series = chart.series.push(new am4charts.PieSeries3D());
            series.dataFields.value = "value";
            series.dataFields.category = "category";
        },
        loadPostsData() {
            axios.post(`/api/dashboard`, {
                    user_id: user.user_id,
                })
                .then((response) => {
                    this.no_coe = 'Penjaminan Mutu Tahun 2021 belum dibuka'//'Sekolah Anda belum ditetapkan sebagai SMK Center of Excelent'
                    this.loading = false
                    let getData = response.data.data
                    if (!getData) {
                        return false
                    }
                    this.jml_sekolah = getData.jml_sekolah
                    this.jml_bangunan = getData.jml_bangunan
                    this.jml_ruang = getData.jml_ruang
                    this.jml_alat_dll = getData.jml_alat_dll
                    let vm = this
                    vm.createChart('chartBangunan', getData.kondisi_bangunan)
                    vm.createChart('chartRuang', getData.kondisi_ruang)
                })
        },
    },
}
</script>
