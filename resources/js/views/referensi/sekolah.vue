<template>
<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Data Sekolah</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link tag="a" to="/beranda">Beranda</router-link>
                        </li>
                        <li class="breadcrumb-item active">Data Sekolah</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-th mr-1"></i>
                                Data Sekolah
                            </h3>
                            <div class="card-tools" v-show="hasRole('admin')">
                                <button class="btn btn-success btn-sm btn-block btn-flat" v-on:click="newModal">Tambah Data</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <app-datatable :items="items" :fields="fields" :meta="meta" :title="'Hapus Sekolah'" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-labelledby="modalAdd" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Sekolah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="insertData()" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>NPSN</label>
                            <input v-model="form.npsn" type="text" name="npsn" class="form-control" :class="{ 'is-invalid': form.errors.has('npsn') }">
                            <has-error :form="form" field="npsn"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Nama Sekolah</label>
                            <input v-model="form.nama" type="text" name="nama" class="form-control" :class="{ 'is-invalid': form.errors.has('nama') }">
                            <has-error :form="form" field="nama"></has-error>
                        </div>
                        <div class="form-group">
                            <label>NSS</label>
                            <input v-model="form.nss" type="text" name="nss" class="form-control" :class="{ 'is-invalid': form.errors.has('nss') }">
                            <has-error :form="form" field="nss"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <input v-model="form.alamat" type="text" name="alamat" class="form-control" :class="{ 'is-invalid': form.errors.has('alamat') }">
                            <has-error :form="form" field="alamat"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Kecamatan</label>
                            <v-select label="nama" :options="kecamatan" v-model="form.kecamatan_id" @input="getDesa" :class="{ 'is-invalid': form.errors.has('kecamatan_id') }" />
                            <has-error :form="form" field="kecamatan_id"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Desa/Kelurahan</label>
                            <v-select label="nama" :options="desa" v-model="form.desa_id" :class="{ 'is-invalid': form.errors.has('desa_id') }" />
                            <has-error :form="form" field="desa_id"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Kodepos</label>
                            <input v-model="form.kode_pos" type="text" name="kode_pos" class="form-control" :class="{ 'is-invalid': form.errors.has('kode_pos') }">
                            <has-error :form="form" field="kode_pos"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input v-model="form.email" type="email" name="email" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                            <has-error :form="form" field="email"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Website</label>
                            <input v-model="form.website" type="text" name="website" class="form-control" :class="{ 'is-invalid': form.errors.has('website') }">
                            <has-error :form="form" field="website"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Status Sekolah</label>
                            <v-select label="nama" :options="[{nama: 'Negeri', key: 1}, {nama: 'Swasta', key:2}]" v-model="form.status_sekolah" :class="{ 'is-invalid': form.errors.has('status_sekolah') }" />
                            <has-error :form="form" field="status_sekolah"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Nomor Ijin Operasional</label>
                            <input v-model="form.nomor_ijop" type="text" name="nomor_ijop" class="form-control" :class="{ 'is-invalid': form.errors.has('nomor_ijop') }">
                            <has-error :form="form" field="nomor_ijop"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Tahun Ijin Operasional</label><br>
                            <date-picker v-model="form.tahun_ijop" type="year" :class="{ 'is-invalid': form.errors.has('tahun_ijop') }"></date-picker>
                            <has-error :form="form" field="tahun_ijop"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Nama Kepala Sekolah</label>
                            <input v-model="form.nama_kepsek" type="text" name="nama_kepsek" class="form-control" :class="{ 'is-invalid': form.errors.has('nama_kepsek') }">
                            <has-error :form="form" field="nama_kepsek"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Nomor HP Kepala Sekolah</label>
                            <input v-model="form.no_telp" type="text" name="no_telp" class="form-control" :class="{ 'is-invalid': form.errors.has('no_telp') }">
                            <has-error :form="form" field="no_telp"></has-error>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <my-loader />
</div>
</template>

