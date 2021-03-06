<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $subjudul ?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12 mb-4">
                <a href="<?= base_url() ?>hasiltugas" class="btn btn-flat btn-sm btn-default"><i class="fa fa-arrow-left"></i> Kembali</a>
                <button type="button" onclick="reload_ajax()" class="btn btn-flat btn-sm bg-purple"><i class="fa fa-refresh"></i> Reload</button>
                <div class="pull-right">
                    <a target="_blank" href="<?= base_url() ?>hasiltugas/cetak_detail/<?= $this->uri->segment(3) ?>" class="btn bg-maroon btn-flat btn-sm">
                        <i class="fa fa-print"></i> Print
                    </a>
                </div>
            </div>
            <div class="col-sm-6">
                <table class="table w-100">
                    <tr>
                        <th>Nama Tugas</th>
                        <td><?= $tugas->nama_tugas ?></td>
                    </tr>
                    <tr>
                        <th>Jumlah Soal</th>
                        <td><?= $tugas->jumlah_soal ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Mulai</th>
                        <td><?= strftime('%A, %d %B %Y', strtotime($tugas->tgl_mulai)) ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Selesai</th>
                        <td><?= strftime('%A, %d %B %Y', strtotime($tugas->terlambat)) ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-6">
                <table class="table w-100">
                    <tr>
                        <th>Topik</th>
                        <td><?= $tugas->nama_topik ?></td>
                    </tr>
                    <tr>
                        <th>Guru</th>
                        <td><?= $tugas->nama_guru ?></td>
                    </tr>
                    <tr>
                        <th>Nilai Terendah</th>
                        <td><?= $nilai->min_nilai ?></td>
                    </tr>
                    <tr>
                        <th>Nilai Tertinggi</th>
                        <td><?= $nilai->max_nilai ?></td>
                    </tr>
                    <tr>
                        <th>Rata-rata Nilai</th>
                        <td><?php printf('%.2f', $nilai->avg_nilai) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="table-responsive px-4 pb-3" style="border: 0">
        <table id="detail_hasil" class="w-100 table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th>Nilai</th>
                    <th class="text-center">
                        <i class="fa fa-search"></i>
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>No.</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th>Nilai</th>
                    <th class="text-center">
                        <i class="fa fa-search"></i>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script type="text/javascript">
    var id = '<?= $this->uri->segment(3) ?>';
    var jenis_soal = '<?= $tugas->jenis_soal ?>';
</script>

<script src="<?= base_url() ?>assets/dist/js/app/tugas/detail_hasil.js"></script>