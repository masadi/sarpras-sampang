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
                <label class="mr-2">Search</label>
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
                <template v-slot:cell(actions)="row">
                    <b-dropdown id="dropdown-dropleft" dropleft text="Aksi" variant="success" class="m-2">
                        <b-dropdown-item href="javascript:" @click="openShowModal(row)"><i class="fas fa-search"></i> Detil</b-dropdown-item>
                        <b-dropdown-item href="javascript:" @click="editData(row)"><i class="fas fa-edit"></i> Edit</b-dropdown-item>
                        <b-dropdown-item href="javascript:" @click="deleteData(row.item.angkutan_id)"><i class="fas fa-trash"></i> Hapus</b-dropdown-item>
                    </b-dropdown>
                </template>
            </b-table>   
      
      	<!-- BAGIAN INI AKAN MENAMPILKAN JUMLAH DATA YANG DI-LOAD -->
          <div class="row">
        <div class="col-md-6">
            <p>Showing {{ meta.from }} to {{ meta.to }} of {{ meta.total }} items</p>
        </div>
      
      	<!-- BLOCK INI AKAN MENJADI PAGINATION DARI DATA YANG DITAMPILKAN -->
        <div class="col-md-6">
          	<!-- DAN KETIKA TERJADI PERGANTIAN PAGE, MAKA AKAN MENJALANKAN FUNGSI changePage -->
            <b-pagination
                v-model="meta.current_page"
                :total-rows="meta.total"
                :per-page="meta.per_page"
                align="right"
                @change="changePage"
                aria-controls="dw-datatable"
            ></b-pagination>
        </div>
        </div>
        <b-modal id="modal-xl" size="xl" v-model="showModal" title="Detil Angkutan">
            <table class="table table-border">
                <tr>
                    <td width="20%">Sekolah</td>
                    <td width="80%">: {{ (modalText.sekolah) ? (modalText.sekolah.nama) : '-' }}</td>
                </tr>
                <tr>
                    <td>Jenis Sarana</td>
                    <td>: {{ (modalText.jenis_sarana) ? (modalText.jenis_sarana.nama) : '-' }}</td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td>: {{ modalText.nama }}</td>
                </tr>
                <tr>
                    <td>Spesifikasi</td>
                    <td>: {{ modalText.spesifikasi }}</td>
                </tr>
                <tr>
                    <td>Merk</td>
                    <td>: {{ modalText.merk }}</td>
                </tr>
                <tr>
                    <td>Nomor Polisi</td>
                    <td>: {{ modalText.no_polisi }}</td>
                </tr>
                <tr>
                    <td>Nomor BPKB</td>
                    <td>: {{ modalText.no_bpkb }}</td>
                </tr>
                <tr>
                    <td>Status Kepemilikan</td>
                    <td>: {{ (modalText.kepemilikan) ? (modalText.kepemilikan.nama) : '-' }}</td>
                </tr>
                <tr>
                    <td>keterangan</td>
                    <td>: {{ modalText.keterangan }}</td>
                </tr>
            </table>
            <template v-slot:modal-footer>
                <div class="w-100 float-right">
                    <b-button
                        variant="secondary"
                        size="sm"
                        @click="showModal=false"
                    >
                        Tutup
                    </b-button>
                </div>
            </template>
        </b-modal>
        <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEdit" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perbaharui Data Angkutan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="updateData()">
                    <div class="modal-body">
                        <div class="form-group">
                            <input v-model="form.id" type="hidden" name="id" class="form-control" :class="{ 'is-invalid': form.errors.has('id') }">
                            <label>Sekolah</label>
                            <v-select label="nama" :options="data_sekolah" v-model="form.sekolah_id" @input="updateJenis" />
                            
                        </div>
                        <div class="form-group">
                            <label>Jenis Sarana</label>
                            <v-select label="nama" :options="data_jenis" v-model="form.jenis_sarana_id" />
                            
                        </div>
                        <div class="form-group">
                            <label>Nama</label>
                            <input v-model="form.nama" type="text" name="nama" class="form-control" :class="{ 'is-invalid': form.errors.has('nama') }">
                            
                        </div>
                        <div class="form-group">
                            <label>Merk</label>
                            <input v-model="form.merk" type="text" name="merk" class="form-control" :class="{ 'is-invalid': form.errors.has('merk') }">
                            
                        </div>
                        <div class="form-group">
                            <label>Nomor Polisi</label>
                            <input v-model="form.no_polisi" type="text" name="no_polisi" class="form-control" :class="{ 'is-invalid': form.errors.has('no_polisi') }">
                            
                        </div>
                        <div class="form-group">
                            <label>Nomor BPKB</label>
                            <input v-model="form.no_bpkb" type="text" name="no_bpkb" class="form-control" :class="{ 'is-invalid': form.errors.has('no_bpkb') }">
                            
                        </div>
                        <div class="form-group">
                            <label>Spesifikasi</label>
                            <input v-model="form.spesifikasi" type="text" name="spesifikasi" class="form-control" :class="{ 'is-invalid': form.errors.has('spesifikasi') }">
                            
                        </div>
                        <div class="form-group">
                            <label>Kepemilikan</label>
                            <v-select label="nama" :options="data_kepemilikan" v-model="form.kepemilikan_sarpras_id" />
                            
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <input v-model="form.keterangan" type="text" name="keterangan" class="form-control" :class="{ 'is-invalid': form.errors.has('keterangan') }">
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button v-show="editmode" type="submit" class="btn btn-success">Perbaharui</button>
                        <button v-show="!editmode" type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>
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
    data() {
        return {
            data_subs: [],
            editmode: false,
            form: new Form({
                sekolah_id: '',
                jenis_sarana_id: '',
                nama: '',
                spesifikasi: '',
                merk: '',
                no_polisi: '',
                no_bpkb: '',
                alamat: '',
                kepemilikan_sarpras_id: '',
                keterangan: '',
            }),
            data_sekolah: [],
            data_jenis: [],
            data_kepemilikan: [],
            //VARIABLE INI AKAN MENGHADLE SORTING DATA
            sortBy: null, //FIELD YANG AKAN DISORT AKAN OTOMATIS DISIMPAN DISINI
            sortDesc: false, //SEDANGKAN JENISNYA ASCENDING ATAU DESC AKAN DISIMPAN DISINI
            //TAMBAHKAN DUA VARIABLE INI UNTUK MENGHANDLE MODAL DAN DATA YANG AKAN DIHAPUS
            deleteModal: false,
            showModal: false,
            editModal: false,
            modalText: '',
            selected: null 
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
        updateJenis(){
            axios.get(`/api/referensi/all-jenis-sarana`, {
                //KIRIMKAN PARAMETER BERUPA PAGE YANG SEDANG DILOAD, PENCARIAN, LOAD PERPAGE DAN SORTING.
                params: {
                    data: 'a_angkutan',
                }
            })
            .then((response) => {
                let getData = response.data.data
                this.data_jenis = getData
                this.getKepemilikan()
            })
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
        getKepemilikan(){
            axios.get(`/api/referensi/kepemilikan`)
            .then((response) => {
                //JIKA RESPONSENYA DITERIMA
                let getData = response.data.data
                //this.items = getData.data //MAKA ASSIGN DATA POSTINGAN KE DALAM VARIABLE ITEMS
                //DAN ASSIGN INFORMASI LAINNYA KE DALAM VARIABLE META
                this.data_kepemilikan = getData
            })
        },
        openShowModal(row) {
            let getData = row
            this.showModal = true
            this.modalText = row.item
            this.selected = row.item
        },
        //JIKA SELECT BOX DIGANTI, MAKA FUNGSI INI AKAN DIJALANKAN
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
        deleteData(id){
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
                    return fetch('/api/referensi/delete-angkutan/'+id, {
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
                    }).catch((data)=> {
                        Swal.fire("Failed!", data.message, "warning");
                    });
                }
            })
        },
        editData(row) {
            this.getSekolah()
            this.updateJenis()
            let getData = row.item
            console.log(getData);
            this.editmode = true
            this.editModal = true
            this.form.id = getData.angkutan_id
            this.form.sekolah_id= {sekolah_id : getData.sekolah_id, nama: getData.sekolah.nama}
            this.form.jenis_sarana_id = {id: getData.jenis_sarana_id, nama: getData.jenis_sarana.nama}
            this.form.nama = getData.nama
            this.form.spesifikasi = getData.spesifikasi
            this.form.merk = getData.merk
            this.form.no_polisi = getData.no_polisi
            this.form.no_bpkb = getData.no_bpkb
            this.form.alamat = getData.alamat
            this.form.kepemilikan_sarpras_id = {kepemilikan_sarpras_id: getData.kepemilikan_sarpras_id, nama: getData.kepemilikan.nama}
            this.form.keterangan= getData.keterangan
            $('#modalEdit').modal('show');
        },
        updateData(){
            let id = this.form.id;
            this.form.put('/api/referensi/update-angkutan/'+id).then((response)=>{
                $('#modalEdit').modal('hide');
                Toast.fire({
                    icon: response.status,
                    title: response.message
                });
                this.loadPerPage(10);
            }).catch((e)=>{
                Toast.fire({
                    icon: 'error',
                    title: 'Some error occured! Please try again'
                });
            })
        },
    }
}
</script>