<template>
<div>
    <div class="row">
        <!-- BLOCK INI AKAN MENGHANDLE LOAD DATA PERPAGE, DENGAN DEFAULT ADALAH 10 DATA -->
        <div class="col-md-4 mb-2">
            <div class="form-inline">
                <!-- KETIKA SELECT BOXNYA DIGANTI, MAKA AKAN MENJALANKAN FUNGSI loadPerPage -->
                <select class="form-control" v-model="meta.per_page" @change="loadPerPage">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <label class="ml-2">Entri</label>
            </div>
        </div>

        <!-- BLOCK INI AKAN MENG-HANDLE PENCARIAN DATA -->
        <div class="col-md-4 offset-md-4">
            <div class="form-inline float-right">
                <label class="mr-2">Cari</label>
                <!-- KETIKA ADA INPUTAN PADA KOLOM PENCARIAN, MAKA AKAN MENJALANKAN FUNGSI SEARCH -->
                <input type="text" class="form-control" @input="search">
            </div>
        </div>
    </div>
    <!-- BLOCK INI AKAN MENGHASILKAN LIST DATA DALAM BENTUK TABLE MENGGUNAKAN COMPONENT TABLE DARI BOOTSTRAP VUE -->

    <!-- :ITEMS ADALAH DATA YANG AKAN DITAMPILKAN -->
    <!-- :FIELDS AKAN MENJADI HEADER DARI TABLE, MAKA BERISI FIELD YANG SALING BERKORELASI DENGAN ITEMS -->
    <!-- :sort-by.sync & :sort-desc.sync AKAN MENGHANDLE FITUR SORTING -->
    <b-table striped hover :items="items" :fields="fields" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" show-empty>
        <template v-slot:cell(jml_sekolah)="row">
            {{row.item.sekolah_sasaran_count}}
        </template>
        <template v-slot:cell(actions)="row">
            <b-dropdown id="dropdown-dropleft" dropleft text="Aksi" size="sm" variant="success">
                <b-dropdown-item href="javascript:" @click="openShowModal(row)"><i class="fas fa-eye"></i> Detil</b-dropdown-item>
                <b-dropdown-item href="javascript:" @click="editData(row)"><i class="fas fa-edit"></i> Edit</b-dropdown-item>
                <b-dropdown-item href="javascript:" @click="deleteData(row.item.buku_id)"><i class="fas fa-trash"></i> Hapus</b-dropdown-item>
            </b-dropdown>
        </template>
    </b-table>

    <!-- BAGIAN INI AKAN MENAMPILKAN JUMLAH DATA YANG DI-LOAD -->
    <div class="row">
        <div class="col-md-6">
            <p>Menampilkan {{ meta.from }} sampai {{ meta.to }} dari {{ meta.total }} entri</p>
        </div>

        <!-- BLOCK INI AKAN MENJADI PAGINATION DARI DATA YANG DITAMPILKAN -->
        <div class="col-md-6">
            <!-- DAN KETIKA TERJADI PERGANTIAN PAGE, MAKA AKAN MENJALANKAN FUNGSI changePage -->
            <b-pagination v-model="meta.current_page" :total-rows="meta.total" :per-page="meta.per_page" align="right" @change="changePage" aria-controls="dw-datatable"></b-pagination>
        </div>
    </div>
    <b-modal id="modal-lg" size="lg" v-model="showModal" title="Detil Buku">
        <table class="table">
            <tr>
                <td width="20%">Sekolah</td>
                <td width="80%">: {{modalText.sekolah.nama}}</td>
            </tr>
            <tr>
                <td>Mata Pelajaran</td>
                <td>: {{modalText.mata_pelajaran.nama}}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>: {{modalText.kelas}}</td>
            </tr>
            <tr>
                <td>Kode</td>
                <td>: {{modalText.kode}}</td>
            </tr>
            <tr>
                <td>Judul</td>
                <td>: {{modalText.judul}}</td>
            </tr>
            <tr>
                <td>Nama Penerbit</td>
                <td>: {{modalText.nama_penerbit}}</td>
            </tr>
            <tr>
                <td>ISBN/ISSN</td>
                <td>: {{modalText.isbn_issn}}</td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>: {{modalText.keterangan}}</td>
            </tr>
        </table>
        <template v-slot:modal-footer>
            <div class="w-100 float-right">
                <b-button variant="secondary" size="sm" @click="showModal=false">
                    Tutup
                </b-button>
            </div>
        </template>
    </b-modal>
    <b-modal ref="editModal" size="lg" title="Edit Data Buku">
        <div class="form-group">
            <input v-model="form.id" type="hidden" name="id" class="form-control" :class="{ 'is-invalid': form.errors.has('id') }">
            <label>Sekolah</label>
            <v-select label="nama" :options="data_sekolah" v-model="form.sekolah_id" />
            
        </div>
        <div class="form-group">
            <label>Kelas</label>
            <v-select label="nama" :options="data_kelas" v-model="form.kelas" @input="updateMapel" />
            
        </div>
        <div class="form-group">
            <label>Mata Pelajaran</label>
            <v-select label="nama" :options="data_mapel" v-model="form.mata_pelajaran_id" />
            
        </div>
        <div class="form-group">
            <label>Kode Buku</label>
            <input v-model="form.kode" type="text" name="kode" class="form-control" :class="{ 'is-invalid': form.errors.has('kode') }">
            
        </div>
        <div class="form-group">
            <label>Judul Buku</label>
            <input v-model="form.judul" type="text" name="judul" class="form-control" :class="{ 'is-invalid': form.errors.has('judul') }">
            
        </div>
        <div class="form-group">
            <label>Nama Penerbit</label>
            <v-select label="nama" :options="data_penerbit" v-model="form.nama_penerbit" />
            
        </div>
        <div class="form-group">
            <label>ISBN/ISSN</label>
            <input v-model="form.isbn_issn" type="text" name="isbn_issn" class="form-control" :class="{ 'is-invalid': form.errors.has('isbn_issn') }">
            
        </div>
        <div class="form-group">
            <label>Keterangan</label>
            <input v-model="form.keterangan" type="text" name="keterangan" class="form-control" :class="{ 'is-invalid': form.errors.has('keterangan') }">
            
        </div>
        <template v-slot:modal-footer>
            <div class="w-100 float-right">
                <b-button variant="secondary" size="sm" @click="hideModal">
                    Tutup
                </b-button>
                <b-button variant="success" size="sm" @click="updateData">
                    Perbaharui
                </b-button>
            </div>
        </template>
    </b-modal>
