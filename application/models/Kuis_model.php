<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kuis_model extends CI_Model
{

    public function create($table, $data, $batch = false)
    {
        if ($batch === false) {
            $insert = $this->db->insert($table, $data);
        } else {
            $insert = $this->db->insert_batch($table, $data);
        }
        return $insert;
    }

    public function update($table, $data, $pk, $id = null, $batch = false)
    {
        if ($batch === false) {
            $insert = $this->db->update($table, $data, array($pk => $id));
        } else {
            $insert = $this->db->update_batch($table, $data, $pk);
        }
        return $insert;
    }

    public function delete($table, $data, $pk)
    {
        $this->db->where_in($pk, $data);
        return $this->db->delete($table);
    }

    public function getDataKuis($id)
    {
        $this->datatables->select('a.id_kuis, a.token, a.nama_kuis, b.nama_mapel, c.nama_topik, a.jumlah_soal, a.jenis_soal, CONCAT(a.tgl_mulai, " <br/> (", a.waktu, " Menit)") as waktu, a.jenis');
        $this->datatables->from('kuis a');
        $this->datatables->join('mapel b', 'a.mapel_id = b.id_mapel');
        $this->datatables->join('topik c', 'c.id_topik = a.topik_id ');
        if ($id !== null) {
            $this->datatables->where('guru_id', $id);
        }
        return $this->datatables->generate();
    }

    public function getListKuis($id, $kelas)
    {
        $this->datatables->select("a.id_kuis, c.nama_guru, (select nama_kelas from kelas where id_kelas = {$kelas}) as nama_kelas, a.nama_kuis, b.nama_mapel, d.nama_topik, a.jumlah_soal, CONCAT(a.tgl_mulai, ' <br/> (', a.waktu, ' Menit)') as waktu, (SELECT COUNT(id) FROM hasil_kuis h WHERE h.siswa_id = {$id} AND h.kuis_id = a.id_kuis) AS ada");
        $this->datatables->from('kuis a');
        $this->datatables->join('mapel b', 'a.mapel_id = b.id_mapel');
        $this->datatables->join('guru c', 'a.guru_id = c.id_guru');
        $this->datatables->join('topik d', 'd.id_topik = a.topik_id');
        $this->datatables->where("a.guru_id IN (select id_guru from guru where FIND_IN_SET({$kelas}, kelas_id))", null);
        return $this->datatables->generate();
    }

    public function getKuisById($id)
    {
        $this->db->select('*');
        $this->db->from('kuis a');
        $this->db->join('guru b', 'a.guru_id=b.id_guru');
        $this->db->join('mapel c', 'a.mapel_id=c.id_mapel');
        $this->db->join('topik d', 'd.id_topik = a.topik_id');
        $this->db->where('id_kuis', $id);
        return $this->db->get()->row();
    }

    public function getIdGuru($nip)
    {
        $this->db->select('id_guru, nama_guru')->from('guru')->where('nip', $nip);
        return $this->db->get()->row();
    }

    public function getJumlahSoal($m, $t)
    {
        $this->db->select('COUNT(id_soal) as jml_soal');
        $this->db->from('soal');
        $this->db->where('mapel_id', $m);
        $this->db->where("FIND_IN_SET({$t}, topik)", null);
        return $this->db->get()->row();
    }

    public function getIdSiswa($nis)
    {
        $this->db->select('*');
        $this->db->from('siswa a');
        $this->db->join('kelas b', 'a.kelas_id=b.id_kelas');
        $this->db->join('jurusan c', 'b.jurusan_id=c.id_jurusan');
        $this->db->where('nis', $nis);
        return $this->db->get()->row();
    }

    public function HslKuis($id, $mhs)
    {
        $this->db->select('*, UNIX_TIMESTAMP(tgl_selesai) as waktu_habis');
        $this->db->from('hasil_kuis');
        $this->db->where('kuis_id', $id);
        $this->db->where('siswa_id', $mhs);
        return $this->db->get();
    }

    public function getHasilEssay($id)
    {
        $this->db->select('*');
        $this->db->from('hasil_kuis a');
        $this->db->join('siswa b', 'a.siswa_id=b.id_siswa');
        $this->db->join('kelas c', 'b.kelas_id=c.id_kelas');
        $this->db->join('kuis d', 'a.kuis_id=d.id_kuis');
        $this->db->where('id', $id);
        return $this->db->get();
    }

    function getAllJawabanByIdSoal($id_soal_essay, $id)
    {
        $this->db->select('*');
        $this->db->from('hasil_kuis a');
        $this->db->join('siswa b', 'a.siswa_id=b.id_siswa');
        $this->db->join('kelas c', 'b.kelas_id=c.id_kelas');
        $this->db->join('kuis d', 'a.kuis_id=d.id_kuis');
        $this->db->where('id_soal_essay', $id_soal_essay);
        $this->db->where_not_in('id', $id);
        return $this->db->get();
    }

    public function getSoal($id)
    {
        $kuis = $this->getKuisById($id);
        if ($kuis->jenis_soal === "pilgan") {
            $order = $kuis->jenis === "acak" ? 'rand()' : 'id_soal';

            $this->db->select('id_soal, soal, file, tipe_file, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, jawaban');
            $this->db->from('soal');
            $this->db->where('mapel_id', $kuis->mapel_id);
            $this->db->where("FIND_IN_SET({$kuis->topik_id}, topik)", null);
            $this->db->where('jenis_soal', 'pilgan');
            $this->db->order_by($order);
            $this->db->limit($kuis->jumlah_soal);
            return $this->db->get()->result();
        } else {
            $this->db->select('id_soal, soal, file, tipe_file');
            $this->db->from('soal');
            $this->db->where('mapel_id', $kuis->mapel_id);
            $this->db->where("FIND_IN_SET({$kuis->topik_id}, topik)", null);
            $this->db->where('id_soal', $kuis->id_soal_essay);
            return $this->db->get()->row();
        }
    }

    public function getSoalEssay($topik)
    {
        $this->db->select('*');
        $this->db->from('soal');
        $this->db->where("FIND_IN_SET({$topik}, topik)");
        $this->db->where('jenis_soal', 'essay');
        return $this->db->get()->result();
    }

    public function ambilSoal($pc_urut_soal1, $pc_urut_soal_arr)
    {
        $this->db->select("*, {$pc_urut_soal1} AS jawaban");
        $this->db->from('soal');
        $this->db->where('id_soal', $pc_urut_soal_arr);
        return $this->db->get()->row();
    }

    public function getJawaban($id_tes)
    {
        $this->db->select('list_jawaban');
        $this->db->from('hasil_kuis');
        $this->db->where('id', $id_tes);
        return $this->db->get()->row()->list_jawaban;
    }

    public function getHasilKuis($nip = null)
    {
        $this->datatables->select('b.id_kuis, b.nama_kuis, e.nama_topik, b.jenis_soal, b.jumlah_soal, CONCAT(b.waktu, " Menit") as waktu, b.tgl_mulai');
        $this->datatables->select('c.nama_mapel, d.nama_guru');
        $this->datatables->from('hasil_kuis a');
        $this->datatables->join('kuis b', 'a.kuis_id = b.id_kuis');
        $this->datatables->join('mapel c', 'b.mapel_id = c.id_mapel');
        $this->datatables->join('guru d', 'b.guru_id = d.id_guru');
        $this->datatables->join('topik e', 'b.topik_id = e.id_topik');
        if ($nip !== null) {
            $this->datatables->where('d.nip', $nip);
        }
        $this->datatables->group_by('b.id_kuis');
        return $this->datatables->generate();
    }

    public function HslKuisById($id, $dt = false)
    {
        if ($dt === false) {
            $db = "db";
            $get = "get";
        } else {
            $db = "datatables";
            $get = "generate";
        }

        $this->$db->select('d.id, a.nama, b.nama_kelas, d.jenis_soal, c.nama_jurusan, d.jml_benar, d.nilai');
        $this->$db->from('siswa a');
        $this->$db->join('kelas b', 'a.kelas_id=b.id_kelas');
        $this->$db->join('jurusan c', 'b.jurusan_id=c.id_jurusan');
        $this->$db->join('hasil_kuis d', 'a.id_siswa=d.siswa_id');
        $this->$db->where(['d.kuis_id' => $id]);
        return $this->$db->$get();
    }

    public function bandingNilai($id)
    {
        $this->db->select_min('nilai', 'min_nilai');
        $this->db->select_max('nilai', 'max_nilai');
        $this->db->select_avg('FORMAT(FLOOR(nilai),0)', 'avg_nilai');
        $this->db->where('kuis_id', $id);
        return $this->db->get('hasil_kuis')->row();
    }
}
