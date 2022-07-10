@extends('layouts.app')
@section('title', 'Beranda')
@section('content')
<main id="main">
    <!-- ======= Services Section ======= -->
    <section id="services" class="services">
      <div class="container">

        <div class="section-title" data-aos="fade-up">
          <h2>Data Sarana</h2>
        </div>
        <div class="row">
          <div class="col-md-4 col-lg-4 d-flex align-items-stretch mb-5 mb-lg-0">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
              <div class="icon"><i class="bx bxl-slack"></i></div>
              <h4 class="title"><a href="">TK</a></h4>
              <p class="description">Terdiri dari  16 Sekolah tingkat TK yang terdata di sistem e-Sarpras</p>
            </div>
          </div>
          <div class="col-md-4 col-lg-4 d-flex align-items-stretch mb-5 mb-lg-0">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="200">
              <div class="icon"><i class="bx bxl-slack"></i></div>
              <h4 class="title"><a href="">SD</a></h4>
              <p class="description">Terdiri dari 517 Sekolah tingkat TK yang terdata di sistem e-Sarpras</p>
            </div>
          </div>
          <div class="col-md-4 col-lg-4 d-flex align-items-stretch mb-5 mb-lg-0">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="300">
              <div class="icon"><i class="bx bxl-slack"></i></div>
              <h4 class="title"><a href="">SMP</a></h4>
              <p class="description">Terdiri dari 51 Sekolah tingkat TK yang terdata di sistem e-Sarpras</p>
            </div>
          </div>
        </div>
      </div>
    </section><!-- End Services Section -->
    <!-- ======= More Services Section ======= -->
    <!-- ======= Features Section ======= -->
    <section id="features" class="features">
      <div class="container">

        <div class="section-title" data-aos="fade-up">
          <h2>Features</h2>
        </div>

        <div class="row" data-aos="fade-up" data-aos-delay="300">
          <div class="col-lg-3 col-md-4">
            <div class="icon-box">
              <i class="ri-store-line" style="color: #ffbb2c;"></i>
              <h3><a href="">Administrator</a></h3>
            </div>
          </div>
          <div class="col-lg-3 col-md-4 mt-4 mt-md-0">
            <div class="icon-box">
              <i class="ri-bar-chart-box-line" style="color: #5578ff;"></i>
              <h3><a href="">Data Luas Tanah</a></h3>
            </div>
          </div>
          <div class="col-lg-3 col-md-4 mt-4 mt-md-0">
            <div class="icon-box">
              <i class="ri-calendar-todo-line" style="color: #e80368;"></i>
              <h3><a href="">Data Gedung Sekolah</a></h3>
            </div>
          </div>
          <div class="col-lg-3 col-md-4 mt-4 mt-lg-0">
            <div class="icon-box">
              <i class="ri-paint-brush-line" style="color: #e361ff;"></i>
              <h3><a href="">Data Ruang Sekolah</a></h3>
            </div>
          </div>
          <div class="col-lg-3 col-md-4 mt-4">
            <div class="icon-box">
              <i class="ri-database-2-line" style="color: #47aeff;"></i>
              <h3><a href="">Data Kerusakan Gedung</a></h3>
            </div>
          </div>
          <div class="col-lg-3 col-md-4 mt-4">
            <div class="icon-box">
              <i class="ri-gradienter-line" style="color: #ffa76e;"></i>
              <h3><a href="">Data Perbaikan Gedung</a></h3>
            </div>
          </div>
          <div class="col-lg-3 col-md-4 mt-4">
            <div class="icon-box">
              <i class="ri-file-list-3-line" style="color: #11dbcf;"></i>
              <h3><a href="">Grafik Kondisi Gedung</a></h3>
            </div>
          </div>
          <div class="col-lg-3 col-md-4 mt-4">
            <div class="icon-box">
              <i class="ri-price-tag-2-line" style="color: #4233ff;"></i>
              <h3><a href="">Pusat Download Data</a></h3>
            </div>
            </div>
        </div>
      </div>
    </section><!-- End Features Section -->
</main><!-- End #main -->
@endsection