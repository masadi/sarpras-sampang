<template>
    <div>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Pusat Unduhan</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><router-link tag="a" to="/beranda">Beranda</router-link></li>
                            <li class="breadcrumb-item active">Pusat Unduhan</li>
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
                                    <i class="fas fa-download mr-1"></i>
                                    Pusat Unduhan
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-primary btn-lg btn-block" @click="rekapSD">Unduh Rekap SD</button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-success btn-lg btn-block" @click="rekapSMP">Unduh Rekap SMP</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
export default {
    methods: {
        rekapSD(){
            console.log('rekapSD');
            window.open('/rekap/sd', '_blank')
        },
        rekapSMP(){
            console.log('rekapSMP');
            window.open('/rekap/smp', '_blank')
        }
    }
}
</script>
