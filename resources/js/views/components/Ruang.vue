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
                    <b-dropdown id="dropdown-dropleft" dropleft text="Aksi" variant="success">
                        <b-dropdown-item href="javascript:" @click="openShowModal(row)"><i class="fas fa-eye"></i> Detil</b-dropdown-item>
                        <b-dropdown-item href="javascript:" @click="uploadFoto(row.item.ruang_id)"><i class="fas fa-upload"></i> Upload Foto</b-dropdown-item>
                        <b-dropdown-item href="javascript:" @click="inputKondisi(row)"><i class="fas fa-list"></i> Input Kondisi</b-dropdown-item>
                        <b-dropdown-item href="javascript:" @click="editData(row)"><i class="fas fa-edit"></i> Edit</b-dropdown-item>
                        <b-dropdown-item href="javascript:" @click="deleteData(row.item.ruang_id)"><i class="fas fa-trash"></i> Hapus</b-dropdown-item>
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
        <b-modal id="modal-xl" size="xl" v-model="showModal" title="Detil Ruang">
            <table class="table table-bordered table-striped">
                <tr>
                    <td width="30%">Sekolah</td>
                    <td width="70%">: {{(modalText.bangunan) ? (modalText.bangunan.tanah) ? (modalText.bangunan.tanah.sekolah) ? modalText.bangunan.tanah.sekolah.nama : '-' : '-' : '-'}}</td>
                </tr>
                <tr>
                    <td>Bangunan</td>
                    <td>: {{(modalText.bangunan) ? modalText.bangunan.nama : '-'}}</td>
                </tr>
                <tr>
                    <td>Jenis Prasarana</td>
                    <td>: {{(modalText.jenis_prasarana) ? modalText.jenis_prasarana.nama : '-'}}</td>
                </tr>
                <tr>
                    <td>Kode Ruang</td>
                    <td>: {{modalText.kode}}</td>
                </tr>
                <tr>
                    <td>Nama Ruang</td>
                    <td>: {{modalText.nama}}</td>
                </tr>
                <tr>
                    <td>Tahun Bangun</td>
                    <td>: {{modalText.tahun_bangun}}</td>
                </tr>
                <tr>
                    <td>Tahun Renovasi</td>
                    <td>: {{modalText.tahun_renovasi}}</td>
                </tr>
                <tr>
                    <td>Lantai Ke-</td>
                    <td>: {{modalText.lantai_ke}}</td>
                </tr>
                <tr>
                    <td>Panjang Ruang</td>
                    <td>: {{modalText.panjang}} m</td>
                </tr>
                <tr>
                    <td>Lebar Ruang</td>
                    <td>: {{modalText.lebar}} m</td>
                </tr>
                <tr>
                    <td>Luas Ruang</td>
                    <td>: {{modalText.luas}} m<sup>2</sup></td>
                </tr>
                <tr>
                    <td>Luas Plester</td>
                    <td>: {{modalText.luas_plester}} m<sup>2</sup></td>
                </tr>
                <tr>
                    <td>Luas Plafon</td>
                    <td>: {{modalText.luas_plafon}} m<sup>2</sup></td>
                </tr>
                <tr>
                    <td>Luas Dinding</td>
                    <td>: {{modalText.luas_dinding}} m<sup>2</sup></td>
                </tr>
                <tr>
                    <td>Luas Daun Jendela</td>
                    <td>: {{modalText.luas_daun_jendela}} m<sup>2</sup></td>
                </tr>
                <tr>
                    <td>Luas Daun Pintu</td>
                    <td>: {{modalText.luas_daun_pintu}} m<sup>2</sup></td>
                </tr>
                <tr>
                    <td>Luas Kusen</td>
                    <td>: {{modalText.luas_kusen}} m<sup>2</sup></td>
                </tr>
                <tr>
                    <td>Luas Tutup Lantai</td>
                    <td>: {{modalText.luas_tutup_lantai}} m<sup>2</sup></td>
                </tr>
                <tr>
                    <td>Jumlah Instalasi Listrik</td>
                    <td>: {{modalText.jumlah_instalasi_listrik}}</td>
                </tr>
                <tr>
                    <td>Panjang Instalasi Air</td>
                    <td>: {{modalText.panjang_instalasi_air}} m</td>
                </tr>
                <tr>
                    <td>Jumlah Instalasi Air</td>
                    <td>: {{modalText.jumlah_instalasi_air}}</td>
                </tr>
                <tr>
                    <td>Panjang Drainase</td>
                    <td>: {{modalText.panjang_drainase}} m</td>
                </tr>
                <tr>
                    <td>Luas Finish Struktur</td>
                    <td>: {{modalText.luas_finish_struktur}} m<sup>2</sup></td>
                </tr>
                <tr>
                    <td>Luas Finish Plafon</td>
                    <td>: {{modalText.luas_finish_plafon}} m<sup>2</sup></td>
                </tr>
                <tr>
                    <td>Luas Finish Dinding</td>
                    <td>: {{modalText.luas_finish_dinding}} m<sup>2</sup></td>
                </tr>
                <tr>
                    <td>Luas Finish Kusen/Pintu/Jendela</td>
                    <td>: {{modalText.luas_finish_kpj}} m<sup>2</sup></td>
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
                    <h5 class="modal-title">Perbaharui Data Ruang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="updateData()">
                    <div class="modal-body">
                        <div class="form-group">
                            <input v-model="form.id" type="hidden" name="id" class="form-control" :class="{ 'is-invalid': form.errors.has('id') }">
                            <label>Sekolah</label>
                            <v-select label="nama" :options="data_sekolah" v-model="form.sekolah_id" @input="updateBangunan" />
                            <has-error :form="form" field="sekolah_id"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Bangunan</label>
                            <v-select label="nama" :options="data_bangunan" v-model="form.bangunan_id" @input="updateJenis" />
                            <has-error :form="form" field="bangunan_id"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Jenis Ruang</label>
                            <v-select label="nama" :options="data_jenis" v-model="form.jenis_prasarana_id" />
                            <has-error :form="form" field="jenis_prasarana_id"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Kode Ruang</label>
                            <input v-model="form.kode" type="text" name="kode" class="form-control" :class="{ 'is-invalid': form.errors.has('kode') }">
                            <has-error :form="form" field="kode"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Nama Ruang</label>
                            <input v-model="form.nama" type="text" name="nama" class="form-control" :class="{ 'is-invalid': form.errors.has('nama') }">
                            <has-error :form="form" field="nama"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Tahun Bangun</label><br>
                            <date-picker v-model="form.tahun_bangun" type="year" :class="{ 'is-invalid': form.errors.has('tahun_bangun') }"></date-picker>
                            <has-error :form="form" field="tahun_bangun"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Tahun Terakhir direnovasi</label><br>
                            <date-picker v-model="form.tahun_renovasi" type="year" :class="{ 'is-invalid': form.errors.has('tahun_renovasi') }"></date-picker>
                            <has-error :form="form" field="tahun_renovasi"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Lantai Ke-</label>
                            <input v-model="form.lantai_ke" type="text" name="lebar" class="form-control" :class="{ 'is-invalid': form.errors.has('lantai_ke') }">
                            <has-error :form="form" field="lantai_ke"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Panjang (m)</label>
                            <input v-model="form.panjang" type="text" name="panjang" class="form-control" :class="{ 'is-invalid': form.errors.has('panjang') }">
                            <has-error :form="form" field="panjang"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Lebar (m)</label>
                            <input v-model="form.lebar" type="text" name="lebar" class="form-control" :class="{ 'is-invalid': form.errors.has('lebar') }">
                            <has-error :form="form" field="lebar"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Luas (m<sup>2</sup>)</label>
                            <input v-model="form.luas" type="text" name="luas" class="form-control" :class="{ 'is-invalid': form.errors.has('luas') }">
                            <has-error :form="form" field="luas"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Luas Plester (m<sup>2</sup>)</label>
                            <input v-model="form.luas_plester" type="text" name="luas_plester" class="form-control" :class="{ 'is-invalid': form.errors.has('luas_plester') }">
                            <has-error :form="form" field="luas_plester"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Luas Plafon (m<sup>2</sup>)</label>
                            <input v-model="form.luas_plafon" type="text" name="luas_plafon" class="form-control" :class="{ 'is-invalid': form.errors.has('luas_plafon') }">
                            <has-error :form="form" field="luas_plafon"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Luas dinding (m<sup>2</sup>)</label>
                            <input v-model="form.luas_dinding" type="text" name="luas_dinding" class="form-control" :class="{ 'is-invalid': form.errors.has('luas_dinding') }">
                            <has-error :form="form" field="luas_dinding"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Luas Daun Jendela (m<sup>2</sup>)</label>
                            <input v-model="form.luas_daun_jendela" type="text" name="luas_daun_jendela" class="form-control" :class="{ 'is-invalid': form.errors.has('luas_daun_jendela') }">
                            <has-error :form="form" field="luas_daun_jendela"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Luas Daun Pintu (m<sup>2</sup>)</label>
                            <input v-model="form.luas_daun_pintu" type="text" name="luas_daun_pintu" class="form-control" :class="{ 'is-invalid': form.errors.has('luas_daun_pintu') }">
                            <has-error :form="form" field="luas_daun_pintu"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Panjang Kusen (m<sup>2</sup>)</label>
                            <input v-model="form.luas_kusen" type="text" name="luas_kusen" class="form-control" :class="{ 'is-invalid': form.errors.has('luas_kusen') }">
                            <has-error :form="form" field="luas_kusen"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Luas Tutup Lantai (m<sup>2</sup>)</label>
                            <input v-model="form.luas_tutup_lantai" type="text" name="luas_tutup_lantai" class="form-control" :class="{ 'is-invalid': form.errors.has('luas_tutup_lantai') }">
                            <has-error :form="form" field="luas_tutup_lantai"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Jumlah Instalasi Listrik</label>
                            <input v-model="form.jumlah_instalasi_listrik" type="text" name="jumlah_instalasi_listrik" class="form-control" :class="{ 'is-invalid': form.errors.has('jumlah_instalasi_listrik') }">
                            <has-error :form="form" field="jumlah_instalasi_listrik"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Panjang Instalasi Air (m)</label>
                            <input v-model="form.panjang_instalasi_air" type="text" name="panjang_instalasi_air" class="form-control" :class="{ 'is-invalid': form.errors.has('panjang_instalasi_air') }">
                            <has-error :form="form" field="panjang_instalasi_air"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Jumlah Instalasi Air</label>
                            <input v-model="form.jumlah_instalasi_air" type="text" name="jumlah_instalasi_air" class="form-control" :class="{ 'is-invalid': form.errors.has('jumlah_instalasi_air') }">
                            <has-error :form="form" field="jumlah_instalasi_air"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Panjang Drainase (m)</label>
                            <input v-model="form.panjang_drainase" type="text" name="panjang_drainase" class="form-control" :class="{ 'is-invalid': form.errors.has('panjang_drainase') }">
                            <has-error :form="form" field="panjang_drainase"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Luas Finish Struktur (m<sup>2</sup>)</label>
                            <input v-model="form.luas_finish_struktur" type="text" name="luas_finish_struktur" class="form-control" :class="{ 'is-invalid': form.errors.has('luas_finish_struktur') }">
                            <has-error :form="form" field="luas_finish_struktur"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Luas Finish Plafon (m<sup>2</sup>)</label>
                            <input v-model="form.luas_finish_plafon" type="text" name="luas_finish_plafon" class="form-control" :class="{ 'is-invalid': form.errors.has('luas_finish_plafon') }">
                            <has-error :form="form" field="luas_finish_plafon"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Luas Finish Dinding (m<sup>2</sup>) </label>
                            <input v-model="form.luas_finish_dinding" type="text" name="luas_finish_dinding" class="form-control" :class="{ 'is-invalid': form.errors.has('luas_finish_dinding') }">
                            <has-error :form="form" field="luas_finish_dinding"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Luas Finish KPJ (m<sup>2</sup>) (Kusen, Pintu, Jendela)</label>
                            <input v-model="form.luas_finish_kpj" type="text" name="luas_finish_kpj" class="form-control" :class="{ 'is-invalid': form.errors.has('luas_finish_kpj') }">
                            <has-error :form="form" field="luas_finish_kpj"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <input v-model="form.keterangan" type="text" name="keterangan" class="form-control" :class="{ 'is-invalid': form.errors.has('keterangan') }">
                            <has-error :form="form" field="keterangan"></has-error>
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
                    <h5 class="modal-title">Input Kondisi Ruang</h5>
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
                                        <input v-model="form.kerusakan_bangunan" type="hidden" name="kerusakan_bangunan">
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
                        <input v-model="form.bangunan_id" type="hidden" name="ruang_id" class="form-control" :class="{ 'is-invalid': form.errors.has('ruang_id') }">
                        <input v-model="form.luas_daun_jendela" type="hidden" name="luas_daun_jendela" class="form-control">
                        <input v-model="form.luas_kusen" type="hidden" name="luas_kusen" class="form-control">
                        <input v-model="form.luas_daun_pintu" type="hidden" name="luas_daun_pintu" class="form-control">
                        <input v-model="form.luas_plafon" type="hidden" name="luas_plafon" class="form-control">
                        <input v-model="form.luas_dinding" type="hidden" name="luas_dinding" class="form-control">
                        <input v-model="form.luas_tutup_lantai" type="hidden" name="luas_tutup_lantai" class="form-control">
                        <input v-model="form.jumlah_instalasi_listrik" type="hidden" name="jumlah_instalasi_listrik" class="form-control">
                        <input v-model="form.panjang_instalasi_air" type="hidden" name="panjang_instalasi_air" class="form-control">
                        <input v-model="form.jumlah_instalasi_air" type="hidden" name="jumlah_instalasi_air" class="form-control">
                        <input v-model="form.panjang_drainase" type="hidden" name="panjang_drainase" class="form-control">
                        <input v-model="form.luas_finish_struktur" type="hidden" name="luas_finish_struktur" class="form-control">
                        <input v-model="form.luas_finish_plafon" type="hidden" name="luas_finish_plafon" class="form-control">
                        <input v-model="form.luas_finish_dinding" type="hidden" name="luas_finish_dinding" class="form-control">
                        <input v-model="form.luas_finish_kpj" type="hidden" name="luas_finish_kpj" class="form-control">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Kerusakan Batu Bata/Partisi (%)</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.rusak_bata_dinding" type="text" name="rusak_bata_dinding" @input="getTotal"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('rusak_bata_dinding') }">
                                        <has-error :form="form" field="rusak_bata_dinding"></has-error>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Keterangan Batu Bata/Partisi</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.ket_bata_dinding" type="text" name="ket_bata_dinding" 
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('ket_bata_dinding') }">
                                        <has-error :form="form" field="ket_bata_dinding"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Kerusakan Kaca (%)</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.rusak_daun_jendela" type="text" name="rusak_daun_jendela" @input="getTotal"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('rusak_daun_jendela') }">
                                        <has-error :form="form" field="rusak_daun_jendela"></has-error>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Keterangan Kaca</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.ket_daun_jendela" type="text" name="ket_daun_jendela"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('ket_daun_jendela') }">
                                        <has-error :form="form" field="ket_daun_jendela"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Kerusakan Pintu (%)</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.rusak_daun_pintu" type="text" name="rusak_daun_pintu" @input="getTotal"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('rusak_daun_pintu') }">
                                        <has-error :form="form" field="rusak_daun_pintu"></has-error>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Keterangan Pintu</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.ket_daun_pintu" type="text" name="ket_daun_pintu"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('ket_daun_pintu') }">
                                        <has-error :form="form" field="ket_daun_pintu"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Kerusakan Kusen (%)</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.rusak_kusen" type="text" name="rusak_kusen" @input="getTotal"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('rusak_kusen') }">
                                        <has-error :form="form" field="rusak_kusen"></has-error>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Keterangan Kusen</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.ket_kusen" type="text" name="ket_kusen" 
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('ket_kusen') }">
                                        <has-error :form="form" field="ket_kusen"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Kerusakan Plafon (%)</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.rusak_tutup_plafon" type="text" name="rusak_tutup_plafon" @input="getTotal"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('rusak_tutup_plafon') }">
                                        <has-error :form="form" field="rusak_tutup_plafon"></has-error>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Keterangan Plafon</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.ket_tutup_plafon" type="text" name="ket_tutup_plafon"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('ket_tutup_plafon') }">
                                        <has-error :form="form" field="ket_tutup_plafon"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Kerusakan Penutup Lantai (%)</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.rusak_tutup_lantai" type="text" name="rusak_tutup_lantai" @input="getTotal"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('rusak_tutup_lantai') }">
                                        <has-error :form="form" field="rusak_tutup_lantai"></has-error>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Keterangan Penutup Lantai</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.ket_penutup_lantai" type="text" name="ket_penutup_lantai" 
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('ket_penutup_lantai') }">
                                        <has-error :form="form" field="ket_penutup_lantai"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Klasifikasi Kerusakan Instalasi Listrik</label>
                                    <div class="col-sm-9">
                                        <v-select :options="data_listrik" v-model="form.ket_inst_listrik" @input="updateListrik" />
                                        <has-error :form="form" field="ket_inst_listrik"></has-error>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Kerusakan Instalasi Listrik (%)</label>
                                    <div class="col-sm-9">
                                        <input v-model="form.rusak_inst_listrik" type="text" name="rusak_inst_listrik" readonly
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('rusak_inst_listrik') }">
                                        <has-error :form="form" field="rusak_inst_listrik"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Klasifikasi Kerusakan Instalasi Air</label>
                                    <div class="col-sm-9">
                                        <v-select :options="data_air" v-model="form.ket_inst_air" @input="updateAir" />
                                        <has-error :form="form" field="ket_inst_air"></has-error>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Kerusakan Instalasi Air (%)</label>
                                    <div class="col-sm-9">
                                        <input v-model="form.rusak_inst_air" type="text" name="rusak_inst_air" readonly
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('rusak_inst_air') }">
                                        <has-error :form="form" field="rusak_inst_air"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Kerusakan Drainase Limbah (%)</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.rusak_drainase" type="text" name="rusak_drainase" @input="getTotal"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('rusak_drainase') }">
                                        <has-error :form="form" field="rusak_drainase"></has-error>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Keterangan Drainase Limbah</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.ket_drainase" type="text" name="ket_drainase"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('ket_drainase') }">
                                        <has-error :form="form" field="ket_drainase"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Kerusakan Finishing Langit-langit (%)</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.rusak_finish_plafon" type="text" name="rusak_finish_plafon" @input="getTotal"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('rusak_finish_plafon') }">
                                        <has-error :form="form" field="rusak_finish_plafon"></has-error>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Keterangan Finishing Langit-langit</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.ket_finish_plafon" type="text" name="ket_finish_plafon" 
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('ket_finish_plafon') }">
                                        <has-error :form="form" field="ket_finish_plafon"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Kerusakan Finishing Dinding (%)</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.rusak_finish_dinding" type="text" name="rusak_finish_dinding" @input="getTotal"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('rusak_finish_dinding') }">
                                        <has-error :form="form" field="rusak_finish_dinding"></has-error>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Keterangan Finishing Dinding</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.ket_finish_dinding" type="text" name="ket_finish_dinding"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('ket_finish_dinding') }">
                                        <has-error :form="form" field="ket_finish_dinding"></has-error>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Kerusakan Finishing Kusen/Pintu/Jendela (%)</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.rusak_finish_kpj" type="text" name="rusak_finish_kpj" @input="getTotal"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('rusak_finish_kpj') }">
                                        <has-error :form="form" field="rusak_finish_kpj"></has-error>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Keterangan Finishing Kusen/Pintu/Jendela</label>
                                    <div class="col-sm-6">
                                        <input v-model="form.ket_finish_kpj" type="text" name="ket_finish_kpj"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('ket_finish_kpj') }">
                                        <has-error :form="form" field="ket_finish_kpj"></has-error>
                                    </div>
                                </div>
                            </div>
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
                                        <b-form-file v-model="foto.file" :state="Boolean(foto.file)" accept=".jpg, .png, .jpeg" placeholder="Choose a file or drop it here..." drop-placeholder="Drop file here..." ></b-form-file>
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
            foto_ruang_id: '',
            presentase_kerusakan: '0%',
            kriteria_kerusakan : '-',
            editmode: false,
            //VARIABLE INI AKAN MENGHADLE SORTING DATA
            sortBy: null, //FIELD YANG AKAN DISORT AKAN OTOMATIS DISIMPAN DISINI
            sortDesc: false, //SEDANGKAN JENISNYA ASCENDING ATAU DESC AKAN DISIMPAN DISINI
            //TAMBAHKAN DUA VARIABLE INI UNTUK MENGHANDLE MODAL DAN DATA YANG AKAN DIHAPUS
            deleteModal: false,
            showModal: false,
            editModal: false,
            modalText: '',
            selected: null,
            form: new Form({
                rusak_bata_dinding: 0,
                ket_bata_dinding: '-',
                ruang_id: '',
                rusak_bata_dinding: 0,
                ket_bata_dinding: '-',
                rusak_daun_jendela: 0,
                ket_daun_jendela: '-',
                rusak_daun_pintu: 0,
                ket_daun_pintu: '-',
                rusak_kusen: 0,
                ket_kusen: '-',
                rusak_tutup_plafon: 0,
                ket_tutup_plafon: '-',
                rusak_tutup_lantai: 0,
                ket_penutup_lantai: '-',
                ket_inst_listrik: 'Tidak ada kerusakan',
                rusak_inst_listrik: 0,
                ket_inst_air: 'Tidak ada kerusakan',
                rusak_inst_air: 0,
                rusak_drainase: 0,
                ket_drainase: '-',
                rusak_finish_plafon: 0,
                ket_finish_plafon: '-',
                rusak_finish_dinding: 0,
                ket_finish_dinding	: '-',
                rusak_finish_kpj: 0,
                ket_finish_kpj: '-',
                //edit
                id: '',
                sekolah_id: '',
                jenis_prasarana_id: '',
                bangunan_id: '',
                kode: '',
                nama: '',
                tahun_bangun: '',
                tahun_renovasi: '',
                lantai_ke: 1,
                panjang: 0,
                lebar: 0,
                luas: 0,
                luas_plester: 0,
                luas_plafon: 0,
                luas_dinding: 0,
                luas_daun_jendela: 0,
                luas_daun_pintu: 0,
                luas_kusen: 0,
                luas_tutup_lantai: 0,
                jumlah_instalasi_listrik: 0,
                panjang_instalasi_air: 0,
                jumlah_instalasi_air: 0,
                panjang_drainase: 0,
                luas_finish_struktur: 0,
                luas_finish_plafon: 0,
                luas_finish_dinding: 0,
                luas_finish_kpj: 0,
                keterangan: '',
                kerusakan_bangunan: 0,
                presentase_kerusakan: 0,
            }),
            data_listrik: [
                {label: 'Tidak ada kerusakan', code: 0},
                {label: 'Sebagian kecil komponen dari panel-panel LP rusak, ada sedkit jalur kabel instalasi shortage, sebagian kecil armature rusak ringan, sehingga biaya perbaikan kurang dari 5% dari biaya instalasi baru', code: 1},
                {label: 'Beberapa komponen dari panel-panel LP rusak, sebagian kecil jalur kabel instalasi shortage, sehingga armature rusak ringan, sehingga  biaya perbaikan 5-20% dari biaya instalasi baru', code: 2},
                {label: 'Beberapa komponen dari panel-panel LP rusak, sebagian kecil jalur kabel instalasi shortage, sehingga armature rusak ringan hingga berat, sehingga  biaya perbaikan 20-50% dari biaya instalasi baru', code: 3},
                {label: 'Sebagian besar komponen panel-panel LP rusak, sebagian besar kabel instalasi shortage, sebagian besar armature rusak berat, sehingga biaya perbaikan lebih  dari 50 % dari instalasi baru', code: 4},
                {label: 'Sebagian besar komponen panel-panel LP rusak, sebagian besar kabel instalasi shortage, seluruh armature rusak berat, sehingga biaya perbaikan lebih dari 50 % dari instalasi baru', code: 5},
            ],
            data_air: [
                {label: 'Tidak ada kerusakan', code: 0},
                {label: 'Kebocoran pipa terbatas ditempat yang terlihat atau mudah dicapai, keran-keran kecil rusak, sehingga biaya perbaikan kurang dari 1 % biaya instalasi baru', code: '0.3'},
                {label: 'Bagian-bagian kecil pemipaan bocor, motor pompa terbakar, keran-keran kecil rusak, sehingga biaya perbaikan antara 1-10% dari biaya instalasi baru', code: '0.6'},
                {label: 'Pompa, motor, pipa, dan keran rusak apabuila diganti atau diperbaiki memerlukan biaya antara 10-25 % dari biaya instalasi baru', code: '0.9'},
                {label: 'Sebagian besar pompa, sebagian besar motor terbakar, pipa utama bocor namun ditempat terbuka, beberapa keran tidak befungsi, sehingga biaya perbaikan 25- 50 % dari biaya instalasi baru', code: '1.2'},
                {label: 'Pompa –pompa rusak total, motor terbakar, dibanyak tempat terbuka dan tutup pipa-pipa bocor, keran-keran tidak berfungsi, sehingga perbaikan instalasi perlu menyeluruh, dengan perkiraan biaya lebih dari 50% dari biaya instalasi baru', code: '1.5'},
            ],
            data_sekolah: [],
            data_tanah: [],
            data_bangunan: [],
            data_jenis: [],
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
            var foto_ruang_id = this.foto_ruang_id
            $.each(this.fotos, function (key, value) {
                console.log(value.file);
                let formData = new FormData();
                formData.append('file', value.file);
                formData.append('ruang_id', foto_ruang_id);
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
                })
            })
            $('#modalUpload').modal('hide');
            this.loadPerPage(10);
        },
        uploadFoto(ruang_id){
            this.foto_ruang_id = ruang_id
            this.addNewApartment()
            $('#modalUpload').modal('show');
        },
        updateListrik(val){
            this.form.rusak_inst_listrik = val.code
            this.getTotal()
        },
        updateAir(val){
            this.form.rusak_inst_air = val.code
            this.getTotal()
        },
        getTotal(){
            let getTotalKaca = this.getTotalKaca()
            let kerusakanKaca = Number(this.number_format(this.form.rusak_daun_jendela,2)) + Number(this.number_format(this.form.rusak_daun_pintu,2)) + Number(this.number_format(this.form.rusak_kusen,2))
            let totalKerusakanKaca = (kerusakanKaca / getTotalKaca) * 100 / 100
            let kerusakanPlafon = (Number(this.number_format(this.form.rusak_tutup_plafon,2)) / this.form.luas_plafon) * 100 / 100
            let kerusakanLantai = (Number(this.number_format(this.form.rusak_tutup_lantai,2)) / this.form.luas_tutup_lantai) * 100 / 100
            let getTotalUtilitas = this.getTotalUtilitas()
            let kerusakanUtilitas = Number(this.form.rusak_inst_listrik) + Number(this.form.rusak_inst_air) + Number(this.form.rusak_drainase)
            let totalKerusakanUtilitas = (kerusakanUtilitas / getTotalUtilitas) * 100 / 100
            let getTotalFinishing = this.getTotalFinishing()
            let kerusakanFinishing = Number(this.number_format(this.form.rusak_finish_plafon,2)) + Number(this.number_format(this.form.rusak_finish_dinding,2)) + Number(this.number_format(this.form.rusak_finish_kpj,2))
            let totalKerusakanFinishing = (kerusakanFinishing / getTotalFinishing) * 100 / 100
            let kerusakan_ruang = Number(this.number_format(totalKerusakanKaca,2)) + Number(this.number_format(this.form.rusak_bata_dinding,2)) + Number(this.number_format(kerusakanPlafon,2)) + Number(this.number_format(kerusakanLantai,2)) + Number(this.number_format(totalKerusakanUtilitas,2)) + Number(this.number_format(totalKerusakanFinishing,2))
            let total_kerusakan = Number(this.number_format(this.form.kerusakan_bangunan,2)) + Number(this.number_format(kerusakan_ruang,2))
            let make_kriteria = null
            if(total_kerusakan == 0){
                make_kriteria = 'BAIK'
            } else if(total_kerusakan > 0 && total_kerusakan <= 30){
                make_kriteria = 'RINGAN'
            } else if(total_kerusakan >= 31 && total_kerusakan <= 45){
                make_kriteria = 'SEDANG'
            } else if(total_kerusakan >= 46 && total_kerusakan <= 65){
                make_kriteria = 'BERAT'
            } else if(total_kerusakan > 66){
                make_kriteria = 'SANGAT BERAT'
            }
            this.kriteria_kerusakan = make_kriteria
            this.presentase_kerusakan = this.number_format(total_kerusakan,2)+'%'
            this.form.presentase_kerusakan = this.number_format(total_kerusakan,2)
        },
        getTotalKaca(){
            let jmlJendela = Number(this.form.luas_daun_jendela) + Number(this.form.luas_daun_pintu) + Number(this.form.luas_kusen)
            return jmlJendela
        },
        getTotalUtilitas(){
            let jmlUtilitas = Number(this.form.jumlah_instalasi_listrik) + Number(this.form.panjang_instalasi_air) + Number(this.form.jumlah_instalasi_air) + Number(this.form.panjang_drainase)
            return jmlUtilitas
        },
        getTotalFinishing(){
            let jmlFinishing = Number(this.form.luas_finish_struktur) + Number(this.form.luas_finish_plafon) + Number(this.form.luas_finish_dinding) + Number(this.form.luas_finish_kpj)
            return jmlFinishing
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
                    return fetch('/api/referensi/delete-ruang/'+id, {
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
        updateBangunan(data){
            this.form.bangunan_id = {bangunan_id: '', nama: '== Pilih Bangunan =='}
            this.form.jenis_prasarana_id = {id: '', nama: '== Pilih Jenis Ruang =='}
            axios.get(`/api/referensi/all-bangunan`, {
                //KIRIMKAN PARAMETER BERUPA PAGE YANG SEDANG DILOAD, PENCARIAN, LOAD PERPAGE DAN SORTING.
                params: {
                    sekolah_id: data.sekolah_id,
                }
            })
            .then((response) => {
                let getData = response.data.data
                this.data_bangunan = getData
            })
        },
        updateJenis(){
            this.form.jenis_prasarana_id = {id: '', nama: '== Pilih Jenis Ruang =='}
            axios.get(`/api/referensi/all-jenis-prasarana`, {
                //KIRIMKAN PARAMETER BERUPA PAGE YANG SEDANG DILOAD, PENCARIAN, LOAD PERPAGE DAN SORTING.
                params: {
                    query: 'a_ruang',
                }
            })
            .then((response) => {
                let getData = response.data.data
                this.data_jenis = getData
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
        openShowModal(row) {
            this.showModal = true
            this.modalText = row.item
        },
        editData(row) {
            this.getSekolah();
            let getData = row.item
            this.editmode = true
            this.editModal = true
            this.form.id = getData.ruang_id
            this.form.sekolah_id = {sekolah_id: getData.bangunan.tanah.sekolah_id, nama: getData.bangunan.tanah.sekolah.nama}
            this.form.bangunan_id = {bangunan_id: getData.bangunan_id, nama: getData.bangunan.nama}
            this.form.jenis_prasarana_id = {id: getData.jenis_prasarana_id, nama: getData.jenis_prasarana.nama}
            this.form.kode = getData.kode
            this.form.nama = getData.nama
            let tahunBangun = moment(String(getData.tahun_bangun))
            let tahunRenovasi = moment(String(getData.tahun_renovasi))
            this.form.tahun_bangun = tahunBangun._d
            this.form.tahun_renovasi = tahunRenovasi._d
            this.form.lantai_ke = getData.lantai_ke
            this.form.panjang = getData.panjang
            this.form.lebar = getData.lebar
            this.form.luas = getData.luas
            this.form.luas_plester = getData.luas_plester
            this.form.luas_plafon = getData.luas_plafon
            this.form.luas_dinding = getData.luas_dinding
            this.form.luas_daun_jendela = getData.luas_daun_jendela
            this.form.luas_daun_pintu = getData.luas_daun_pintu
            this.form.luas_kusen = getData.luas_kusen
            this.form.luas_tutup_lantai = getData.luas_tutup_lantai
            this.form.jumlah_instalasi_listrik = getData.jumlah_instalasi_listrik
            this.form.panjang_instalasi_air = getData.panjang_instalasi_air
            this.form.jumlah_instalasi_air = getData.jumlah_instalasi_air
            this.form.panjang_drainase = getData.panjang_drainase
            this.form.luas_finish_struktur = getData.luas_finish_struktur
            this.form.luas_finish_plafon = getData.luas_finish_plafon
            this.form.luas_finish_dinding = getData.luas_finish_dinding
            this.form.luas_finish_kpj = getData.luas_finish_kpj
            this.form.keterangan = getData.keterangan
            $('#modalEdit').modal('show');
        },
        inputKondisi(row){
            this.editmode = true
            this.form.ruang_id = row.item.ruang_id
            axios.get(`/api/kondisi/ruang`, {
                //KIRIMKAN PARAMETER BERUPA PAGE YANG SEDANG DILOAD, PENCARIAN, LOAD PERPAGE DAN SORTING.
                params: {
                    ruang_id: row.item.ruang_id,
                }
            })
            .then((response) => {
                console.log(response);
                //JIKA RESPONSENYA DITERIMA
                let getDataRuang = response.data.data
                this.form.luas_daun_jendela = getDataRuang.luas_daun_jendela
                this.form.luas_daun_pintu = getDataRuang.luas_daun_pintu
                this.form.luas_kusen = getDataRuang.luas_kusen
                this.form.luas_plafon = getDataRuang.luas_plafon
                this.form.luas_dinding = getDataRuang.luas_dinding
                this.form.luas_tutup_lantai = getDataRuang.luas_tutup_lantai
                this.form.jumlah_instalasi_listrik = getDataRuang.jumlah_instalasi_listrik
                this.form.panjang_instalasi_air = getDataRuang.panjang_instalasi_air
                this.form.jumlah_instalasi_air = getDataRuang.jumlah_instalasi_air
                this.form.panjang_drainase = getDataRuang.panjang_drainase
                this.form.luas_finish_struktur = getDataRuang.luas_finish_struktur
                this.form.luas_finish_plafon = getDataRuang.luas_finish_plafon
                this.form.luas_finish_dinding = getDataRuang.luas_finish_dinding
                this.form.luas_finish_kpj = getDataRuang.luas_finish_kpj
                let getData = getDataRuang.kondisi_ruang
                this.form.ruang_id = getDataRuang.ruang_id
                //let kerusakan_bangunan = Number(this.number_format(getDataRuang.bangunan.total_kerusakan, 2))
                this.form.kerusakan_bangunan = getDataRuang.bangunan.total_kerusakan
                if(getData){
                    this.form.rusak_bata_dinding = this.number_format(getData.rusak_bata_dinding)
                    this.form.ket_bata_dinding = getData.ket_bata_dinding
                    this.form.rusak_daun_jendela = this.number_format(getData.rusak_daun_jendela)
                    this.form.ket_daun_jendela = getData.ket_daun_jendela
                    this.form.rusak_daun_pintu = this.number_format(getData.rusak_daun_pintu)
                    this.form.ket_daun_pintu = getData.ket_daun_pintu
                    this.form.rusak_kusen = this.number_format(getData.rusak_kusen)
                    this.form.ket_kusen = getData.ket_kusen
                    this.form.rusak_tutup_plafon = this.number_format(getData.rusak_tutup_plafon)
                    this.form.ket_tutup_plafon = getData.ket_tutup_plafon
                    this.form.rusak_tutup_lantai = this.number_format(getData.rusak_tutup_lantai)
                    this.form.ket_penutup_lantai = getData.ket_penutup_lantai
                    this.form.rusak_inst_listrik = getData.rusak_inst_listrik
                    this.form.ket_inst_listrik = getData.ket_inst_listrik
                    this.form.rusak_inst_air = getData.rusak_inst_air
                    this.form.ket_inst_air = getData.ket_inst_air
                    this.form.rusak_drainase = this.number_format(getData.rusak_drainase)
                    this.form.ket_drainase = getData.ket_drainase
                    this.form.rusak_finish_plafon = this.number_format(getData.rusak_finish_plafon)
                    this.form.ket_finish_plafon = getData.ket_finish_plafon
                    this.form.rusak_finish_dinding = this.number_format(getData.rusak_finish_dinding)
                    this.form.ket_finish_dinding = getData.ket_finish_dinding	
                    this.form.rusak_finish_kpj = this.number_format(getData.rusak_finish_kpj)
                    this.form.ket_finish_kpj = getData.ket_finish_kpj
                    /*let kerusakan_ruang = Number(this.number_format(this.form.rusak_bata_dinding,2)) + Number(this.number_format(this.form.rusak_daun_jendela,2)) + Number(this.number_format(this.form.rusak_daun_pintu,2)) + Number(this.number_format(this.form.rusak_kusen,2)) + Number(this.number_format(this.form.rusak_tutup_plafon,2)) + Number(this.number_format(this.form.rusak_tutup_lantai,2)) + Number(this.number_format(this.form.rusak_inst_listrik,2)) + Number(this.number_format(this.form.rusak_inst_air,2)) + Number(this.number_format(this.form.rusak_drainase,2)) + Number(this.number_format(this.form.rusak_finish_plafon,2)) + Number(this.number_format(this.form.rusak_finish_dinding,2)) + Number(this.number_format(this.form.rusak_finish_kpj,2))
                    let total_kerusakan = Number(this.number_format(this.form.kerusakan_bangunan,2)) + Number(this.number_format(kerusakan_ruang,2))
                    this.presentase_kerusakan = this.number_format(total_kerusakan,2)
                    let make_kriteria = null
                    if(total_kerusakan == 0){
                        make_kriteria = 'BAIK'
                    } else if(total_kerusakan > 0 && total_kerusakan <= 30){
                        make_kriteria = 'RINGAN'
                    } else if(total_kerusakan >= 31 && total_kerusakan <= 45){
                        make_kriteria = 'SEDANG'
                    } else if(total_kerusakan >= 46 && total_kerusakan <= 65){
                        make_kriteria = 'BERAT'
                    } else if(total_kerusakan > 66){
                        make_kriteria = 'TOTAL'
                    }
                    this.kriteria_kerusakan = make_kriteria*/
                    let getTotalKaca = this.getTotalKaca()
                    let kerusakanKaca = Number(this.number_format(this.form.rusak_daun_jendela,2)) + Number(this.number_format(this.form.rusak_daun_pintu,2)) + Number(this.number_format(this.form.rusak_kusen,2))
                    let totalKerusakanKaca = (kerusakanKaca / getTotalKaca) * 100 / 100
                    let kerusakanPlafon = (Number(this.number_format(this.form.rusak_tutup_plafon,2)) / this.form.luas_plafon) * 100 / 100
                    let kerusakanLantai = (Number(this.number_format(this.form.rusak_tutup_lantai,2)) / this.form.luas_tutup_lantai) * 100 / 100
                    let getTotalUtilitas = this.getTotalUtilitas()
                    let kerusakanUtilitas = Number(this.form.rusak_inst_listrik) + Number(this.form.rusak_inst_air) + Number(this.form.rusak_drainase)
                    let totalKerusakanUtilitas = (kerusakanUtilitas / getTotalUtilitas) * 100 / 100
                    let getTotalFinishing = this.getTotalFinishing()
                    let kerusakanFinishing = Number(this.number_format(this.form.rusak_finish_plafon,2)) + Number(this.number_format(this.form.rusak_finish_dinding,2)) + Number(this.number_format(this.form.rusak_finish_kpj,2))
                    let totalKerusakanFinishing = (kerusakanFinishing / getTotalFinishing) * 100 / 100
                    let kerusakan_ruang = Number(this.number_format(totalKerusakanKaca,2)) + Number(this.number_format(this.form.rusak_bata_dinding,2)) + Number(this.number_format(kerusakanPlafon,2)) + Number(this.number_format(kerusakanLantai,2)) + Number(this.number_format(totalKerusakanUtilitas,2)) + Number(this.number_format(totalKerusakanFinishing,2))
                    let total_kerusakan = Number(this.number_format(this.form.kerusakan_bangunan,2)) + Number(this.number_format(kerusakan_ruang,2))
                    console.log(this.form.kerusakan_bangunan);
                    let make_kriteria = null
                    if(total_kerusakan == 0){
                        make_kriteria = 'BAIK'
                    } else if(total_kerusakan > 0 && total_kerusakan <= 30){
                        make_kriteria = 'RINGAN'
                    } else if(total_kerusakan >= 31 && total_kerusakan <= 45){
                        make_kriteria = 'SEDANG'
                    } else if(total_kerusakan >= 46 && total_kerusakan <= 65){
                        make_kriteria = 'BERAT'
                    } else if(total_kerusakan > 66){
                        make_kriteria = 'SANGAT BERAT'
                    }
                    this.kriteria_kerusakan = make_kriteria
                    this.presentase_kerusakan = this.number_format(total_kerusakan,2)+'%'
                    this.form.presentase_kerusakan = this.number_format(total_kerusakan,2)
                } else {
                    let total_kerusakan = Number(this.number_format(this.form.kerusakan_bangunan,2))
                    console.log(this.form.kerusakan_bangunan);
                    let make_kriteria = null
                    if(total_kerusakan == 0){
                        make_kriteria = 'BAIK'
                    } else if(total_kerusakan > 0 && total_kerusakan <= 30){
                        make_kriteria = 'RINGAN'
                    } else if(total_kerusakan >= 31 && total_kerusakan <= 45){
                        make_kriteria = 'SEDANG'
                    } else if(total_kerusakan >= 46 && total_kerusakan <= 65){
                        make_kriteria = 'BERAT'
                    } else if(total_kerusakan > 66){
                        make_kriteria = 'SANGAT BERAT'
                    }
                    this.kriteria_kerusakan = make_kriteria
                    this.presentase_kerusakan = this.number_format(total_kerusakan,2)+'%'
                    this.form.presentase_kerusakan = this.number_format(total_kerusakan,2)
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
            this.form.post('/api/kondisi/simpan-ruang').then((response)=>{
                console.log(response);
                $('#modalKondisi').modal('hide');
                Toast.fire({
                    icon: 'success',
                    title: response.status
                });
                this.loadPerPage(10);
            }).catch((e)=>{
                console.log(e);
                Toast.fire({
                    icon: 'error',
                    title: 'Some error occured! Please try again'
                });
            })
        },
        updateData(){
            let id = this.form.id;
            this.form.put('/api/referensi/update-ruang/'+id).then((response)=>{
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