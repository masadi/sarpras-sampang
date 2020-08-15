<?php
$user = auth()->user();
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{url('/')}}" class="brand-link">
    <!--router-link tag="a" to="/dashboard" class="brand-link"-->
        <img src="/images/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">APM SMK</span>
    <!--/router-link-->
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->

                <li class="nav-item">
                    <router-link tag="a" to="/beranda" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Beranda</p>
                    </router-link>
                </li>
                @if($user->isAbleTo('users-create'))
                <li class="nav-item has-treeview">
                    <a class="nav-link" href="">
                        <i class="nav-icon fas fa-list"></i>
                        <p>Referensi
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <router-link tag="a" to="/komponen" class="nav-link">
                                <i class="nav-icon fas fa-hand-point-right"></i>
                                <p>Komponen</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link tag="a" to="/aspek" class="nav-link">
                                <i class="nav-icon fas fa-hand-point-right"></i>
                                <p>Aspek</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link tag="a" to="/atribut" class="nav-link">
                                <i class="nav-icon fas fa-hand-point-right"></i>
                                <p>Atribut</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link tag="a" to="/indikator" class="nav-link">
                                <i class="nav-icon fas fa-hand-point-right"></i>
                                <p>Indikator Kinerja</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link tag="a" to="/instrumen" class="nav-link">
                                <i class="nav-icon fas fa-tasks"></i>
                                <p>Instrumen</p>
                            </router-link>
                        </li>        
                        <li class="nav-item">
                            <router-link tag="a" to="/sekolah" class="nav-link">
                                <i class="nav-icon fas fa-hand-point-right"></i>
                                <p>Sekolah</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link tag="a" to="/pengguna" class="nav-link">
                                <i class="nav-icon fas fa-hand-point-right"></i>
                                <p>Pengguna</p>
                            </router-link>
                        </li>
                    </ul>
                </li>
                @endif
                <!-- New Sidebar Item -->
                <li class="nav-item">
                    <router-link tag="a" to="/kuisioner" class="nav-link">
                        <i class="nav-icon fas fa-check"></i>
                        <p>Isi Kuisioner</p>
                    </router-link>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->

    </div>
    <!-- /.sidebar -->
</aside>