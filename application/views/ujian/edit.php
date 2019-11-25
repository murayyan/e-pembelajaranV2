<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $subjudul ?></h3>
        <div class="box-tools pull-right">
            <a href="<?= base_url() ?>ujian/master" class="btn btn-sm btn-flat btn-warning">
                <i class="fa fa-arrow-left"></i> Batal
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-4">
                <div class="alert bg-purple">
                    <h4>Mata Pelajaran <i class="fa fa-book pull-right"></i></h4>
                    <p><?= $mapel->nama_mapel ?></p>
                </div>
                <div class="alert bg-purple">
                    <h4>Guru <i class="fa fa-address-book-o pull-right"></i></h4>
                    <p><?= $guru->nama_guru ?></p>
                </div>
            </div>
            <div class="col-sm-4">
                <?= form_open('ujian/save', array('id' => 'formujian'), array('method' => 'edit', 'guru_id' => $guru->id_guru, 'mapel_id' => $mapel->mapel_id, 'id_ujian' => $ujian->id_ujian)) ?>
                <div class="form-group">
                    <label for="nama_ujian">Nama Ujian</label>
                    <input value="<?= $ujian->nama_ujian ?>" autofocus="autofocus" onfocus="this.select()" placeholder="Nama Ujian" type="text" class="form-control" name="nama_ujian">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="topik">Topik</label>
                    <select name="topik" id="topik" class="form-control select2" style="width: 100%!important">
                        <?php foreach ($topik as $row) : ?>
                            <option <?= $ujian->topik_id === $row->id_topik ? "selected" : "" ?> value="<?= $row->id_topik ?>"><?= $row->nama_topik ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="help-block"></small>
                </div>

                <div class="form-group">
                    <label for="soal" class="control-label">Jenis Soal</label>
                    <select id="jenis_soal" name="jenis_soal" class="form-control" style="width: 100%!important">
                        <option value="pilgan" <?= $ujian->jenis_soal === 'pilgan' ? "selected" : "" ?>>Pilihan Ganda</option>
                        <option value="essay" <?= $ujian->jenis_soal === 'essay' ? "selected" : "" ?>>Essay</option>
                    </select>
                    <small class="help-block" style="color: #dc3545"><?= form_error('jenis_soal') ?></small>
                </div>

                <div class="form-group">
                    <label for="tgl_mulai">Tanggal Mulai</label>
                    <input id="tgl_mulai" name="tgl_mulai" type="text" class="datetimepicker form-control" placeholder="Tanggal Mulai">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="tgl_selesai">Tanggal Selesai</label>
                    <input id="tgl_selesai" name="tgl_selesai" type="text" class="datetimepicker form-control" placeholder="Tanggal Selesai">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="waktu">Waktu</label>
                    <input value="<?= $ujian->waktu ?>" placeholder="menit" type="number" class="form-control" name="waktu">
                    <small class="help-block"></small>
                </div>

                <div id="pilgan">
                    <div class="form-group">
                        <label for="jumlah_soal">Jumlah Soal</label>
                        <input value="<?= $ujian->jumlah_soal ?>" placeholder="Jumlah Soal" type="number" class="form-control" name="jumlah_soal">
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group">
                        <label for="jenis">Acak Soal</label>
                        <select name="jenis" class="form-control">
                            <option value="" disabled selected>--- Pilih ---</option>
                            <option <?= $ujian->jenis === "acak" ? "selected" : ""; ?> value="acak">Acak Soal</option>
                            <option <?= $ujian->jenis === "urut" ? "selected" : ""; ?> value="urut">Urut Soal</option>
                        </select>
                        <small class="help-block"></small>
                    </div>
                </div>

                <div id="essay" class="form-group">
                    <label for="soal">Soal</label>
                    <div style="width: 100%; overflow: scroll; height: 300px">
                        <div id="soal">
                        <?php foreach ($soal as $soal): ?>
                            <input type="radio" name="soal" value="<?= $soal->id_soal ?>"  <?= $soal->id_soal === $ujian->id_soal_essay ? "checked" : ""; ?>><?= $soal->soal ?><div class="w-25"><?= tampil_media("uploads/bank_soal/" . $soal->file) ?> </div><br>
                        <?php endforeach; ?>
                        </div>
                    </div>
                    <small class="help-block"></small>
                </div>

                <div class="form-group pull-right">
                    <button type="reset" class="btn btn-default btn-flat">
                        <i class="fa fa-rotate-left"></i> Reset
                    </button>
                    <button id="submit" type="submit" class="btn btn-flat bg-purple"><i class="fa fa-save"></i> Simpan</button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var tgl_mulai = '<?= $ujian->tgl_mulai ?>';
    var terlambat = '<?= $ujian->terlambat ?>';
</script>
<script>
    $(document).ready(function(){
        
        if ($("#jenis_soal").val() == 'pilgan') {
            $("#pilgan").show()
            $("#essay").hide()
        } else {
            $("#pilgan").hide()
            $("#essay").show()
        }
        
        $("#jenis_soal").change(function() {
            if ($("#jenis_soal").val() == 'pilgan') {
                $("#pilgan").show()
                $("#essay").hide()
            } else {
                $("#pilgan").hide()
                $("#essay").show()
            }
        })


    });

    function getSoal()
    {
        var topik = $('#topik').val();
        // console.log(topik)

        $.get( base_url + 'ujian/getSoalByTopic', { topik: topik } )
            .done(function( result ) {
                document.getElementById('soal').innerHTML = ''
                result.forEach(function (val) {
                    document.getElementById('soal').innerHTML += '<input type="radio" name="soal" value="'+ val.id_soal +'">' + val.soal + '  <div class="w-25"><?= tampil_media("uploads/bank_soal/" . "<script>document.write(val.file)</script>") ?> </div><br>'
                })
        });
    }
</script>
<script src="<?= base_url() ?>assets/dist/js/app/ujian/edit.js"></script>