<script>
import Datatable from './../components/Sekolah.vue' //IMPORT COMPONENT DATATABLENYA
import axios from 'axios' //IMPORT AXIOS
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
export default {
    //KETIKA COMPONENT INI DILOAD
    created() {
        //MAKA AKAN MENJALANKAN FUNGSI BERIKUT
        this.loadPostsData()
    },
    data() {
        return {
            user: user,
            fields: [{
                    key: 'nama',
                    label: 'Nama Sekolah',
                    sortable: true
                },
                {
                    key: 'npsn',
                    label: 'NPSN',
                    sortable: true
                },
                {
                    key: 'alamat',
                    label: 'Alamat',
                    sortable: true
                },
                {
                    key: 'desa_kelurahan',
                    label: 'Desa/Kelurahan',
                    sortable: false
                },
                {
                    key: 'kecamatan',
                    label: 'Kecamatan',
                    sortable: true
                },
                {
                    key: 'actions',
                    label: 'Aksi',
                    sortable: false
                }, //TAMBAHKAN CODE INI
            ],
            items: [], //DEFAULT VALUE DARI ITEMS ADALAH KOSONG
            meta: [], //JUGA BERLAKU UNTUK META
            current_page: 1, //DEFAULT PAGE YANG AKTIF ADA PAGE 1
            per_page: 10, //DEFAULT LOAD PERPAGE ADALAH 10
            search: '',
            sortBy: 'created_at', //DEFAULT SORTNYA ADALAH CREATED_AT
            sortByDesc: true, //ASCEDING
            sekolah_id: user.sekolah_id,
            form: new Form({
                npsn: '',
                nama: '',
                nss: '',
                alamat: '',
                kode_pos: '',
                email: '',
                website: '',
                status_sekolah: '',
                nama_kepsek: '',
                no_telp: '',
                kecamatan_id: '',
                desa_id: '',
                nomor_ijop: '',
                tahun_ijop: '',
            }),
            kecamatan: [],
            desa: [],
            checkResetDB: null,
        }
    },
    components: {
        DatePicker,
        'app-datatable': Datatable //REGISTER COMPONENT DATATABLE
    },
    methods: {
        newModal() {
            this.editmode = false;
            this.form.reset();
            this.getKecamatan();
            $('#modalAdd').modal('show');
        },
        getKecamatan(){
            axios.get(`/api/referensi/kecamatan`)
            .then((response) => {
                this.kecamatan = response.data.data
            });
        },
        getDesa(val){
            this.desa_id = {nama: '== Pilih Desa/Kelurahan ==', kode_wilayah: ''}
            axios.get(`/api/referensi/desa`, {
                params: {
                    kecamatan_id: val.kode_wilayah.trim()
                }
            })
            .then((response) => {
                this.desa = response.data.data
            });
        },
        loadPostsData() {
            let current_page = this.search == '' ? this.current_page : 1
            //LAKUKAN REQUEST KE API UNTUK MENGAMBIL DATA POSTINGAN
            axios.get(`/api/referensi/sekolah`, {
                    //KIRIMKAN PARAMETER BERUPA PAGE YANG SEDANG DILOAD, PENCARIAN, LOAD PERPAGE DAN SORTING.
                    params: {
                        sekolah_id: this.sekolah_id,
                        verifikasi_id: user.verifikator_id,
                        page: current_page,
                        per_page: this.per_page,
                        q: this.search,
                        sortby: this.sortBy,
                        sortbydesc: this.sortByDesc ? 'DESC' : 'ASC'
                    }
                })
                .then((response) => {
                    //JIKA RESPONSENYA DITERIMA
                    let getData = response.data.data
                    this.items = getData.data //MAKA ASSIGN DATA POSTINGAN KE DALAM VARIABLE ITEMS
                    //DAN ASSIGN INFORMASI LAINNYA KE DALAM VARIABLE META
                    this.meta = {
                        total: getData.total,
                        current_page: getData.current_page,
                        per_page: getData.per_page,
                        from: getData.from,
                        to: getData.to,
                        isBusy: false,
                    }
                    console.log(getData.data)
                })
        },
        //JIKA ADA EMIT TERKAIT LOAD PERPAGE, MAKA FUNGSI INI AKAN DIJALANKAN
        handlePerPage(val) {
            this.per_page = val //SET PER_PAGE DENGAN VALUE YANG DIKIRIM DARI EMIT
            this.loadPostsData() //DAN REQUEST DATA BARU KE SERVER
        },
        //JIKA ADA EMIT PAGINATION YANG DIKIRIM, MAKA FUNGSI INI AKAN DIEKSEKUSI
        handlePagination(val) {
            this.current_page = val //SET CURRENT PAGE YANG AKTIF
            this.loadPostsData()
        },
        //JIKA ADA DATA PENCARIAN
        handleSearch(val) {
            this.search = val //SET VALUE PENCARIAN KE VARIABLE SEARCH
            this.loadPostsData() //REQUEST DATA BARU
        },
        //JIKA ADA EMIT SORT
        handleSort(val) {
            if (val.sortBy) {
                this.sortBy = val.sortBy
                this.sortByDesc = val.sortDesc

                this.loadPostsData() //DAN LOAD DATA BARU BERDASARKAN SORT
            }
        },
        insertData() {
            this.form.post('/api/referensi/simpan-sekolah').then((response) => {
                //console.log(response);
                $('#modalAdd').modal('hide');
                Toast.fire({
                    icon: 'success',
                    title: response.message
                });
                this.loadPostsData();
            }).catch((e) => {
                Toast.fire({
                    icon: 'error',
                    title: 'Some error occured! Please try again'
                });
            })
        },
    }
}
</script>