</div>
</template>

<script>
import _ from 'lodash' //IMPORT LODASH, DIMANA AKAN DIGUNAKAN UNTUK MEMBUAT DELAY KETIKA KOLOM PENCARIAN DIISI
export default {
    //PROPS INI ADALAH DATA YANG AKAN DIMINTA DARI PENGGUNA COMPONENT DATATABLE YANG KITA BUAT
    props: {
        //ITEMS STRUKTURNYA ADALAH ARRAY, KARENA BAGIAN INI BERISI DATA YANG AKAN DITAMPILKAN DAN SIFATNYA WAJIB DIKIRIMKAN KETIKA COMPONENT INI DIGUNAKAN
        items: {
            type: Array,
            required: true
        },
        //FIELDS JUGA SAMA DENGAN ITEMS
        fields: {
            type: Array,
            required: true
        },
        //ADAPUN META, TYPENYA ADALAH OBJECT YANG BERISI INFORMASI MENGENAL CURRENT PAGE, LOAD PERPAGE, TOTAL DATA, DAN LAIN SEBAGAINYA.
        meta: {
            required: true
        },
        title: {
            type: String,
            default: "Delete Modal"
        },
        editUrl: {
            type: String,
            default: null
        }
    },
    components: {
    },
    data() {
        return {
            formattedMoney: null,
            editmode: false,
            form: new Form({
                id: '',
                sekolah_id: '',
                kode: '',
                judul: '',
                mata_pelajaran_id: '',
                nama_penerbit: '',
                isbn_issn: '',
                keterangan: '',
                kelas: '',
            }),
            //VARIABLE INI AKAN MENGHADLE SORTING DATA
            sortBy: null, //FIELD YANG AKAN DISORT AKAN OTOMATIS DISIMPAN DISINI
            sortDesc: false, //SEDANGKAN JENISNYA ASCENDING ATAU DESC AKAN DISIMPAN DISINI
            //TAMBAHKAN DUA VARIABLE INI UNTUK MENGHANDLE MODAL DAN DATA YANG AKAN DIHAPUS
            deleteModal: false,
            showModal: false,
            editModal: false,
            modalText: {
                sekolah: '',
                mata_pelajaran: '',
            },
            selected: null,
            data_sekolah: [],
            data_mapel: [],
            data_kelas: [7, 8, 9],
            data_penerbit : [],
        }
    },
    watch: {
        //KETIKA VALUE DARI VARIABLE sortBy BERUBAH
        sortBy(val) {
            //MAKA KITA EMIT DENGAN NAMA SORT DAN DATANYA ADALAH OBJECT BERUPA VALUE DARI SORTBY DAN SORTDESC
            //EMIT BERARTI MENGIRIMKAN DATA KEPADA PARENT ATAU YANG MEMANGGIL COMPONENT INI
            //SEHINGGA DARI PARENT TERSEBUT, KITA BISA MENGGUNAKAN VALUE YANG DIKIRIMKAN
            this.$emit('sort', {
                sortBy: this.sortBy,
                sortDesc: this.sortDesc
            })
        },
        //KETIKA VALUE DARI SORTDESC BERUBAH
        sortDesc(val) {
            //MAKA CARA YANG SAMA AKAN DIKERJAKAN
            this.$emit('sort', {
                sortBy: this.sortBy,
                sortDesc: this.sortDesc
            })
        }
    },
    methods: {
        updateMapel(data){
            console.log(data);
            axios.get(`/api/referensi/all-mapel`, {
                //KIRIMKAN PARAMETER BERUPA PAGE YANG SEDANG DILOAD, PENCARIAN, LOAD PERPAGE DAN SORTING.
                params: {
                    tingkat_pendidikan_id: data,
                }
            })
            .then((response) => {
                //JIKA RESPONSENYA DITERIMA
                let getData = response.data.data
                this.data_mapel = getData
            })
        },
        //JIKA SELECT BOX DIGANTI, MAKA FUNGSI INI AKAN DIJALANKAN
        addSekolah(id) {
            this.$refs.TambahSekolahPendamping.show(id);
        },
        listSekolah(id) {
            this.$refs.ListSekolahPendamping.show(id);
        },
        loadPerPage(val) {
            //DAN KITA EMIT LAGI DENGAN NAMA per_page DAN VALUE SESUAI PER_PAGE YANG DIPILIH
            this.$emit('per_page', this.meta.per_page)
        },
        //KETIKA PAGINATION BERUBAH, MAKA FUNGSI INI AKAN DIJALANKAN
        changePage(val) {
            //KIRIM EMIT DENGAN NAMA PAGINATION DAN VALUENYA ADALAH HALAMAN YANG DIPILIH OLEH USER
            this.$emit('pagination', val)
        },
        //KETIKA KOTAK PENCARIAN DIISI, MAKA FUNGSI INI AKAN DIJALANKAN
        //KITA GUNAKAN DEBOUNCE UNTUK MEMBUAT DELAY, DIMANA FUNGSI INI AKAN DIJALANKAN
        //500 MIL SECOND SETELAH USER BERHENTI MENGETIK
        search: _.debounce(function (e) {
            //KIRIM EMIT DENGAN NAMA SEARCH DAN VALUE SESUAI YANG DIKETIKKAN OLEH USER
            this.$emit('search', e.target.value)
        }, 500),
        deleteData(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Tindakan ini tidak dapat dikembalikan!",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    return fetch('/api/referensi/delete-buku/' + id, {
                        method: 'DELETE',
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire(
                            data.title,
                            data.message,
                            data.status
                        ).then(()=>{
                            this.loadPerPage(10);
                        });
                    }).catch((data) => {
                        Swal.fire("Failed!", data.message, "warning");
                    });
                }
            })
        },
        openShowModal(row) {
            let getData = row.item
            this.showModal = true
            this.modalText = getData
            
        },
        getSekolah() {
            axios.get(`/api/referensi/all-sekolah`)
            .then((response) => {
                //JIKA RESPONSENYA DITERIMA
                let getData = response.data.data
                //this.items = getData.data //MAKA ASSIGN DATA POSTINGAN KE DALAM VARIABLE ITEMS
                //DAN ASSIGN INFORMASI LAINNYA KE DALAM VARIABLE META
                this.data_sekolah = getData
            })
        },
        updateMapel(data){
            axios.get(`/api/referensi/all-mapel`, {
                //KIRIMKAN PARAMETER BERUPA PAGE YANG SEDANG DILOAD, PENCARIAN, LOAD PERPAGE DAN SORTING.
                params: {
                    tingkat_pendidikan_id: data,
                }
            })
            .then((response) => {
                //JIKA RESPONSENYA DITERIMA
                let getData = response.data.data
                this.data_mapel = getData
            })
        },
        getPenerbit(){
            axios.get(`/api/referensi/all-penerbit`)
            .then((response) => {
                //JIKA RESPONSENYA DITERIMA
                let getData = response.data.data
                //this.items = getData.data //MAKA ASSIGN DATA POSTINGAN KE DALAM VARIABLE ITEMS
                //DAN ASSIGN INFORMASI LAINNYA KE DALAM VARIABLE META
                this.data_penerbit = getData
            })
        },
        editData(row) {
            this.getSekolah()
            let getData = row.item
            this.updateMapel(getData.kelas)
            this.getPenerbit()
            console.log(getData);
            this.form.id = getData.buku_id
            this.form.judul = getData.judul
            this.form.kode = getData.kode
            this.form.kelas = getData.kelas
            this.form.nama_penerbit = (getData.penerbit_id) ? {penerbit_id: getData.penerbit_id, nama:getData.nama_penerbit} : getData.nama_penerbit
            this.form.isbn_issn = getData.isbn_issn
            this.form.keterangan = getData.keterangan
            this.form.sekolah_id = {sekolah_id : getData.sekolah_id, nama: getData.sekolah.nama}
            this.form.mata_pelajaran_id = {mata_pelajaran_id: getData.mata_pelajaran_id, nama: getData.mata_pelajaran.nama}
            /*this.editmode = true
            this.editModal = true

            $('#modalEdit').modal('show');*/
            this.$refs['editModal'].show()
        },
        hideModal() {
            this.$refs['editModal'].hide()
        },
        updateData() {
            let id = this.form.id
            this.form.put('/api/referensi/update-buku/' + id).then((response) => {
                this.$refs['editModal'].hide()
                Toast.fire({
                    icon: response.status,
                    title: response.message
                });
                this.loadPerPage(10);
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
