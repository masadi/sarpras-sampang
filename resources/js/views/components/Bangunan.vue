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
                    <b-dropdown id="dropdown-dropleft" dropleft text="Aksi" variant="success" size="sm">
                        <b-dropdown-item href="javascript:" @click="openShowModal(row)"><i class="fas fa-eye"></i> Detil</b-dropdown-item>
                        <b-dropdown-item href="javascript:" @click="uploadFoto(row.item.bangunan_id)"><i class="fas fa-upload"></i> Upload Foto</b-dropdown-item>
                        <b-dropdown-item href="javascript:" @click="inputKondisi(row)"><i class="fas fa-list"></i> Input Kondisi</b-dropdown-item>
                        <b-dropdown-item href="javascript:" @click="editData(row)"><i class="fas fa-edit"></i> Edit</b-dropdown-item>
                        <b-dropdown-item href="javascript:" @click="deleteData(row.item.bangunan_id)"><i class="fas fa-trash"></i> Hapus</b-dropdown-item>
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
        <b-modal id="modal-xl" size="xl" v-model="showModal" title="Detil Bangunan">
            <table class="table table-bordered table-striped">
                <tr>
                    <td width="30%">Sekolah</td>
                    <td width="70%">: {{(modalText.tanah) ? (modalText.tanah.sekolah) ? modalText.tanah.sekolah.nama : '-' : '-'}}</td>
                </tr>
                <tr>
                    <td>Tanah</td>
                    <td>: {{(modalText.tanah) ? modalText.tanah.nama : '-'}}</td>
                </tr>
                <tr>
                    <td>Nama Bangunan</td>
                    <td>: {{modalText.nama}}</td>
                </tr>
                <tr>
                    <td>Nomor IMB</td>
                    <td>: {{modalText.imb}}</td>
                </tr>
                <tr>
                    <td>Panjang</td>
                    <td>: {{modalText.panjang}} m</td>
                </tr>
                <tr>
                    <td>Lebar</td>
                    <td>: {{modalText.lebar}} m</td>
                </tr>
                <tr>
                    <td>Luas</td>
                    <td>: {{modalText.luas}} m<sup>2</sup></td>
                </tr>
                <tr>
                    <td>Panjang</td>
                    <td>: {{modalText.panjang}} m</td>
                </tr>
                <tr>
                    <td>Jumlah Lantai</td>
                    <td>: {{modalText.lantai}}</td>
                </tr>
                <tr>
                    <td>Kepemilikan</td>
                    <td>: {{(modalText.kepemilikan) ? modalText.kepemilikan.nama : '-'}}</td>
                </tr>
                <tr>
                    <td>Keterangan</td>
                    <td>: {{modalText.keterangan}}</td>
                </tr>
            </table>
            <template v-for="foto in modalText.foto">
                <div class="show-image">
                    <img :src="'/uploads/' + foto.nama" class="img-thumbnail" width="200">
                    <a :href="'/uploads/' + foto.nama" target="_blank"></a>
                    <button class="view btn btn-primary" @click="viewImage(foto)"><i class="fas fa-search-plus"></i></button>
                    <button class="delete btn btn-danger" @click="trashImage(foto)"><i class="fas fa-trash"></i></button>
                </div>
            </template>
            <template v-slot:modal-footer>
                <div class="w-100 float-right">
                    <b-button variant="secondary" size="sm" @click="showModal=false">Tutup</b-button>
                </div>
            </template>
        </b-modal>
        <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEdit" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perbaharui Data Bangunan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="updateData()">
                    <div class="modal-body">
                        <div class="form-group">
                        <input v-model="form.id" type="hidden" name="id" class="form-control" :class="{ 'is-invalid': form.errors.has('id') }">
                            <label>Sekolah</label>
                            <v-select label="nama" :options="data_sekolah" v-model="form.sekolah_id" @input="updateTanah" />
                            
                        </div>
                        <div class="form-group">
                            <label>Tanah</label>
                            <v-select label="nama" :options="data_tanah" v-model="form.tanah_id" />
                            
                        </div>
                        <div class="form-group">
                            <label>Nama</label>
                            <input v-model="form.nama" type="text" name="nama" class="form-control" :class="{ 'is-invalid': form.errors.has('nama') }">
                            
                        </div>
                        <div class="form-group">
                            <label>Nomor IMB</label>
                            <input v-model="form.imb" type="text" name="imb" class="form-control" :class="{ 'is-invalid': form.errors.has('imb') }">
                            
                        </div>
                        <div class="form-group">
                            <label>Panjang (m)</label>
                            <input v-model="form.panjang" type="text" name="panjang" class="form-control" :class="{ 'is-invalid': form.errors.has('panjang') }">
                            
                        </div>
                        <div class="form-group">
                            <label>Lebar (m)</label>
                            <input v-model="form.lebar" type="text" name="lebar" class="form-control" :class="{ 'is-invalid': form.errors.has('lebar') }">
                            
                        </div>
                        <div class="form-group">
                            <label>Luas (m<sup>2</sup>)</label>
                            <input v-model="form.luas" type="text" name="luas" class="form-control" :class="{ 'is-invalid': form.errors.has('luas') }">
                            
                        </div>
                        <div class="form-group">
                            <label>Jumlah Lantai</label>
                            <input v-model="form.lantai" type="text" name="lantai" class="form-control" :class="{ 'is-invalid': form.errors.has('lantai') }">
                            
                        </div>
                        <div class="form-group">
                            <label>Kepemilikan</label>
                            <v-select label="nama" :options="data_kepemilikan" v-model="form.kepemilikan_sarpras_id" />
                            
                        </div>
                        <div class="form-group">
                            <label>Tahun Bangun</label><br>
                            <date-picker v-model="form.tahun_bangun" type="year" :class="{ 'is-invalid': form.errors.has('tahun_bangun') }"></date-picker>
                            
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
        <div class="modal fade" id="modalKondisi" tabindex="-1" role="dialog" aria-labelledby="modalKondisi" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Kondisi Bangunan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="updateKondisi()">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box bg-info">
                                    <div class="info-box-content text-center">
                                        <h5 class="info-box-text">Tingkat Persentase Kerusakan</h5> 
                                        <h3 class="info-box-number">{{presentase_kerusakan}}</h3>
                                        <input v-model="form.presentase_kerusakan" type="hidden" name="presentase_kerusakan">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box bg-navy">
                                    <div class="info-box-content text-center">
                                        <h5 class="info-box-text">Kriteria Kerusakan</h5> 
                                        <h3 class="info-box-number">{{kriteria_kerusakan}}</h3> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input v-model="form.bangunan_id" type="hidden" name="bangunan_id" class="form-control" :class="{ 'is-invalid': form.errors.has('bangunan_id') }">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Klasifikasi Kerusakan Pondasi</label>
                                    <div class="col-sm-9">
                                        <v-select :options="data_pondasi" v-model="form.ket_pondasi" @input="updatePondasi" />
                                        
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Kerusakan Pondasi (%)</label>
                                    <div class="col-sm-9">
                                        <input v-model="form.rusak_pondasi" type="text" name="rusak_pondasi" readonly class="form-control" :class="{ 'is-invalid': form.errors.has('rusak_pondasi') }">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Kerusakan Kolom (%)</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.rusak_sloop_kolom_balok" type="text" name="rusak_sloop_kolom_balok" class="form-control" :class="{ 'is-invalid': form.errors.has('rusak_sloop_kolom_balok') }" @input="getTotal" :readonly="form.presentase_kerusakan >= 100">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Keterangan Kolom</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.ket_sloop_kolom_balok" type="text" name="ket_sloop_kolom_balok" class="form-control" :class="{ 'is-invalid': form.errors.has('ket_sloop_kolom_balok') }">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Kerusakan Balok (%)</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.rusak_kudakuda_atap" type="text" name="rusak_kudakuda_atap" class="form-control" :class="{ 'is-invalid': form.errors.has('rusak_kudakuda_atap') }"  @input="getTotal" :readonly="form.presentase_kerusakan >= 100">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Keterangan Balok</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.ket_kudakuda_atap" type="text" name="ket_kudakuda_atap" class="form-control" :class="{ 'is-invalid': form.errors.has('ket_kudakuda_atap') }">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Kerusakan Pelat Lantai (%)</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.rusak_plester_struktur" type="text" name="rusak_plester_struktur" class="form-control" :class="{ 'is-invalid': form.errors.has('rusak_plester_struktur') }"  @input="getTotal" :readonly="form.presentase_kerusakan >= 100">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Keterangan Pelat Lantai</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.ket_plester_struktur" type="text" name="ket_plester_struktur" class="form-control" :class="{ 'is-invalid': form.errors.has('ket_plester_struktur') }">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Kerusakan Atap (%)</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.rusak_tutup_atap" type="text" name="rusak_tutup_atap" class="form-control" :class="{ 'is-invalid': form.errors.has('rusak_tutup_atap') }"  @input="getTotal" :readonly="form.presentase_kerusakan >= 100">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Keterangan Penutup Atap</label>
                                    <div class="col-sm-6">
                                        <b-form-group v-slot="{ ariaDescribedby }">
                                            <b-form-radio v-model="form.ket_tutup_atap" :aria-describedby="ariaDescribedby" name="ket_tutup_atap" value="BUKAN DAK">BUKAN DAK</b-form-radio>
                                            <b-form-radio v-model="form.ket_tutup_atap" :aria-describedby="ariaDescribedby" name="ket_tutup_atap" value="DAK BETON">DAK BETON</b-form-radio>
                                            <b-form-radio v-model="form.ket_tutup_atap" :aria-describedby="ariaDescribedby" name="ket_tutup_atap" value="TIDAK MEMILIKI ATAP">TIDAK MEMILIKI ATAP</b-form-radio>
                                        </b-form-group>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button v-show="editmode" type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalUpload" tabindex="-1" role="dialog" aria-labelledby="modalUpload" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <form>
                        <div class="modal-header">
                            <h5 class="modal-title">Upload Foto Bangunan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div v-for="(foto, index) in fotos">
                                <div class="row">
                                    <div class="form-group col-lg-10">
                                        <b-form-file v-model="foto.file" :state="Boolean(foto.file)" accept=".jpg, .png, .jpeg, .pdf" placeholder="Choose a file or drop it here..." drop-placeholder="Drop file here..." ></b-form-file>
                                        <div class="progress-bar" role="progressbar"  :style="{width: progressBar + '%'}"  :aria-valuenow="progressBar"  aria-valuemin="0"  aria-valuemax="100"></div>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" v-on:click="removeApartment(index)" class="btn btn-block btn-danger">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="button" v-on:click="addNewApartment" class="btn btn-success">+Tambah Form</button>
                            <button type="submit" v-on:click.prevent="sumbitForm" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import _ from 'lodash' //IMPORT LODASH, DIMANA AKAN DIGUNAKAN UNTUK MEMBUAT DELAY KETIKA KOLOM PENCARIAN DIISI
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import moment from 'moment'
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
        DatePicker
    },
    data() {
        return {
            progressBar: 0,
            foto: {
                file: '',
            },
            fotos: [],
            foto_bangunan_id: '',
            presentase_kerusakan: '0%',
            kriteria_kerusakan : '-',
            data_pondasi: [
                {label: 'Tidak ada kerusakan', code: 0},
                {label: 'Penurunan merata pada seluruh struktur bangunan', code: 20},
                {label: 'Penurunan tidak merata, namun perbedaan penurunan tidak melebihi 1/250L', code: 40},
                {label: 'Penurunan > 1/250L sehingga menimbulkan kerusakan struktur atasnya. Tanah di sekeliling bangunan naik', code: 60},
                {label: 'Bangunan miring secara kasat mata, lantai dasar naik/menggelembung', code: 80},
                {label: 'Pondasi patah, bergeser akibat longsor, stuktur atas menjadi rusak', code: 100},
            ],
            editmode: false,
            form: new Form({
                bangunan_id: '',
                rusak_pondasi : 0,
                ket_pondasi: 'Tidak ada kerusakan',
                rusak_sloop_kolom_balok: 0,
                ket_sloop_kolom_balok: '-',
                rusak_kudakuda_atap: 0,
                ket_kudakuda_atap: '-',
                rusak_plester_struktur: 0,
                ket_plester_struktur: '-',
                rusak_tutup_atap: 0,
                ket_tutup_atap: '-',
                //edit
                id: '',
                sekolah_id: '',
                tanah_id: '',
                nama: '',
                imb: '',
                panjang: '',
                lebar: '',
                luas: '',
                lantai: '',
                tahun_bangun: '',
                kepemilikan_sarpras_id: '',
                keterangan: '',
                presentase_kerusakan: 0,
            }),
            //VARIABLE INI AKAN MENGHADLE SORTING DATA
            sortBy: null, //FIELD YANG AKAN DISORT AKAN OTOMATIS DISIMPAN DISINI
            sortDesc: false, //SEDANGKAN JENISNYA ASCENDING ATAU DESC AKAN DISIMPAN DISINI
            //TAMBAHKAN DUA VARIABLE INI UNTUK MENGHANDLE MODAL DAN DATA YANG AKAN DIHAPUS
            deleteModal: false,
            showModal: false,
            editModal: false,
            modalText: '',
            selected: null,
            data_sekolah: [],
            data_tanah: [],
            data_kepemilikan: [],
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
        viewImage(foto){
            window.open('/uploads/' + foto.nama, '_blank');
        },
        trashImage(foto){
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
                    return fetch('/api/referensi/delete-foto/'+foto.foto_id, {
                        method: 'DELETE',
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire(
                            data.title,
                            data.message,
                            data.status
                        ).then(()=>{
                            this.showModal = false
                            this.loadPerPage(10);
                        });
                    }).catch((data)=> {
                        Swal.fire("Failed!", data.message, "warning");
                    });
                }
            })
        },
        addNewApartment: function () {
            this.fotos.push(Vue.util.extend({}, this.apartment))
        },
        removeApartment: function (index) {
            Vue.delete(this.fotos, index);
        },
        sumbitForm: function () {
            /*
            * You can remove or replace the "submitForm" method.
            * Remove: if you handle form sumission on server side.
            * Replace: for example you need an AJAX submission.
            */
            console.log('<< Form Submitted >>')
            console.log('Vue.js fotos object:', this.fotos)
            var foto_bangunan_id = this.foto_bangunan_id
            $.each(this.fotos, function (key, value) {
                console.log(value.file);
                let formData = new FormData();
                formData.append('file', value.file);
                formData.append('bangunan_id', foto_bangunan_id);
                axios.post('/api/referensi/simpan-file', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    //FUNGSI INI YANG MEMILIKI PERAN UNTUK MENGUBAH SEBERAPA JAUH PROGRESS UPLOAD FILE BERJALAN
                    onUploadProgress: function( progressEvent ) {
                        //DATA TERSEBUT AKAN DI ASSIGN KE VARIABLE progressBar
                        this.progressBar = parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total))
                    }.bind(this)
                }).then((response) => {
                    setTimeout(() => {
                        this.progressBar = 0
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Upload berhasil'
                    });
                    $('#modalUpload').modal('hide');
                    this.loadPerPage(10);
                }).catch((data)=> {
                    console.log(data);
                    //Swal.fire("Failed!", data.message, "warning");
                });
            })
        },
        uploadFoto(bangunan_id){
            this.foto_bangunan_id = bangunan_id
            this.addNewApartment()
            $('#modalUpload').modal('show');
        },
        updatePondasi(val){
            this.form.rusak_pondasi = val.code
            this.getTotal()
        },
        getTotal(){
            let total_kerusakan = Number(this.number_format(this.form.rusak_pondasi,2)) + Number(this.number_format(this.form.rusak_sloop_kolom_balok,2)) + Number(this.number_format(this.form.rusak_kudakuda_atap,2)) + Number(this.number_format(this.form.rusak_plester_struktur,2)) + Number(this.number_format(this.form.rusak_tutup_atap,2)) + Number(this.number_format(this.form.ket_tutup_atap,2))
            let make_kriteria = null
            if(total_kerusakan == 0){
                make_kriteria = 'BAIK'
            } else if(total_kerusakan > 0 && total_kerusakan <= 30){
                make_kriteria = 'RINGAN'
            } else if(total_kerusakan > 30 && total_kerusakan <= 45){
                make_kriteria = 'SEDANG'
            } else if(total_kerusakan > 45 && total_kerusakan <= 65){
                make_kriteria = 'BERAT'
            } else if(total_kerusakan > 65){
                make_kriteria = 'SANGAT BERAT'
            }
            console.log(total_kerusakan);
            this.kriteria_kerusakan = make_kriteria
            this.presentase_kerusakan = this.number_format(total_kerusakan,2)+'%'
            this.form.presentase_kerusakan = this.number_format(total_kerusakan,2)
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
                    return fetch('/api/referensi/delete-bangunan/'+id, {
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
        updateTanah(data){
            axios.get(`/api/referensi/all-tanah`, {
                //KIRIMKAN PARAMETER BERUPA PAGE YANG SEDANG DILOAD, PENCARIAN, LOAD PERPAGE DAN SORTING.
                params: {
                    sekolah_id: data.sekolah_id,
                }
            })
            .then((response) => {
                let getData = response.data.data
                this.data_tanah = getData
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
        getTahun(tahun_bangun){
            let tahunBangun = moment(String(tahun_bangun))
            return tahunBangun._d
        },
        openShowModal(row) {
            this.showModal = true
            this.modalText = row.item
        },
        editData(row) {
            let getData = row.item
            this.getTahun(getData.tahun_bangun)
            this.editmode = true
            this.editModal = true
            this.form.id = getData.bangunan_id
            this.form.sekolah_id = {sekolah_id: getData.sekolah_id, nama: getData.tanah.sekolah.nama}
            this.form.tanah_id = {tanah_id: getData.tanah_id, nama: getData.tanah.nama}
            this.form.nama = getData.nama
            this.form.imb = getData.imb
            this.form.panjang = getData.panjang
            this.form.lebar = getData.lebar
            this.form.luas = getData.luas
            this.form.lantai = getData.lantai
            this.form.tahun_bangun = this.getTahun(getData.tahun_bangun)
            this.form.kepemilikan_sarpras_id = {kepemilikan_sarpras_id: getData.kepemilikan_sarpras_id, nama: getData.kepemilikan.nama}
            this.form.keterangan = getData.keterangan
            $('#modalEdit').modal('show');
        },
        inputKondisi(row){
            this.editmode = true
            this.form.bangunan_id = row.item.bangunan_id
            axios.get(`/api/kondisi/bangunan`, {
                //KIRIMKAN PARAMETER BERUPA PAGE YANG SEDANG DILOAD, PENCARIAN, LOAD PERPAGE DAN SORTING.
                params: {
                    bangunan_id: row.item.bangunan_id,
                }
            })
            .then((response) => {
                //JIKA RESPONSENYA DITERIMA
                let getData = response.data.data
                if(getData){
                    this.form.bangunan_id = getData.bangunan_id
                    this.form.rusak_pondasi = this.number_format(getData.rusak_pondasi)
                    this.form.ket_pondasi = getData.ket_pondasi
                    this.form.rusak_sloop_kolom_balok = this.number_format(getData.rusak_sloop_kolom_balok)
                    this.form.ket_sloop_kolom_balok = getData.ket_sloop_kolom_balok
                    this.form.rusak_kudakuda_atap = this.number_format(getData.rusak_kudakuda_atap)
                    this.form.ket_kudakuda_atap = getData.ket_kudakuda_atap
                    this.form.rusak_plester_struktur = this.number_format(getData.rusak_plester_struktur)
                    this.form.ket_plester_struktur = getData.ket_plester_struktur
                    this.form.rusak_tutup_atap = this.number_format(getData.rusak_tutup_atap)
                    this.form.ket_tutup_atap = getData.ket_tutup_atap
                    let total_kerusakan = Number(getData.rusak_pondasi) + Number(getData.rusak_sloop_kolom_balok) + Number(getData.rusak_kudakuda_atap) + Number(getData.rusak_plester_struktur) + Number(getData.rusak_tutup_atap)
                    this.presentase_kerusakan = this.number_format(total_kerusakan,2)+'%'
                    this.form.presentase_kerusakan = this.number_format(total_kerusakan,2)
                    let make_kriteria = null
                    if(total_kerusakan == 0){
                        make_kriteria = 'BAIK'
                    } else if(total_kerusakan >= 1 && total_kerusakan <= 30){
                        make_kriteria = 'RINGAN'
                    } else if(total_kerusakan >= 31 && total_kerusakan <= 45){
                        make_kriteria = 'SEDANG'
                    } else if(total_kerusakan >= 46 && total_kerusakan <= 65){
                        make_kriteria = 'BERAT'
                    } else if(total_kerusakan > 66){
                        make_kriteria = 'TOTAL'
                    }
                    this.kriteria_kerusakan = make_kriteria
                }
            })
            $('#modalKondisi').modal('show');
        },
        number_format(number, decimals, dec_point, thousands_sep) {
            var n = !isFinite(+number) ? 0 : +number, 
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                toFixedFix = function (n, prec) {
                    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
                    var k = Math.pow(10, prec);
                    return Math.round(n * k) / k;
                },
                s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        },
        updateKondisi(){
            this.form.post('/api/kondisi/simpan-bangunan').then((response)=>{
                $('#modalKondisi').modal('hide');
                Toast.fire({
                    icon: 'success',
                    title: response.status
                });
                this.loadPerPage(10);
            }).catch((e)=>{
                Toast.fire({
                    icon: 'error',
                    title: 'Some error occured! Please try again'
                });
            })
        },
        updateData(){
            let id = this.form.id;
            this.form.put('/api/referensi/update-bangunan/'+id).then((response)=>{
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
<style>
div.show-image {
    position: relative;
    float:left;
    margin:5px;
}
div.show-image:hover img{
    opacity:0.5;
}
div.show-image:hover button {
    display: block;
}
div.show-image button {
    position:absolute;
    display:none;
}
div.show-image button.view {
    top:50%;
    left:30%;
}
div.show-image button.delete {
    top:50%;
    left:50%;
}
</style>