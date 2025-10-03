<style>
.pesan{
    color: #fff !important;
}
</style>

<div class="row mt-3">
	<div class="col-3">
	</div>
    <div class="col-8">
        <div class="row">
            <!--- KIRI -->
            <div class="col-8">
                <form id="ff" class="needs-validation" novalidate method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-body">
                                <h3 class="header-title">BIODATA</h3>
                                <div class="row">
                                    <!--kiri-->
                                    <div class="col-5">
                                        <div class="form-input mt-2">
                                            <label for="simpleinput" class="form-label">Jenis Kelamin</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
                                                <select class="form-control">
                                                    <option value="Tn">Laki-laki</option>
                                                    <option value="Ny">Perempuan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-input mt-2">
                                            <label for="simpleinput" class="form-label">Tipe Identitas</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1"><i class="far fa-id-card"></i></span>
                                                <select class="form-control">
                                                    <option value="Tn">NIK</option>
                                                    <option value="Ny">Paspor</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-input mt-2">
                                            <label for="simpleinput" class="form-label">Tanggal Keberangkatan</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1"><i class="far fa-calendar-alt"></i></span>
                                                <input type="date" class="form-control" placeholder="Username"
                                                    aria-label="Username" aria-describedby="basic-addon1">
                                            </div>
                                        </div>
                                        <div class="form-input mt-2">
                                            <label for="simpleinput" class="form-label">No.Tlp</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1"><i class=" fas fa-phone-alt"></i></span>
                                                <input type="text" placeholder="08xxx" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!--Kanan-->
                                    <div class="col-7">
                                        <div class="form-input mt-2">
                                            <label for="simpleinput" class="form-label">Nama Lengkap</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
                                                <input type="text" placeholder="Nama sesuai NIK/Paspor" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="form-input mt-2">
                                            <label for="simpleinput" class="form-label">Nomer Identitas</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1"><i class="far fa-id-card"></i></span>
                                                <input type="text" placeholder="No identitas sesuai NIK/Paspor" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="form-input mt-2">
                                            <label for="simpleinput" class="form-label">Email</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1"><i class="far fa-envelope"></i></span>
                                                <input type="email" placeholder="contoh@mail.com" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>               
                </form>
            </div>
        </div>
            <div class="row">
                    <div class="col-8">
                        <div class="card border">
                            <div class="card-body">
                                <h4 class="header-title">Otentikasi</h4>
                                <div class="row mt-2">
                                    <!--Kiri-->
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="password1" class="form-label">Password</label>
                                            <input class="form-control" type="password" required="" id="password1" placeholder="Enter your password">
                                        </div>
                                        <div class="mb-3">
                                            <label for="password1" class="form-label">Konfirmasi Password</label>
                                            <input class="form-control" type="password" required="" id="password1" placeholder="Enter your password">
                                        </div>
                                    </div>                                
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="button-list">
                    <button type="submit" class="btn btn-warning btn-sm waves-effect fw-bold btn-cari fs-5">Register</button>
                </div>
            </div>
    </div>
    <div class="col-2">
	</div>
</div>


